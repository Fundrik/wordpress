<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Campaigns\Application\Input;

use Fundrik\WordPress\Campaigns\Application\Validation\CampaignTargetConstraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Input DTO for partial updates of WordPress campaign data via the admin interface.
 *
 * This class represents data received when editing campaigns in the WordPress admin.
 * WordPress only sends fields that were actually changed, so some fields may be omitted.
 *
 * @since 1.0.0
 */
#[CampaignTargetConstraint]
readonly class AdminWordPressCampaignPartialInput {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id The campaign ID. Must be a positive integer.
	 * @param bool $is_open Whether the campaign is open.
	 * @param bool $has_target Whether the campaign has a target amount.
	 * @param int $target_amount The campaign target amount.
	 * @param string|null $title The campaign title. Must not be blank (optional).
	 * @param string|null $slug The campaign slug. Must not be blank (optional).
	 *
	 * If $has_target is true, then $target_amount must be greater than zero.
	 * If $has_target is false, then $target_amount must be exactly zero.
	 */
	public function __construct(
		// @todo Translate message.
		#[Assert\Positive( message: 'ID must be a positive integer' )]
		public int $id,
		public bool $is_open,
		public bool $has_target,
		public int $target_amount,
		// @todo Translate message.
		#[Assert\NotBlank( allowNull: true, message: 'Title must not be blank' )]
		public ?string $title = null,
		// @todo Translate message.
		#[Assert\NotBlank( allowNull: true, message: 'Slug must not be blank' )]
		public ?string $slug = null,
	) {}
}
