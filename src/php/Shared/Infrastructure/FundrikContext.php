<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Shared\Infrastructure;

use Fundrik\WordPress\Campaigns\Infrastructure\WordPress\WordPressCampaignPostType;
use Fundrik\WordPress\Shared\Infrastructure\WordPress\Interfaces\PostTypeInterface;
use Fundrik\WordPress\Support\Path;

/**
 * Provides Fundrik-specific plugin context.
 *
 * @since 1.0.0
 */
readonly class FundrikContext {

	/**
	 * Returns the list of Fundrik-defined post type instances.
	 *
	 * @since 1.0.0
	 *
	 * @return array<PostTypeInterface> An array of post type instances that Fundrik handles.
	 */
	public function get_post_types(): array {

		/**
		 * Filters the list of Fundrik post type instances.
		 *
		 * Allows to add additional post types that Fundrik should handle.
		 *
		 * @since 1.0.0
		 *
		 * @param array<PostTypeInterface> $post_types An array of post type instances.
		 */
		return apply_filters(
			'fundrik_post_types',
			array_map(
				static fn ( string $class_name ): PostTypeInterface => fundrik()->get( $class_name ),
				[
					WordPressCampaignPostType::class,
				],
			),
		);
	}

	/**
	 * Returns the path to the blocks directory used for block registration.
	 *
	 * @since 1.0.0
	 *
	 * @return string The absolute filesystem path to the blocks directory.
	 */
	public function get_blocks_path(): string {

		return Path::Blocks->get_full_path();
	}

	/**
	 * Returns the path to the block manifest file used for block registration.
	 *
	 * @since 1.0.0
	 *
	 * @return string The absolute filesystem path to the blocks manifest PHP file.
	 */
	public function get_blocks_manifest_path(): string {

		return Path::BlocksManifest->get_full_path();
	}
}
