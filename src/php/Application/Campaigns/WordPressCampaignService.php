<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns;

use Fundrik\Core\Domain\EntityId;
use Fundrik\WordPress\Application\Campaigns\Input\AbstractAdminWordPressCampaignInput;
use Fundrik\WordPress\Application\Campaigns\Interfaces\WordPressCampaignRepositoryInterface;
use Fundrik\WordPress\Application\Campaigns\Interfaces\WordPressCampaignServiceInterface;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaign;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaignFactory;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Service for coordinating access to WordPress-specific campaign data and behavior.
 *
 * @since 1.0.0
 */
final readonly class WordPressCampaignService implements WordPressCampaignServiceInterface {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressCampaignFactory             $factory      Factory to create WordPressCampaign entities.
	 * @param WordPressCampaignDtoFactory          $dto_factory  Factory for creating WordPressCampaignDto.
	 * @param WordPressCampaignRepositoryInterface $repository   Repository that handles WordPress campaign data access.
	 * @param ValidatorInterface                   $validator    Validator used to validate campaign input data.
	 */
	public function __construct(
		private WordPressCampaignFactory $factory,
		private WordPressCampaignDtoFactory $dto_factory,
		private WordPressCampaignRepositoryInterface $repository,
		private ValidatorInterface $validator,
	) {}

	/**
	 * Get a campaign by its ID.
	 *
	 * @since 1.0.0
	 *
	 * @param EntityId $id The campaign ID.
	 *
	 * @return WordPressCampaign|null The campaign if found, or null if not found.
	 */
	public function get_campaign_by_id( EntityId $id ): ?WordPressCampaign {

		$campaign_dto = $this->repository->get_by_id( $id );

		return $campaign_dto ? $this->factory->create( $campaign_dto ) : null;
	}

	/**
	 * Get all campaigns.
	 *
	 * @since 1.0.0
	 *
	 * @return WordPressCampaign[] An array of campaigns.
	 */
	public function get_all_campaigns(): array {

		$dto_list = $this->repository->get_all();

		return array_map(
			fn( WordPressCampaignDto $dto ): WordPressCampaign => $this->factory->create( $dto ),
			$dto_list
		);
	}

	/**
	 * Saves a campaign by either creating a new one or updating an existing one.
	 *
	 * @since 1.0.0
	 *
	 * @param AbstractAdminWordPressCampaignInput $input The admin input used to create or update the campaign.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function save_campaign( AbstractAdminWordPressCampaignInput $input ): bool {

		$this->validate_input( $input );

		$dto      = $this->dto_factory->from_input( $input );
		$campaign = $this->factory->create( $dto );

		return $this->repository->exists( $campaign )
			? $this->repository->update( $campaign )
			: $this->repository->insert( $campaign );
	}

	/**
	 * Delete a campaign by its ID.
	 *
	 * @since 1.0.0
	 *
	 * @param EntityId $id The ID of the campaign to delete.
	 *
	 * @return bool True if the campaign was successfully deleted, false otherwise.
	 */
	public function delete_campaign( EntityId $id ): bool {

		return $this->repository->delete( $id );
	}

	/**
	 * Validates the given AbstractAdminWordPressCampaignInput.
	 *
	 * Throws a ValidationFailedException if validation fails.
	 *
	 * @since 1.0.0
	 *
	 * @param AbstractAdminWordPressCampaignInput $input The input data to validate.
	 *
	 * @throws ValidationFailedException If validation constraints are violated.
	 */
	public function validate_input( AbstractAdminWordPressCampaignInput $input ): void {

		$errors = $this->validator->validate( $input );

		if ( count( $errors ) > 0 ) {
			// @todo Escaping
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new ValidationFailedException( $input, $errors );
		}
	}
}
