<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Components\Shared\Application\Exceptions;

use RuntimeException;

/**
 * Signals a failure in the WordPress application layer.
 *
 * @since 1.0.0
 */
abstract class FundrikWordPressApplicationException extends RuntimeException {}
