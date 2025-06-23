<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Domain\Campaigns;

use Fundrik\Core\Domain\Campaigns\Campaign;

/**
 * Represents a WordPress-specific campaign entity.
 *
 * This class wraps the core Campaign entity and adds platform-specific
 * attributes and behaviors.
 *
 * @since 1.0.0
 */
final readonly class WordPressCampaign {

	/**
	 * Constructor.
	 *
	 * Initializes a WordPressCampaign by wrapping a core Campaign entity and
	 * adding WordPress-specific data.
	 *
	 * @since 1.0.0
	 *
	 * @param Campaign $core_campaign Core campaign entity.
	 * @param WordPressCampaignSlug $slug Campaign slug.
	 */
	public function __construct(
		private Campaign $core_campaign,
		private WordPressCampaignSlug $slug,
	) {}

	/**
	 * Get campaign id.
	 *
	 * @since 1.0.0
	 *
	 * @return int|string Campaign id.
	 */
	public function get_id(): int|string {

		return $this->core_campaign->get_id();
	}

	/**
	 * Get campaign title.
	 *
	 * @since 1.0.0
	 *
	 * @return int|string Campaign title.
	 */
	public function get_title(): string {

		return $this->core_campaign->get_title();
	}

	/**
	 * Check if campaign is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if enabled, false otherwise.
	 */
	public function is_enabled(): bool {

		return $this->core_campaign->is_enabled();
	}

	/**
	 * Check if campaign is open.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if open, false otherwise.
	 */
	public function is_open(): bool {

		return $this->core_campaign->is_open();
	}

	/**
	 * Check if target is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if target is enabled.
	 */
	public function has_target(): bool {

		return $this->core_campaign->has_target();
	}

	/**
	 * Get target amount for campaign.
	 *
	 * @since 1.0.0
	 *
	 * @return int Target amount.
	 */
	public function get_target_amount(): int {

		return $this->core_campaign->get_target_amount();
	}

	/**
	 * Get campaign slug.
	 *
	 * @since 1.0.0
	 *
	 * @return string Campaign slug.
	 */
	public function get_slug(): string {

		return $this->slug->value;
	}
}
