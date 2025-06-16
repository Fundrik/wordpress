<?php
/**
 * Nspace class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Support;

/**
 * A utility class for working with namespaces.
 *
 * @since 1.0.0
 */
final readonly class Nspace {

	public const BASE = 'Fundrik\WordPress';

	/**
	 * Resolves a fully qualified class name based on a given file path.
	 *
	 * Converts a file path within the WordPress plugin directory into a
	 * namespaced class string, assuming PSR-4 autoloading conventions.
	 *
	 * For example:
	 *   '/plugin/src/php/WordPress/Infrastructure/Migrations/FooBar.php'
	 *   → 'Fundrik\WordPress\Infrastructure\Migrations\FooBar'
	 *
	 * @since 1.0.0
	 *
	 * @param string $path The absolute file path to the PHP class file.
	 *
	 * @return string|null Fully qualified class name or null if the path is invalid.
	 */
	public static function get_full_class_name_by_path( string $path ): ?string {

		$path = explode( Path::PHP_BASE, $path );
		if ( 2 !== count( $path ) ) {
			return null;
		}

		$path = str_replace(
			[ '/', '.php' ],
			[ '\\', '' ],
			$path[1],
		);

		return self::BASE . '\\' . $path;
	}
}
