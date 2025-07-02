<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Interfaces;

use Fundrik\Core\Domain\EntityId;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignInput;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignPartialInput;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaign;

/**
 * Interface for working with WordPressCampaign entities using WordPress infrastructure.
 *
 * @since 1.0.0
 */
interface WordPressCampaignServiceInterface {

	/**
	 * Get a campaign by its ID.
	 *
	 * @since 1.0.0
	 *
	 * @param EntityId $id The campaign ID.
	 *
	 * @return WordPressCampaign|null The campaign if found, or null otherwise.
	 */
	public function get_campaign_by_id( EntityId $id ): ?WordPressCampaign;

	/**
	 * Get all campaigns.
	 *
	 * @since 1.0.0
	 *
	 * @return array<WordPressCampaign> An array of campaigns.
	 */
	public function get_all_campaigns(): array;

	/**
	 * Save a campaign (create or update).
	 *
	 * @since 1.0.0
	 *
	 * @param AdminWordPressCampaignInput $input The input DTO containing campaign data.
	 *
	 * @return bool True on success, false otherwise.
	 */
	public function save_campaign( AdminWordPressCampaignInput $input ): bool;

	/**
	 * Delete a campaign by its ID.
	 *
	 * @since 1.0.0
	 *
	 * @param EntityId $id The ID of the campaign to delete.
	 *
	 * @return bool True on success, false otherwise.
	 */
	public function delete_campaign( EntityId $id ): bool;

	/**
	 * Validates the provided campaign input.
	 *
	 * Implementations are expected to throw an exception if validation fails.
	 *
	 * @since 1.0.0
	 *
	 * @param AdminWordPressCampaignInput|AdminWordPressCampaignPartialInput $input The input DTO
	 *                                                                              containing campaign data.
	 */
	public function validate_input( AdminWordPressCampaignInput|AdminWordPressCampaignPartialInput $input ): void;
}
