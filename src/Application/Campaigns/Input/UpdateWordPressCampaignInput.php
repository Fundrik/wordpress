<?php
/**
 * UpdateWordPressCampaignInput class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents input data for updating an existing WordPress campaign.
 *
 * Extends the base WordPressCampaignInput by adding an ID property,
 * which is required and must not be blank.
 *
 * @since 1.0.0
 */
final readonly class UpdateWordPressCampaignInput extends WordPressCampaignInput {

	/**
	 * Constructor for UpdateWordPressCampaignInput.
	 *
	 * @param int|string $id The identifier of the campaign to update. Must not be blank.
	 * @param string     $title The campaign title. Must not be blank.
	 * @param string     $slug The campaign slug. Must not be blank.
	 * @param bool       $is_enabled Whether the campaign is enabled.
	 * @param bool       $is_open Whether the campaign is open.
	 * @param bool       $has_target Whether the campaign has a target amount.
	 * @param int        $target_amount The target amount for the campaign. Must be zero or positive.
	 */
	public function __construct(
		#[Assert\NotBlank( message: 'ID must not be blank' )]
		public int|string $id,
		string $title,
		string $slug,
		bool $is_enabled,
		bool $is_open,
		bool $has_target,
		int $target_amount,
	) {

		parent::__construct(
			$title,
			$slug,
			$is_enabled,
			$is_open,
			$has_target,
			$target_amount
		);
	}
}
