<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Components\Campaigns\Domain\Exceptions;

/**
 * Signals that the campaign slug is empty or contains only whitespace.
 *
 * @since 1.0.0
 */
final class InvalidCampaignSlugException extends CampaignDomainException {}
