<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Exceptions;

use Fundrik\WordPress\Application\Exceptions\FundrikWordPressApplicationException;

/**
 * Base exception for all WordPress campaign application errors.
 *
 * This class specializes WordPress application-level exceptions to those
 * specific to the Campaigns context, such as invalid campaign data,
 * synchronization issues, or platform mapping errors.
 *
 * @since 1.0.0
 */
abstract class WordPressCampaignApplicationException extends FundrikWordPressApplicationException {}
