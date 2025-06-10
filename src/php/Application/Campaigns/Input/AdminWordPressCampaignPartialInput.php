<?php
/**
 * AdminWordPressCampaignPartialInput class.
 *
 * @since 1.0.0
 */

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
final readonly class AdminWordPressCampaignPartialInput extends AbstractAdminWordPressCampaignInput {

	/**
	 * AdminWordPressCampaignPartialInput constructor.
	 *
	 * @param int         $id            Campaign identifier provided by WordPress.
	 * @param bool        $is_open       Flag for open state.
	 * @param bool        $has_target    Flag for whether campaign has a target.
	 * @param int         $target_amount Target amount.
	 * @param string|null $title         Campaign title (optional).
	 * @param string|null $slug          Campaign slug (optional).
	 */
	public function __construct(
		int $id,
		bool $is_open,
		bool $has_target,
		int $target_amount,
		#[Assert\NotBlank( allowNull: true, message: 'Title must not be blank' )]
		public ?string $title = null,
		#[Assert\NotBlank( allowNull: true, message: 'Slug must not be blank' )]
		public ?string $slug = null,
	) {
		parent::__construct( $id, $is_open, $has_target, $target_amount );
	}
}
