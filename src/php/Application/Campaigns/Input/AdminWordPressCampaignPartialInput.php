<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Input DTO for partial updates of WordPress campaign data via the admin interface.
 *
 * This class represents data received when editing campaigns in the WordPress admin.
 * WordPress only sends fields that were actually changed, so some fields may be omitted.
 *
 * @since 1.0.0
 */
readonly class AdminWordPressCampaignPartialInput {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id Campaign identifier provided by WordPress.
	 * @param bool $is_open Flag for open state.
	 * @param bool $has_target Flag for whether campaign has a target.
	 * @param int $target_amount Target amount.
	 * @param string|null $title Campaign title. Must not be blank (optional).
	 * @param string|null $slug Campaign slug. Must not be blank (optional).
	 */
	public function __construct(
		// @todo Translate message.
		#[Assert\Positive( message: 'ID must be a positive' )]
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
