<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input\Abstracts;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Abstract base class for full WordPress campaign admin input.
 *
 * Encapsulates shared validated fields such as title, slug, and enablement state.
 *
 * @since 1.0.0
 */
abstract readonly class AbstractAdminWordPressCampaignInput extends AbstractBaseAdminWordPressCampaignInput {

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
		int $id,
		// @todo Translate message.
		#[Assert\NotBlank( message: 'Title must not be blank' )]
		public string $title,
		// @todo Translate message.
		#[Assert\NotBlank( message: 'Slug must not be blank' )]
		public string $slug,
		public bool $is_enabled,
		bool $is_open,
		bool $has_target,
		int $target_amount,
	) {

		parent::__construct( $id, $is_open, $has_target, $target_amount );
	}
}
