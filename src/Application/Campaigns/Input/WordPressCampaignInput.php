<?php
/**
 * WordPressCampaignInput class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Base input DTO for WordPress campaign data.
 *
 * Contains common properties used both for creating and updating campaigns,
 * with validation constraints applied to required fields.
 *
 * @since 1.0.0
 */
abstract readonly class WordPressCampaignInput {

	/**
	 * Constructor for WordPressCampaignInput.
	 *
	 * @param string $title Campaign title. Must not be blank.
	 * @param string $slug Campaign slug. Must not be blank.
	 * @param bool   $is_enabled Whether the campaign is enabled.
	 * @param bool   $is_open Whether the campaign is open.
	 * @param bool   $has_target Whether the campaign has a target amount.
	 * @param int    $target_amount Target amount for the campaign. Must be zero or positive.
	 */
	public function __construct(
		#[Assert\NotBlank( message: 'Title must not be blank' )]
		public string $title,
		#[Assert\NotBlank( message: 'Slug must not be blank' )]
		public string $slug,
		public bool $is_enabled,
		public bool $is_open,
		public bool $has_target,
		#[Assert\PositiveOrZero]
		public int $target_amount,
	) {
	}
}
