<?php
/**
 * WordPressPlatform class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Platform;

use Fundrik\Core\Application\Platform\Interfaces\PlatformInterface;
use Fundrik\WordPress\Infrastructure\DependencyProvider;
use Fundrik\WordPress\Infrastructure\Platform\Interfaces\ListenerInterface;
use Fundrik\WordPress\Infrastructure\Platform\Interfaces\PostTypeInterface;
use Fundrik\WordPress\Support\Path;
use WP_Block_Editor_Context;

/**
 * Represents the WordPress platform integration for Fundrik.
 *
 * @since 1.0.0
 */
final readonly class WordPressPlatform implements PlatformInterface {

	/**
	 * Constructs the WordPress platform integration.
	 *
	 * @param DependencyProvider      $dependency_provider Provides all necessary bindings
	 *                                                     for dependency injection within the platform.
	 * @param AllowedBlockTypesFilter $allowed_block_types_filter Handles filtering of allowed block types.
	 *
	 * @since 1.0.0
	 */
	public function __construct(
		private DependencyProvider $dependency_provider,
		private AllowedBlockTypesFilter $allowed_block_types_filter,
	) {}

	/**
	 * Initializes the platform.
	 *
	 * @since 1.0.0
	 */
	public function init(): void {

		$this->register_bindings();
		$this->register_listeners();

		add_action( 'init', $this->register_post_types( ... ) );
		add_action( 'init', $this->register_blocks( ... ) );

		add_filter(
			'allowed_block_types_all',
			$this->filter_allowed_blocks_by_post_type( ... ),
			10,
			2
		);
	}

	/**
	 * Registers all custom post types for the platform.
	 *
	 * @since 1.0.0
	 */
	public function register_post_types(): void {

		foreach ( $this->get_post_types() as $post_type ) {

			register_post_type(
				$post_type->get_type(),
				[
					'labels'       => $post_type->get_labels(),
					'public'       => true,
					'menu_icon'    => 'dashicons-heart',
					'supports'     => [ 'title', 'editor', 'custom-fields' ],
					'has_archive'  => true,
					'rewrite'      => [ 'slug' => $post_type->get_slug() ],
					'show_in_rest' => true,
					'template'     => $post_type->get_template_blocks(),
				]
			);

			foreach ( $post_type->get_meta_fields() as $meta_key => $args ) {

				register_post_meta(
					$post_type->get_type(),
					$meta_key,
					wp_parse_args(
						$args,
						[
							'show_in_rest' => true,
							'single'       => true,
						]
					)
				);
			}
		}
	}

	/**
	 * Registers all custom blocks for the platform.
	 *
	 * @since 1.0.0
	 */
	public function register_blocks(): void {

		wp_register_block_types_from_metadata_collection(
			Path::blocks(),
			Path::blocks_manifest()
		);
	}

	/**
	 * Filters the allowed block types based on the current post type context.
	 *
	 * @since 1.0.0
	 *
	 * @param bool|array              $allowed_blocks The blocks allowed by default.
	 *                                                Can be true (all allowed), false (none allowed),
	 *                                                or an array of block names.
	 * @param WP_Block_Editor_Context $editor_context Context object containing info about
	 *                                                the editor state.
	 *
	 * @return array The filtered array of allowed block names for the current post type.
	 */
	public function filter_allowed_blocks_by_post_type(
		bool|array $allowed_blocks,
		WP_Block_Editor_Context $editor_context
	): array {

		return $this->allowed_block_types_filter->filter(
			$allowed_blocks,
			$editor_context->post->post_type,
			$this->get_post_types()
		);
	}

	/**
	 * Registers all dependency bindings into the container
	 *
	 * @since 1.0.0
	 */
	private function register_bindings(): void {

		$fundrik_container = fundrik();

		foreach ( $this->dependency_provider->get_bindings() as $abstract => $concrete ) {

			if ( is_array( $concrete ) ) {

				foreach ( $concrete as $a => $c ) {
					$fundrik_container->singleton( $a, $c );
				}

				continue;
			}

			$fundrik_container->singleton( $abstract, $concrete );
		}
	}

	/**
	 * Registers all platform-specific listeners.
	 *
	 * @since 1.0.0
	 */
	private function register_listeners(): void {

		foreach ( $this->get_listeners() as $listener ) {

			$listener->register();
		}
	}

	/**
	 * Retrieves all post type instances.
	 *
	 * @return PostTypeInterface[] Array of post type instances.
	 *
	 * @since 1.0.0
	 */
	private function get_post_types(): array {

		$post_type_classes = $this->dependency_provider->get_bindings( 'post_types' );
		$post_types        = [];

		foreach ( $post_type_classes as $class ) {

			$post_type = fundrik()->get( $class );

			if ( $post_type instanceof PostTypeInterface ) {
				$post_types[] = $post_type;
			}
		}

		return $post_types;
	}

	/**
	 * Retrieves all listener instances.
	 *
	 * @return ListenerInterface[] Array of listener instances.
	 *
	 * @since 1.0.0
	 */
	private function get_listeners(): array {

		$listeners_classes = $this->dependency_provider->get_bindings( 'listeners' );
		$listeners         = [];

		foreach ( $listeners_classes as $class ) {

			$listener = fundrik()->get( $class );

			if ( $listener instanceof ListenerInterface ) {
				$listeners[] = $listener;
			}
		}

		return $listeners;
	}
}
