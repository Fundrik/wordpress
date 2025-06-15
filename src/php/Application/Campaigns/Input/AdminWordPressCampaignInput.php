<?php
/**
 * AdminWordPressCampaignInput class.
 *
 * @since 1.0.0
 */

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
final readonly class AdminWordPressCampaignInput extends AbstractAdminWordPressCampaignInput {

	/**
	 * AdminWordPressCampaignInput constructor.
	 *
	 * @param int    $id Campaign identifier provided by WordPress. Must not be blank.
	 * @param string $title Campaign title. Must not be blank.
	 * @param string $slug Campaign slug. Must not be blank.
	 * @param bool   $is_enabled Whether the campaign is enabled.
	 * @param bool   $is_open Whether the campaign is open.
	 * @param bool   $has_target Whether the campaign has a target amount.
	 * @param int    $target_amount Target amount for the campaign. Must be zero or positive.
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
