<?php
/**
 * WpdbCampaignRepository class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Campaigns\Persistence;

use Fundrik\Core\Application\Campaigns\CampaignDtoFactory;
use Fundrik\Core\Domain\Campaigns\Campaign;
use Fundrik\Core\Domain\Campaigns\CampaignDto;
use Fundrik\Core\Domain\Campaigns\Interfaces\CampaignRepositoryInterface;
use Fundrik\Core\Domain\EntityId;
use Fundrik\WordPress\Infrastructure\Persistence\Interfaces\QueryExecutorInterface;

/**
 * Repository for managing campaigns in the WordPress database using wpdb.
 *
 * @since 1.0.0
 */
final readonly class WpdbCampaignRepository implements CampaignRepositoryInterface {

	private const TABLE = 'fundrik_campaigns';

	/**
	 * WpdbCampaignRepository constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param CampaignDtoFactory     $dto_factory The factory to create CampaignDto objects from database data.
	 * @param QueryExecutorInterface $query_executor The query executor interface for interacting with the database.
	 */
	public function __construct(
		private CampaignDtoFactory $dto_factory,
		private QueryExecutorInterface $query_executor
	) {
	}

	/**
	 * Get a campaign DTO by its ID.
	 *
	 * @since 1.0.0
	 *
	 * @param EntityId $id The campaign ID.
	 *
	 * @return CampaignDto|null The campaign DTO if found, or null if not found.
	 */
	public function get_by_id( EntityId $id ): ?CampaignDto {

		$data = $this->query_executor->get_by_id( self::TABLE, $id->value );

		return $data ? $this->dto_factory->from_array( $data ) : null;
	}

	/**
	 * Get all campaigns.
	 *
	 * @since 1.0.0
	 *
	 * @return CampaignDto[] An array of campaign DTOs.
	 */
	public function get_all(): array {

		$data = $this->query_executor->get_all( self::TABLE );

		return array_map(
			fn( $item ): CampaignDto => $this->dto_factory->from_array( $item ),
			$data
		);
	}

	/**
	 * Check if a campaign exists.
	 *
	 * @since 1.0.0
	 *
	 * @param Campaign $campaign The campaign entity to check.
	 *
	 * @return bool True if the campaign exists, false otherwise.
	 */
	public function exists( Campaign $campaign ): bool {

		return $this->query_executor->exists( self::TABLE, $campaign->id->value );
	}

	/**
	 * Insert a new campaign.
	 *
	 * @since 1.0.0
	 *
	 * @param Campaign $campaign The campaign entity to insert.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function insert( Campaign $campaign ): bool {

		$dto = $this->dto_factory->from_campaign( $campaign );

		return $this->query_executor->insert( self::TABLE, (array) $dto );
	}

	/**
	 * Update an existing campaign.
	 *
	 * @since 1.0.0
	 *
	 * @param Campaign $campaign The campaign entity to update.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function update( Campaign $campaign ): bool {

		$dto = $this->dto_factory->from_campaign( $campaign );

		return $this->query_executor->update(
			self::TABLE,
			(array) $dto,
			$campaign->id->value
		);
	}

	/**
	 * Delete a campaign by its ID.
	 *
	 * @since 1.0.0
	 *
	 * @param EntityId $id The ID of the campaign to delete.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function delete( EntityId $id ): bool {

		return $this->query_executor->delete( self::TABLE, $id->value );
	}
}
