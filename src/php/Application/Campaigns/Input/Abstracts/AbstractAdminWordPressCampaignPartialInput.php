<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input\Abstracts;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Abstract base class for partial WordPress campaign admin input.
 *
 * Used for handling updates where only a subset of fields may be present.
 *
 * @since 1.0.0
 */
abstract readonly class AbstractAdminWordPressCampaignPartialInput extends AbstractBaseAdminWordPressCampaignInput {

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
		int $id,
		bool $is_open,
		bool $has_target,
		int $target_amount,
		// @todo Translate message.
		#[Assert\NotBlank( allowNull: true, message: 'Title must not be blank' )]
		public ?string $title = null,
		// @todo Translate message.
		#[Assert\NotBlank( allowNull: true, message: 'Slug must not be blank' )]
		public ?string $slug = null,
	) {

		parent::__construct( $id, $is_open, $has_target, $target_amount );
	}
}
