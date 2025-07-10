<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Shared\Infrastructure\Migrations;

/**
 * Represents a reference to a single migration class, including its version.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class MigrationReference {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $version Migration version (e.g., "2025_06_16_01").
	 * @param string $class_name Fully qualified migration class name.
	 */
	public function __construct(
		public string $version,
		public string $class_name,
	) {
	}
}
