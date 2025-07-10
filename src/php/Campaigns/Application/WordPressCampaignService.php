<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Campaigns\Application;

use Fundrik\Core\Domain\EntityId;
use Fundrik\WordPress\Campaigns\Application\Dto\WordPressCampaignDto;
use Fundrik\WordPress\Campaigns\Application\Dto\WordPressCampaignDtoFactory;
use Fundrik\WordPress\Campaigns\Application\Input\AdminWordPressCampaignInput;
use Fundrik\WordPress\Campaigns\Application\Input\AdminWordPressCampaignPartialInput;
use Fundrik\WordPress\Campaigns\Application\Interfaces\WordPressCampaignServiceInterface;
use Fundrik\WordPress\Campaigns\Domain\WordPressCampaign;
use Fundrik\WordPress\Campaigns\Domain\WordPressCampaignFactory;
use Fundrik\WordPress\Campaigns\Infrastructure\Interfaces\WordPressCampaignRepositoryInterface;
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
	 * @param WordPressCampaignFactory $factory Creates domain entities from campaign DTOs.
	 * @param WordPressCampaignDtoFactory $dto_factory Maps admin input into internal campaign DTOs.
	 * @param WordPressCampaignRepositoryInterface $repository Persists and retrieves campaign DTOs.
	 * @param ValidatorInterface $validator Validates admin-facing input before save or update.
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

		return $campaign_dto !== null ? $this->factory->create( $campaign_dto ) : null;
	}

	/**
	 * Get all campaigns.
	 *
	 * @since 1.0.0
	 *
	 * @return array<WordPressCampaign> An array of campaigns.
	 */
	public function get_all_campaigns(): array {

		$dto_list = $this->repository->get_all();

		return array_map(
			fn ( WordPressCampaignDto $dto ): WordPressCampaign => $this->factory->create( $dto ),
			$dto_list,
		);
	}

	/**
	 * Save a campaign (create or update).
	 *
	 * @since 1.0.0
	 *
	 * @param AdminWordPressCampaignInput $input The input DTO containing campaign data.
	 *
	 * @return bool True on success, false otherwise.
	 */
	public function save_campaign( AdminWordPressCampaignInput $input ): bool {

		$this->validate_input( $input );

		$dto = $this->dto_factory->from_input( $input );
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
	 * @return bool True on success, false otherwise.
	 */
	public function delete_campaign( EntityId $id ): bool {

		return $this->repository->delete( $id );
	}

	/**
	 * Validates the provided campaign input.
	 *
	 * @since 1.0.0
	 *
	 * @param AdminWordPressCampaignInput|AdminWordPressCampaignPartialInput $input The input DTO
	 *                                                                              containing campaign data.
	 */
	public function validate_input( AdminWordPressCampaignInput|AdminWordPressCampaignPartialInput $input ): void {

		$errors = $this->validator->validate( $input );

		if ( count( $errors ) > 0 ) {
			throw new ValidationFailedException( $input, $errors );
		}
	}
}
