<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Input DTO for managing full WordPress campaign data via the admin interface.
 *
 * Represents the complete set of campaign fields after WordPress has saved the post.
 * Used primarily for synchronization and validation against the full set of constraints.
 *
 * @since 1.0.0
 */
readonly class AdminWordPressCampaignInput {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id Campaign identifier.
	 * @param string $title Campaign title. Must not be blank.
	 * @param string $slug Campaign slug. Must not be blank.
	 * @param bool $is_enabled Whether the campaign is enabled.
	 * @param bool $is_open Whether the campaign is open.
	 * @param bool $has_target Whether the campaign has a target amount.
	 * @param int $target_amount Target amount for the campaign. Must be zero or positive.
	 */
	public function __construct(
		// @todo Translate message.
		#[Assert\Positive( message: 'ID must be a positive' )]
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
