<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Components\Campaigns\Domain\Exceptions;

/**
 * Thrown when the WordPress campaign fails domain-level validation or integrity checks.
 *
 * @since 1.0.0
 */
final class InvalidCampaignException extends CampaignDomainException {}
