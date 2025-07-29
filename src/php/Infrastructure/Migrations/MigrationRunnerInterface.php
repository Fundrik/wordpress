<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Migrations;

/**
 * Provides methods for running database migrations.
 *
 * @since 1.0.0
 *
 * @internal
 */
interface MigrationRunnerInterface {

	/**
	 * Applies all pending migrations.
	 *
	 * @since 1.0.0
	 */
	public function migrate(): void;
}
