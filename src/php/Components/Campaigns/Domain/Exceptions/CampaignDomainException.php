<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Components\Campaigns\Domain\Exceptions;

use Fundrik\WordPress\Components\Shared\Domain\Exceptions\FundrikWordPressDomainException;

/**
 * Signals a WordPress-specific domain error related to campaign invariants or rules.
 *
 * @since 1.0.0
 */
abstract class CampaignDomainException extends FundrikWordPressDomainException {}
