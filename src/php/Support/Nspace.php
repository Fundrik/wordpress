<?php

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
	public static function resolve_class_name_by_path( string $path ): ?string {

		$path = explode( Path::PHP_BASE, $path );
		if ( 2 !== count( $path ) ) {
			return null;
		}

		$path = self::normalize_path( $path[1] );

		$path = str_replace( '.php', '', $path );

		$segments = explode( '/', $path );

		$segments = array_map(
			static fn( string $segment ): string => ucfirst( $segment ),
			$segments
		);

		$path = '\\' . implode( '\\', $segments );

		return self::BASE . $path;
	}

	/**
	 * Normalizes a file path by resolving `.` and `..` segments.
	 *
	 * Removes empty segments and processes relative path indicators (`.` and `..`)
	 * to produce a clean, normalized path. This ensures the path can be reliably
	 * used for namespace resolution or other operations that require canonical structure.
	 *
	 * For example:
	 *   'Infrastructure/../Domain/./ValueObject' → 'Domain/ValueObject'
	 *
	 * Unlike `realpath()`, this method does not require the file or directory
	 * to actually exist on the filesystem. It operates purely on the string,
	 * which makes it safe for use in autoloading logic, test environments,
	 * or code analysis where physical files may not yet be present.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path A relative file path.
	 *
	 * @return string The normalized path.
	 */
	private static function normalize_path( string $path ): string {

		$segments = [];

		foreach ( explode( '/', $path ) as $segment ) {

			if ( '' === $segment || '.' === $segment ) {
				continue;
			}

			if ( '..' === $segment ) {
				array_pop( $segments );
			} else {
				$segments[] = $segment;
			}
		}

		return implode( '/', $segments );
	}
}
