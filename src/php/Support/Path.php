<?php
/**
 * Path enum.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Support;

/**
 * Enum representing various paths used within the Fundrik plugin.
 *
 * @since 1.0.0
 */
enum Path: string {

	const BASE = FUNDRIK_PATH;

	case Blocks         = 'assets/js/blocks/';
	case BlocksManifest = self::Blocks->value . 'blocks-manifest.php';

	case Migrations = 'src/Infrastructure/Migrations/';

	/**
	 * Returns the full filesystem path by prepending the plugin base directory.
	 *
	 * @return string Full path including plugin base directory.
	 */
	public function get(): string {

		$base   = self::BASE;
		$target = $this->value;

		return "{$base}/{$target}";
	}
}
