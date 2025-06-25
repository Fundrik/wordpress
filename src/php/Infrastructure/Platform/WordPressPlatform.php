<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Platform;

use Fundrik\Core\Infrastructure\Interfaces\DependencyProviderInterface;
use Fundrik\Core\Support\TypeCaster;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationManager;
use Fundrik\WordPress\Infrastructure\Platform\Interfaces\ListenerInterface;
use Fundrik\WordPress\Infrastructure\Platform\Interfaces\PlatformInterface;
use Fundrik\WordPress\Infrastructure\Platform\Interfaces\PostTypeInterface;
use Fundrik\WordPress\Support\Path;
use RuntimeException;
use WP_Block_Editor_Context;

/**
 * Represents the WordPress platform integration for Fundrik.
 *
 * @since 1.0.0
 */
final readonly class WordPressPlatform implements PlatformInterface {

	/**
	 * Constructor.
	 *
	 * @param DependencyProviderInterface $dependency_provider Provides all necessary bindings
	 *                                                for dependency injection within the platform.
	 * @param AllowedBlockTypesFilter $allowed_block_types_filter Handles filtering of allowed block types.
	 * @param MigrationManager $migration_manager Manages and executes plugin database migrations.
	 *
	 * @since 1.0.0
	 */
	public function __construct(
		private DependencyProviderInterface $dependency_provider,
		private AllowedBlockTypesFilter $allowed_block_types_filter,
		private MigrationManager $migration_manager,
	) {}

	/**
	 * Initializes the platform.
	 *
	 * @since 1.0.0
	 */
	public function init(): void {

		$this->register_listeners();

		add_action( 'init', $this->register_post_types( ... ) );
		add_action( 'init', $this->register_blocks( ... ) );

		add_filter(
			'allowed_block_types_all',
			$this->filter_allowed_blocks_by_post_type( ... ),
			10,
			2,
		);
	}

	// phpcs:disable SlevomatCodingStandard.Functions.FunctionLength.FunctionLength
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
					'labels' => $post_type->get_labels(),
					'public' => true,
					'menu_icon' => 'dashicons-heart',
					'supports' => [ 'title', 'editor', 'custom-fields' ],
					'has_archive' => true,
					'rewrite' => [ 'slug' => $post_type->get_slug() ],
					'show_in_rest' => true,
					'template' => $post_type->get_template_blocks(),
				],
			);

			foreach ( $post_type->get_meta_fields() as $meta_key => $args ) {

				register_post_meta(
					$post_type->get_type(),
					$meta_key,
					$args + [
						'show_in_rest' => true,
						'single' => true,
					],
				);
			}
		}
	}
	// phpcs:enable SlevomatCodingStandard.Functions.FunctionLength.FunctionLength

	/**
	 * Registers all custom blocks for the platform.
	 *
	 * @since 1.0.0
	 */
	public function register_blocks(): void {

		wp_register_block_types_from_metadata_collection(
			Path::Blocks->get_full_path(),
			Path::BlocksManifest->get_full_path(),
		);
	}

	/**
	 * Filters the allowed block types based on the current post type context.
	 *
	 * @since 1.0.0
	 *
	 * @param bool|array<string> $allowed_blocks The blocks allowed by default.
	 *                                                Can be true (all allowed), false (none allowed),
	 *                                                or an array of block names.
	 * @param WP_Block_Editor_Context $editor_context Context object containing info about
	 *                                                the editor state.
	 *
	 * @return array<string> The filtered array of allowed block names for the current post type.
	 */
	public function filter_allowed_blocks_by_post_type(
		bool|array $allowed_blocks,
		WP_Block_Editor_Context $editor_context,
	): array {

		if ( $editor_context->post === null ) {
			return is_array( $allowed_blocks ) ? $allowed_blocks : [];
		}

		return $this->allowed_block_types_filter->filter(
			$allowed_blocks,
			$editor_context->post->post_type,
			$this->get_post_types(),
		);
	}

	/**
	 * Handles actions that must occur once upon plugin activation.
	 *
	 * @since 1.0.0
	 */
	public function on_activate(): void {

		$this->migration_manager->migrate();
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
	 * @since 1.0.0
	 *
	 * @return array<PostTypeInterface> Array of post type instances.
	 */
	private function get_post_types(): array {

		$post_type_classes = $this->dependency_provider->get_bindings( 'post_types' );
		$post_types = [];

		foreach ( $post_type_classes as $class ) {

			$post_type = fundrik()->get( TypeCaster::to_string( $class ) );

			if ( ! $post_type instanceof PostTypeInterface ) {
				// @todo Escaping
				throw new RuntimeException(
					sprintf(
						'Expected instance of PostTypeInterface, got %s for class %s',
						get_debug_type( $post_type ),
						TypeCaster::to_scalar_or_null( $class ),
					),
				);
			}

			$post_types[] = $post_type;
		}

		return $post_types;
	}

	/**
	 * Retrieves all listener instances.
	 *
	 * @since 1.0.0
	 *
	 * @return array<ListenerInterface> Array of listener instances.
	 */
	private function get_listeners(): array {

		$listener_classes = $this->dependency_provider->get_bindings( 'listeners' );
		$listeners = [];

		foreach ( $listener_classes as $class ) {

			$listener = fundrik()->get( TypeCaster::to_string( $class ) );

			if ( ! $listener instanceof ListenerInterface ) {
				// @todo Escaping
				throw new RuntimeException(
					sprintf(
						'Expected instance of ListenerInterface, got %s for class %s',
						get_debug_type( $listener ),
						TypeCaster::to_scalar_or_null( $class ),
					),
				);
			}

			$listeners[] = $listener;
		}

		return $listeners;
	}
}
