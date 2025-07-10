<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Shared\Infrastructure\WordPress\Listeners;

use Fundrik\WordPress\Shared\Infrastructure\WordPress\Events\AllowedBlockTypesFilterWordPressEvent;

/**
 * Filters the allowed block types based on the current post type.
 *
 * Only blocks explicitly registered for a given post type will be allowed.
 * Blocks without restriction will remain allowed by default.
 *
 * @since 1.0.0
 */
final readonly class FilterAllowedBlocksByPostTypeWordPressListener {

	/**
	 * The map of block names to the list of allowed post types.
	 *
	 * Format: [ block_name => [ post_type_slug1, post_type_slug2, ... ] ]
	 *
	 * @var array<string, array<string>>
	 */
	private array $block_allowed_post_types;

	/**
	 * Handler.
	 *
	 * @since 1.0.0
	 *
	 * @param AllowedBlockTypesFilterWordPressEvent $event The 'allowed_block_types_all' WordPress action
	 *                                                     with the WordPress-specific plugin context.
	 */
	public function handle( AllowedBlockTypesFilterWordPressEvent $event ): void {

		$allowed = $event->allowed;

		if ( $allowed === false ) {
			return;
		}

		$current_post_type = $event->editor_context->post->post_type ?? null;

		if ( $current_post_type === null ) {
			return;
		}

		if ( $allowed === true ) {
			$allowed = array_keys( $event->context->get_block_types() );
		}

		$this->set_block_allowed_post_types( $event->context->plugin->get_post_types() );

		$filtered = array_filter(
			$allowed,
			fn ( string $block_name ): bool => $this->is_block_allowed( $block_name, $current_post_type ),
		);

		$event->allowed = array_values( $filtered );
	}

	/**
	 * Sets the map of block names to their allowed post types.
	 *
	 * @since 1.0.0
	 *
	 * @param array<PostTypeInterface> $post_types The list of Fundrik post type instances.
	 */
	private function set_block_allowed_post_types( array $post_types ): void {

		$map = [];

		foreach ( $post_types as $post_type ) {

			foreach ( $post_type->get_specific_blocks() as $block_name ) {
				$map[ $block_name ][] = $post_type->get_type();
			}
		}

		$this->block_allowed_post_types = $map;
	}

	/**
	 * Checks if a block is allowed for the given post type.
	 *
	 * If the block is not explicitly restricted, it is allowed by default.
	 *
	 * @since 1.0.0
	 *
	 * @param string $block_name The block name.
	 * @param string $current_post_type The current post type slug.
	 *
	 * @return bool True True if allowed, false otherwise.
	 */
	private function is_block_allowed( string $block_name, string $current_post_type ): bool {

		$map = $this->block_allowed_post_types;

		if ( ! isset( $map[ $block_name ] ) ) {
			return true;
		}

		return in_array( $current_post_type, $map[ $block_name ], true );
	}
}
