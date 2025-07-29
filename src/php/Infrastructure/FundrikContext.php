<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure;

use Fundrik\WordPress\Infrastructure\Helpers\PluginPath;
use Fundrik\WordPress\Infrastructure\WordPress\PostTypes\CampaignPostType;

/**
 * Provides Fundrik-specific plugin context.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class FundrikContext {

	/**
	 * Returns the list of declared post type class names.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string> The list of post type class names.
	 *
	 * @phpstan-return list<class-string<\Fundrik\WordPress\Infrastructure\WordPress\PostTypes\PostTypeInterface>>
	 */
	public function get_post_types(): array {

		return [
			CampaignPostType::class,
		];
	}

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
