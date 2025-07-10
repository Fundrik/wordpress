<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Shared\Application\Exceptions;

use RuntimeException;

/**
 * Base exception for all WordPress Application-level errors in Fundrik.
 *
 * This abstract class serves as the root for any exceptions that occur
 * within the WordPress-specific Application layer, including input factories,
 * mappers, or integration logic with WordPress APIs.
 *
 * @since 1.0.0
 */
abstract class FundrikWordPressApplicationException extends RuntimeException {}
