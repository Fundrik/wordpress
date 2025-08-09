<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Migrations;

use RuntimeException;

/**
 * Indicates a critical failure in database upgrade logic.
 *
 * @since 1.0.0
 */
final class MigrationException extends RuntimeException {}
