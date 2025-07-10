<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Shared\Infrastructure\WordPress;

use WP_Block_Type_Registry;

/**
 * Provides WordPress-specific context for the Fundrik plugin.
 *
 * @since 1.0.0
 */
readonly class WordPressContext {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param FundrikContext $plugin The plugin-specific context.
	 */
	public function __construct(
		public FundrikContext $plugin,
	) {}

	/**
	 * Retrieves the list of post type instances registered in WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @return array<WP_Post_Type> The array of registered post type objects keyed by post type slug.
	 */
	public function get_post_types(): array {

		return get_post_types( output: 'objects' );
	}

	/**
	 * Retrieves the list of block type instances registered in WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @return array<WP_Block_Type> The array of registered block type objects keyed by block name.
	 */
	public function get_block_types(): array {

		return WP_Block_Type_Registry::get_instance()->get_all_registered();
	}
}
