<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure;

use Fundrik\WordPress\Infrastructure\Helpers\PluginPath;

/**
 * Provides Fundrik-specific plugin context.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class FundrikContext {

	/**
	 * Returns the path to the custom Gutenberg blocks directory.
	 *
	 * @since 1.0.0
	 *
	 * @return string The absolute path to the block source directory.
	 */
	public function get_blocks_path(): string {

		return PluginPath::Blocks->get_full_path();
	}

	/**
	 * Returns the path to the block manifest file.
	 *
	 * @since 1.0.0
	 *
	 * @return string The absolute path to the PHP block manifest file.
	 */
	public function get_blocks_manifest_path(): string {

		return PluginPath::BlocksManifest->get_full_path();
	}
}
