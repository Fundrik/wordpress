<?php
/**
 * CreateWordPressCampaignInput class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input;

/**
 * Represents input data for creating a new WordPress campaign.
 *
 * This class extends WordPressCampaignInput without adding any additional properties,
 * and is used to clearly distinguish creation intent in the application.
 *
 * @since 1.0.0
 */
final readonly class CreateWordPressCampaignInput extends WordPressCampaignInput {}
