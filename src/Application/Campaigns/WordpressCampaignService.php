<?php
/**
 * Provides the application-level service for managing campaign retrieval.
 *
 * Acts as a bridge between the core logic and WordPress-specific repositories,
 * coordinating access and transformation of campaign data.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns;

use Fundrik\Core\Domain\EntityId;
use Fundrik\WordPress\Application\Campaigns\Interfaces\WordPressCampaignRepositoryInterface;
use Fundrik\WordPress\Application\Campaigns\Interfaces\WordPressCampaignServiceInterface;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaign;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaignFactory;

/**
 * Application service for coordinating access to WordPress-specific campaign data and behavior.
 *
 * @since 1.0.0
 */
final readonly class WordPressCampaignService implements WordPressCampaignServiceInterface {

	/**
	 * WordPressCampaignService constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressCampaignFactory             $factory Factory to create WordPressCampaign objects from full DTOs.
	 * @param WordPressCampaignRepositoryInterface $repository Repository that handles data access to campaigns including WordPress-specific fields.
	 */
	public function __construct(
		private WordPressCampaignFactory $factory,
		private WordPressCampaignRepositoryInterface $repository
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
	 * Save a campaign (create or update).
	 *
	 * @param WordPressCampaignDto $dto The campaign DTO to save.
	 */
	public function save_campaign( WordPressCampaignDto $dto ): bool {

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
}
