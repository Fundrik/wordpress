<?php
/**
 * WpdbWordPressCampaignRepository class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Campaigns\Persistence;

use Fundrik\Core\Domain\EntityId;
use Fundrik\WordPress\Application\Campaigns\Interfaces\WordPressCampaignRepositoryInterface;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignDto;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignDtoFactory;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaign;
use Fundrik\WordPress\Infrastructure\Persistence\Interfaces\QueryExecutorInterface;

/**
 * Repository for managing campaigns in the WordPress database using wpdb.
 *
 * @since 1.0.0
 */
final readonly class WpdbWordPressCampaignRepository implements WordPressCampaignRepositoryInterface {

	private const TABLE = 'fundrik_campaigns';

	/**
	 * WpdbCampaignRepository constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressCampaignDtoFactory $dto_factory The factory to create WordPressCampaignDto objects from database data.
	 * @param QueryExecutorInterface      $query_executor The query executor interface for interacting with the database.
	 */
	public function __construct(
		private WordPressCampaignDtoFactory $dto_factory,
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
	 * @return WordPressCampaignDto|null The campaign DTO if found, or null if not found.
	 */
	public function get_by_id( EntityId $id ): ?WordPressCampaignDto {

		$data = $this->query_executor->get_by_id( self::TABLE, $id->value );

		return $data ? $this->dto_factory->from_array( $data ) : null;
	}

	/**
	 * Get all campaigns.
	 *
	 * @since 1.0.0
	 *
	 * @return WordPressCampaignDto[] An array of campaign DTOs.
	 */
	public function get_all(): array {

		$data = $this->query_executor->get_all( self::TABLE );

		return array_map(
			fn( $item ): WordPressCampaignDto => $this->dto_factory->from_array( $item ),
			$data
		);
	}

	/**
	 * Check if a campaign exists.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressCampaign $campaign The campaign entity to check.
	 *
	 * @return bool True if the campaign exists, false otherwise.
	 */
	public function exists( WordPressCampaign $campaign ): bool {

		return $this->query_executor->exists( self::TABLE, $campaign->get_id() );
	}

	/**
	 * Check if a campaign exists by its slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The slug to check.
	 *
	 * @return bool True if the campaign exists, false otherwise.
	 */
	public function exists_by_slug( string $slug ): bool {

		return $this->query_executor->exists_by_column( self::TABLE, 'slug', $slug );
	}

	/**
	 * Insert a new campaign.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressCampaign $campaign The campaign entity to insert.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function insert( WordPressCampaign $campaign ): bool {

		$dto = $this->dto_factory->from_campaign( $campaign );

		return $this->query_executor->insert( self::TABLE, (array) $dto );
	}

	/**
	 * Update an existing campaign.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressCampaign $campaign The campaign entity to update.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function update( WordPressCampaign $campaign ): bool {

		$dto = $this->dto_factory->from_campaign( $campaign );

		return $this->query_executor->update(
			self::TABLE,
			(array) $dto,
			$campaign->get_id()
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
