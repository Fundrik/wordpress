<?php
/**
 * Path enum.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Support;

/**
 * Represents various paths used within the Fundrik plugin.
 *
 * @since 1.0.0
 */
enum Path: string {

	public const BASE     = FUNDRIK_PATH;
	public const PHP_BASE = self::BASE . 'src/php/';

	case Blocks         = 'assets/js/blocks/';
	case BlocksManifest = self::Blocks->value . 'blocks-manifest.php';

	case MigrationFiles = 'Infrastructure/Migrations/Files/';

	/**
	 * Returns the full filesystem path by prepending the plugin base directory.
	 *
	 * @param string $suffix Optional suffix to append to the path (e.g. filename or subdirectory).
	 *
	 * @return string Full path including plugin base directory.
	 */
	public function get_full_path( string $suffix = '' ): string {

		$base   = str_starts_with( $this->value, 'assets' ) ? self::BASE : self::PHP_BASE;
		$target = $this->value;

		$path = "{$base}{$target}";

		if ( $suffix ) {
			$path .= $suffix;
		}

		return $path;
	}
}
