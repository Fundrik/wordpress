<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Campaigns\Application\Input\Exceptions;

/**
 * Base exception for admin campaign input errors.
 *
 * Thrown when the admin-facing input DTOs encounter structural or semantic inconsistencies
 * during construction or transformation.
 *
 * @since 1.0.0
 */
abstract class AdminWordPressCampaignInputException extends WordPressCampaignInputException {}
