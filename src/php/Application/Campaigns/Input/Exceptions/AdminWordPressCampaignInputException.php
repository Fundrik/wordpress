<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input\Exceptions;

/**
 * Base exception for admin campaign input errors in the WordPress layer.
 *
 * Thrown when the admin-facing input DTOs (such as AdminWordPressCampaignInput
 * or its partial version) encounter structural or semantic inconsistencies
 * during construction or transformation.
 *
 * @since 1.0.0
 */
abstract class AdminWordPressCampaignInputException extends WordPressCampaignInputException {}
