<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Components\Campaigns\Application\Exceptions;

use Fundrik\WordPress\Components\Shared\Application\Exceptions\FundrikWordPressApplicationException;

/**
 * Serves as the base exception for WordPress campaign application-layer errors.
 *
 * @since 1.0.0
 */
abstract class CampaignApplicationException extends FundrikWordPressApplicationException {}
