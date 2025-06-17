<?php
/**
 * MigrationValidationResult class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Migrations;

/**
 * Represents the result of validating a migration file.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class MigrationValidationResult {

	/**
	 * MigrationValidationResult constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $version Migration version.
	 * @param string $class_name Fully qualified migration class name.
	 */
	public function __construct(
		public string $version,
		public string $class_name,
	) {
	}
}
