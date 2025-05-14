<?php
/**
 * WordpressPlatform class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Platform;

use Fundrik\Core\Infrastructure\Platform\Interfaces\PlatformInterface;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\CampaignPostType;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\CampaignSyncProvider;
use Fundrik\WordPress\Infrastructure\DependencyProvider;

/**
 * Represents the WordPress platform integration for Fundrik.
 *
 * @since 1.0.0
 */
final readonly class WordpressPlatform implements PlatformInterface {

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
	}

	/**
	 * Registers all custom post types for the platform.
	 *
	 * @since 1.0.0
	 */
	public function register_post_types(): void {

		register_post_type(
			CampaignPostType::get_type(),
			[
				'labels'       => CampaignPostType::get_labels(),
				'public'       => true,
				'menu_icon'    => 'dashicons-heart',
				'supports'     => [ 'title', 'editor' ],
				'has_archive'  => true,
				'rewrite'      => [ 'slug' => CampaignPostType::get_rewrite_slug() ],
				'show_in_rest' => true,
			]
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

		$fundrik_container = fundrik();

		( $fundrik_container->get( CampaignSyncProvider::class ) )->register();
	}
}
