<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Helpers;

/**
 * Formats structured log contexts for common plugin operations.
 *
 * @since 1.0.0
 */
final readonly class LoggerFormatter {

	/**
	 * Returns the structured context for a hook mapper-related log message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook The name of the WordPress hook being mapped.
	 * @param string $mapper The fully qualified class name of the mapper.
	 *
	 * @return array<string, string> The context array for structured logging.
	 */
	public static function hook_mapper_context( string $hook, string $mapper ): array {

		return [
			'wordpress_hook_name' => $hook,
			'hook_mapper_class' => $mapper,
		];
	}

	/**
	 * Returns the structured context for a migration-related log message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $class_name The fully qualified class name of the migration.
	 * @param string $version The version string assigned to the migration.
	 *
	 * @return array<string, string> The context array for structured logging.
	 */
	public static function migration_context( string $class_name, string $version ): array {

		return [
			'migration_class' => $class_name,
			'migration_version' => $version,
		];
	}
}
