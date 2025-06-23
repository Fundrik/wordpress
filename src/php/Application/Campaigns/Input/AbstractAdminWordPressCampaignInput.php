<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input;

use Fundrik\WordPress\Application\Campaigns\Validation\CampaignTargetConstraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Base input DTO for WordPress campaign data used in the admin interface.
 *
 * This abstract class defines shared fields: ID, target info and open flag.
 *
 * @since 1.0.0
 *
 * @codeCoverageIgnore
 *
 * @todo Remove codeCoverageIgnore after migrating to PHP 8.3+
 */
#[CampaignTargetConstraint]
abstract readonly class AbstractAdminWordPressCampaignInput {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id Campaign identifier.
	 * @param bool $is_open Whether the campaign is open.
	 * @param bool $has_target Whether the campaign has a target.
	 * @param int $target_amount Target amount.
	 */
	public function __construct(
		// @todo Translate message.
		#[Assert\Positive( message: 'ID must be a positive' )]
		public int $id,
		public bool $is_open,
		public bool $has_target,
		public int $target_amount,
	) {
	}
}
