<?php
/**
 * Path class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Support;

/**
 * A utility class for resolving paths within the Fundrik plugin.
 *
 * @since 1.0.0
 */
final readonly class Path {

	/**
	 * Returns the path to the blocks directory.
	 *
	 * @since 1.0.0
	 *
	 * @return string The absolute path to the blocks directory.
	 */
	public static function blocks(): string {

		return FUNDRIK_PATH . 'assets/js/blocks/';
	}

	/**
	 * Returns the path to the blocks manifest file.
	 *
	 * @since 1.0.0
	 *
	 * @return string The absolute path to the blocks manifest file.
	 */
	public static function blocks_manifest(): string {

		return self::blocks() . 'blocks-manifest.php';
	}
}
