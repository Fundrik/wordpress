<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input;

use Fundrik\WordPress\Application\Campaigns\Input\Abstracts\AbstractAdminWordPressCampaignInput;

// phpcs:disable SlevomatCodingStandard.Classes.EmptyLinesAroundClassBraces.IncorrectEmptyLinesBeforeClosingBrace, SlevomatCodingStandard.Classes.EmptyLinesAroundClassBraces.MultipleEmptyLinesAfterOpeningBrace
/**
 * Input DTO for managing full WordPress campaign data via the admin interface.
 *
 * Represents the complete set of campaign fields after WordPress has saved the post.
 * Used primarily for synchronization and validation against the full set of constraints.
 *
 * @since 1.0.0
 */
final readonly class AdminWordPressCampaignInput extends AbstractAdminWordPressCampaignInput {}
// phpcs:enable SlevomatCodingStandard.Classes.EmptyLinesAroundClassBraces.IncorrectEmptyLinesBeforeClosingBrace, SlevomatCodingStandard.Classes.EmptyLinesAroundClassBraces.MultipleEmptyLinesAfterOpeningBrace