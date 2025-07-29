<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Components\Shared\Domain\Exceptions;

use DomainException;

/**
 * Signals a failure in the WordPress domain layer.
 *
 * @since 1.0.0
 */
abstract class FundrikWordPressDomainException extends DomainException {}
