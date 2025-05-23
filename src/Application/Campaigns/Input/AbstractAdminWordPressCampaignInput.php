<?php
/**
 * AbstractAdminWordPressCampaignInput class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Base input DTO for WordPress campaign data used in the admin interface.
 *
 * This abstract class defines the minimal required property — the campaign ID.
 *
 * @since 1.0.0
 */
abstract readonly class AbstractAdminWordPressCampaignInput {

	/**
	 * Constructor for AbstractAdminWordPressCampaignInput.
	 *
	 * @param int $id Campaign identifier provided by WordPress. Must not be blank.
	 */
	public function __construct(
		#[Assert\NotBlank( message: 'ID must not be blank' )]
		public int $id,
	) {
	}
}
