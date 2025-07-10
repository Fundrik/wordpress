<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Campaigns\Application\Exceptions;

use Fundrik\WordPress\Shared\Application\Exceptions\FundrikWordPressApplicationException;

/**
 * Base exception for all WordPress campaign application errors.
 *
 * @since 1.0.0
 */
abstract class WordPressCampaignApplicationException extends FundrikWordPressApplicationException {}
