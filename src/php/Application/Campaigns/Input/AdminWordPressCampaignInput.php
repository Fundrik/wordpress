<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input;

use Fundrik\WordPress\Application\Campaigns\Validation\CampaignTargetConstraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Input DTO for managing full WordPress campaign data via the admin interface.
 *
 * Represents the complete set of campaign fields after WordPress has saved the post.
 * Used for validation against the full set of constraints.
 *
 * @since 1.0.0
 */
#[CampaignTargetConstraint]
readonly class AdminWordPressCampaignInput {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id The campaign ID. Must be a positive integer.
	 * @param string $title The campaign title. Must not be blank.
	 * @param string $slug The campaign slug. Must not be blank.
	 * @param bool $is_enabled Whether the campaign is enabled.
	 * @param bool $is_open Whether the campaign is open.
	 * @param bool $has_target Whether the campaign has a target amount.
	 * @param int $target_amount The campaign target amount.
	 *
	 * If $has_target is true, then $target_amount must be greater than zero.
	 * If $has_target is false, then $target_amount must be exactly zero.
	 */
	public function __construct(
		// @todo Translate message.
		#[Assert\Positive( message: 'ID must be a positive integer' )]
		public int $id,
		// @todo Translate message.
		#[Assert\NotBlank( message: 'Title must not be blank' )]
		public string $title,
		// @todo Translate message.
		#[Assert\NotBlank( message: 'Slug must not be blank' )]
		public string $slug,
		public bool $is_enabled,
		public bool $is_open,
		public bool $has_target,
		public int $target_amount,
	) {}
}
