<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Interfaces;

use Fundrik\Core\Domain\EntityId;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignDto;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaign;

/**
 * Interface for the WordPress-specific campaign repository.
 *
 * @since 1.0.0
 */
interface WordPressCampaignRepositoryInterface {

	/**
	 * Get a campaign by its ID.
	 *
	 * @since 1.0.0
	 *
	 * @param EntityId $id The campaign ID.
	 *
	 * @return WordPressCampaignDto|null The campaign DTO if found, or null otherwise.
	 */
	public function get_by_id( EntityId $id ): ?WordPressCampaignDto;

	/**
	 * Get all campaigns.
	 *
	 * @since 1.0.0
	 *
	 * @return array<WordPressCampaignDto> An array of campaign DTOs.
	 */
	public function get_all(): array;

	/**
	 * Check if a campaign exists.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressCampaign $campaign The campaign entity to check.
	 *
	 * @return bool True if the campaign exists, false otherwise.
	 */
	public function exists( WordPressCampaign $campaign ): bool;

	/**
	 * Check if a campaign exists by its slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The slug to check.
	 *
	 * @return bool True if the campaign exists, false otherwise.
	 */
	public function exists_by_slug( string $slug ): bool;

	/**
	 * Insert a new campaign.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressCampaign $campaign The campaign entity to insert.
	 *
	 * @return bool True on success, false otherwise.
	 */
	public function insert( WordPressCampaign $campaign ): bool;

	/**
	 * Update an existing campaign.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressCampaign $campaign The campaign entity to update.
	 *
	 * @return bool True on success, false otherwise.
	 */
	public function update( WordPressCampaign $campaign ): bool;

	/**
	 * Delete a campaign by its ID.
	 *
	 * @since 1.0.0
	 *
	 * @param EntityId $id The ID of the campaign to delete.
	 *
	 * @return bool True on success, false otherwise.
	 */
	public function delete( EntityId $id ): bool;
}
