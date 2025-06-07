<?php
/**
 * AllowedBlockTypesFilter class.
 *
 * @since 1.0.0
 */

namespace Fundrik\WordPress\Infrastructure\Platform;

use WP_Block_Editor_Context;
use WP_Block_Type_Registry;

/**
 * Ensures only blocks registered for a specific post type are allowed.
 *
 * @since 1.0.0
 */
final readonly class AllowedBlockTypesFilter {

	/**
	 * Filters allowed block types by current post type.
	 *
	 * @param bool|array              $allowed_blocks True or list of block names currently allowed.
	 * @param WP_Block_Editor_Context $editor_context Provides info about current editor state.
	 * @param PostTypeInterface[]     $post_types List of post type objects.
	 *
	 * @return array<int, string> Filtered list of allowed block names for the current post type.
	 */
	public function filter(
		bool|array $allowed_blocks,
		WP_Block_Editor_Context $editor_context,
		array $post_types
	): array {

		if ( false === $allowed_blocks ) {
			return [];
		}

		$current_post_type = $editor_context->post->post_type;

		$block_allowed_post_types = $this->build_block_allowed_post_types_map( $post_types );

		if ( true === $allowed_blocks ) {
			$allowed_blocks = $this->get_all_registered_block_names();
		}

		$filtered = array_filter(
			$allowed_blocks,
			fn( string $block_name ): bool => $this->is_block_allowed(
				$block_name,
				$current_post_type,
				$block_allowed_post_types
			)
		);

		return array_values( $filtered );
	}

	/**
	 * Builds map block_name => [allowed_post_types].
	 *
	 * @param PostTypeInterface[] $post_types List of post type objects.
	 *
	 * @return array<string, string[]> Map where keys are block names,
	 *                                 and values are arrays of allowed post type names.
	 */
	private function build_block_allowed_post_types_map( array $post_types ): array {

		$map = [];

		foreach ( $post_types as $post_type ) {
			foreach ( $post_type->get_specific_blocks() as $block_name ) {
				$map[ $block_name ][] = $post_type->get_type();
			}
		}

		return $map;
	}

	/**
	 * Gets all registered block names.
	 *
	 * @return string[]
	 */
	private function get_all_registered_block_names(): array {

		return array_keys( WP_Block_Type_Registry::get_instance()->get_all_registered() );
	}

	/**
	 * Checks if a block is allowed for the given post type.
	 *
	 * If the block is not specifically restricted to any post types (i.e. not present in the map),
	 * it is considered allowed by default.
	 *
	 * @param string                  $block_name         The name of the block to check.
	 * @param string                  $current_post_type  The current post type slug.
	 * @param array<string, string[]> $map       Map of block names to arrays of allowed post types.
	 *
	 * @return bool True if the block is allowed for the current post type, false otherwise.
	 */
	private function is_block_allowed( string $block_name, string $current_post_type, array $map ): bool {

		if ( ! isset( $map[ $block_name ] ) ) {
			return true;
		}

		return in_array( $current_post_type, $map[ $block_name ], true );
	}
}
