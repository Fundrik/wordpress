<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Helpers;

/**
 * Enumerates filesystem paths used by the Fundrik plugin.
 *
 * @since 1.0.0
 */
enum PluginPath: string {

	/**
	 * The base directory of the plugin.
	 */
	public const BASE = FUNDRIK_PATH;

	/**
	 * The base directory for PHP source files.
	 */
	public const PHP_BASE = self::BASE . 'src/php/';

	/**
	 * The directory containing custom Gutenberg blocks.
	 */
	case Blocks = 'assets/js/blocks/';

	/**
	 * The PHP manifest file describing available blocks.
	 */
	case BlocksManifest = 'assets/js/blocks/blocks-manifest.php';

	/**
	 * Resolves the absolute filesystem path to this plugin resource.
	 *
	 * @since 1.0.0
	 *
	 * @param string $suffix Optional suffix to append.
	 *
	 * @return string The full absolute path to the plugin resource.
	 */
	public function get_full_path( string $suffix = '' ): string {

		$base = str_starts_with( $this->value, 'assets' ) ? self::BASE : self::PHP_BASE;
		$target = $this->value;

		$path = "{$base}{$target}";

		if ( $suffix !== '' ) {
			$path .= $suffix;
		}

		return $path;
	}
}
