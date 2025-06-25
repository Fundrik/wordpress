<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Platform;

// phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse
use Fundrik\WordPress\Infrastructure\Platform\Interfaces\PostTypeInterface;
use WP_Block_Type_Registry;

/**
 * Ensures only blocks registered for a specific post type are allowed.
 *
 * @since 1.0.0
 */
final readonly class AllowedBlockTypesFilter {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string>|null $all_registered_blocks Optional list of all registered block names
	 *                                                  for test injection or override.
	 */
	public function __construct(
		private ?array $all_registered_blocks = null,
	) {}

	/**
	 * Filters allowed block types by current post type.
	 *
	 * @since 1.0.0
	 *
	 * @param bool|array<string> $allowed_blocks True or list of block names currently allowed.
	 * @param string $current_post_type The post type currently being edited.
	 * @param array<PostTypeInterface> $post_types List of post type objects.
	 *
	 * @return array<int, string> Filtered list of allowed block names for the current post type.
	 */
	public function filter( bool|array $allowed_blocks, string $current_post_type, array $post_types ): array {

		if ( $allowed_blocks === false ) {
			return [];
		}

		$block_allowed_post_types = $this->build_block_allowed_post_types_map( $post_types );

		if ( $allowed_blocks === true ) {
			$allowed_blocks = $this->get_all_registered_block_names();
		}

		$filtered = array_filter(
			$allowed_blocks,
			fn ( string $block_name ): bool => $this->is_block_allowed(
				$block_name,
				$current_post_type,
				$block_allowed_post_types,
			),
		);

		return array_values( $filtered );
	}

	/**
	 * Builds map block_name => [allowed_post_types].
	 *
	 * @since 1.0.0
	 *
	 * @param array<PostTypeInterface> $post_types List of post type objects.
	 *
	 * @return array<string, array<string>> Map where keys are block names,
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
	 * @since 1.0.0
	 *
	 * @return array<string>
	 */
	private function get_all_registered_block_names(): array {

		if ( $this->all_registered_blocks !== null ) {
			return $this->all_registered_blocks;
		}

		// @codeCoverageIgnoreStart
		return array_keys( WP_Block_Type_Registry::get_instance()->get_all_registered() );
		// @codeCoverageIgnoreEnd
	}

	/**
	 * Checks if a block is allowed for the given post type.
	 *
	 * If the block is not specifically restricted to any post types (i.e. not present in the map),
	 * it is considered allowed by default.
	 *
	 * @since 1.0.0
	 *
	 * @param string $block_name The name of the block to check.
	 * @param string $current_post_type The current post type slug.
	 * @param array<string, array<string>> $map Map of block names to arrays of allowed post types.
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
