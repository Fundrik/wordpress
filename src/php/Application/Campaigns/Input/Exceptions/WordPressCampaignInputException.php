<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input\Exceptions;

use Fundrik\WordPress\Application\Campaigns\Exceptions\WordPressCampaignApplicationException;

/**
 * Base exception for input-related errors in the WordPress Campaigns module.
 *
 * This class represents exceptions thrown when processing, validating, or
 * transforming input data related to campaign entities in the admin interface.
 *
 * @since 1.0.0
 */
abstract class WordPressCampaignInputException extends WordPressCampaignApplicationException {}
