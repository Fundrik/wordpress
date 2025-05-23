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
 * WordPress only sends fields that were actually changed, so all fields except the ID are optional.
 *
 * @since 1.0.0
 */
final readonly class AdminWordPressCampaignPartialInput extends AbstractAdminWordPressCampaignInput {

	/**
	 * AdminWordPressCampaignPartialInput constructor.
	 *
	 * @param int         $id            Campaign identifier provided by WordPress.
	 * @param string|null $title         Optional campaign title.
	 * @param string|null $slug          Optional campaign slug.
	 * @param bool|null   $is_enabled    Optional flag for enabled state.
	 * @param bool|null   $is_open       Optional flag for open state.
	 * @param bool|null   $has_target    Optional flag for whether campaign has a target.
	 * @param int|null    $target_amount Optional target amount.
	 */
	public function __construct(
		int $id,
		#[Assert\NotBlank( allowNull: true, message: 'Title must not be blank' )]
		public ?string $title = null,
		public ?string $slug = null,
		public ?bool $is_enabled = null,
		public ?bool $is_open = null,
		public ?bool $has_target = null,
		#[Assert\PositiveOrZero( message: 'Target amount must be zero or positive' )]
		public ?int $target_amount = null,
	) {
		parent::__construct( $id );
	}
}
