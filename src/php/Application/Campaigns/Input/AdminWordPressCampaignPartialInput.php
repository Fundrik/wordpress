<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input;

use Fundrik\WordPress\Application\Campaigns\Input\Abstracts\AbstractAdminWordPressCampaignPartialInput;

// phpcs:disable SlevomatCodingStandard.Classes.EmptyLinesAroundClassBraces.IncorrectEmptyLinesBeforeClosingBrace, SlevomatCodingStandard.Classes.EmptyLinesAroundClassBraces.MultipleEmptyLinesAfterOpeningBrace
/**
 * Input DTO for partial updates of WordPress campaign data via the admin interface.
 *
 * This class represents data received when editing campaigns in the WordPress admin.
 * WordPress only sends fields that were actually changed, so some fields may be omitted.
 *
 * @since 1.0.0
 */
final readonly class AdminWordPressCampaignPartialInput extends AbstractAdminWordPressCampaignPartialInput {}
// phpcs:enable SlevomatCodingStandard.Classes.EmptyLinesAroundClassBraces.IncorrectEmptyLinesBeforeClosingBrace, SlevomatCodingStandard.Classes.EmptyLinesAroundClassBraces.MultipleEmptyLinesAfterOpeningBrace