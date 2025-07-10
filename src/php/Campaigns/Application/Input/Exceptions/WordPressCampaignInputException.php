<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Campaigns\Application\Input\Exceptions;

use Fundrik\WordPress\Campaigns\Application\Exceptions\WordPressCampaignApplicationException;

/**
 * Base exception for input-related errors in the WordPress Campaigns module.
 *
 * This class represents exceptions thrown when processing, validating, or
 * transforming input data related to campaign entities in the admin interface.
 *
 * @since 1.0.0
 */
abstract class WordPressCampaignInputException extends WordPressCampaignApplicationException {}
