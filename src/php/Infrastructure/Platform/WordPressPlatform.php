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

/**
 * Represents the WordPress platform integration for Fundrik.
 *
 * @since 1.0.0
 */
final readonly class WordPressPlatform implements PlatformInterface {

	/**
	 * Constructs the WordPress platform integration.
	 *
	 * @param DependencyProvider $dependency_provider Provides all necessary bindings
	 *                                                for dependency injection within the platform.
	 *
	 * @since 1.0.0
	 */
	public function __construct(
		private DependencyProvider $dependency_provider
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
	}

	/**
	 * Registers all custom post types for the platform.
	 *
	 * @since 1.0.0
	 */
	public function register_post_types(): void {

		$post_types = $this->dependency_provider->get_bindings( 'post_types' );

		foreach ( $post_types as $class ) {

			/**
			 * Instance of a post type configuration class.
			 *
			 * @var PostTypeInterface $post_type
			 */
			$post_type = fundrik()->get( $class );

			if ( ! $post_type instanceof PostTypeInterface ) {
				continue;
			}

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
				]
			);

			foreach ( $post_type->get_meta_fields() as $meta_key => $type ) {

				register_post_meta(
					$post_type->get_type(),
					$meta_key,
					[
						'show_in_rest' => true,
						'single'       => true,
						'type'         => $type,
					]
				);
			}
		}
	}

	public function register_blocks(): void {

		wp_register_block_types_from_metadata_collection(
			Path::blocks(),
			Path::blocks_manifest()
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

		$listeners = $this->dependency_provider->get_bindings( 'listeners' );

		foreach ( $listeners as $class ) {

			/**
			 * Instance of a post type configuration class.
			 *
			 * @var ListenerInterface $listener
			 */
			$listener = fundrik()->get( $class );

			if ( ! $listener instanceof ListenerInterface ) {
				continue;
			}

			$listener->register();
		}
	}
}
