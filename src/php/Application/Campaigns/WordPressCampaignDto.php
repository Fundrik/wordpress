<?php
/**
 * Data Transfer Object for WordPress-specific campaign data.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns;

/**
 * Represents the full state of a campaign entity within the WordPress implementation.
 *
 * @since 1.0.0
 */
final readonly class WordPressCampaignDto {

	/**
	 * WordPressCampaignDto constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $id            The campaign ID (integer or UUID).
	 * @param string     $title         The title of the campaign.
	 * @param string     $slug          URL-friendly slug used in WordPress permalinks.
	 * @param bool       $is_enabled    Whether the campaign is currently enabled (visible and accessible).
	 * @param bool       $is_open       Whether the campaign is currently open.
	 * @param bool       $has_target    Whether the campaign has a target goal.
	 * @param int        $target_amount The target amount (if any) for the campaign.
	 */
	public function __construct(
		public int|string $id,
		public string $title,
		public string $slug,
		public bool $is_enabled,
		public bool $is_open,
		public bool $has_target,
		public int $target_amount,
	) {
	}
}
