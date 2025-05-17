<?php
/**
 * Defines interface for working with WordPressCampaign entities using WordPress infrastructure.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Interfaces;

use Fundrik\Core\Domain\EntityId;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignDto;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaign;

interface WordPressCampaignServiceInterface {

	/**
	 * Get a campaign by its ID.
	 *
	 * @since 1.0.0
	 *
	 * @param EntityId $id The campaign ID.
	 *
	 * @return WordPressCampaign|null The campaign if found, or null if not found.
	 */
	public function get_campaign_by_id( EntityId $id ): ?WordPressCampaign;

	/**
	 * Get all campaigns.
	 *
	 * @since 1.0.0
	 *
	 * @return WordPressCampaign[] An array of campaigns.
	 */
	public function get_all_campaigns(): array;

	/**
	 * Save a campaign (create or update).
	 *
	 * @param WordPressCampaignDto $dto The campaign DTO to save.
	 */
	public function save_campaign( WordPressCampaignDto $dto ): bool;

	/**
	 * Delete a campaign by its ID.
	 *
	 * @since 1.0.0
	 *
	 * @param EntityId $id The ID of the campaign to delete.
	 *
	 * @return bool True if the campaign was successfully deleted, false otherwise.
	 */
	public function delete_campaign( EntityId $id ): bool;
}
