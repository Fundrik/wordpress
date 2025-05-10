<?php
/**
 * WordpressPlatform class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Platform;

use Fundrik\Core\Domain\Campaigns\Interfaces\QueryExecutorInterface;
use Fundrik\Core\Infrastructure\Platform\Interfaces\PlatformInterface;
use Fundrik\WordPress\Infrastructure\Campaigns\Persistence\WpdbQueryExecutor;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\CampaignPostType;

/**
 * Represents the WordPress platform integration for Fundrik.
 *
 * @since 1.0.0
 */
final readonly class WordpressPlatform implements PlatformInterface {

	/**
	 * Initializes the platform.
	 *
	 * @since 1.0.0
	 */
	public function init(): void {

		$this->setup_container();

		add_action( 'init', $this->register_post_types( ... ) );
	}

	/**
	 * Registers the custom post types for the platform.
	 *
	 * @since 1.0.0
	 */
	public function register_post_types(): void {

		register_post_type(
			CampaignPostType::TYPE,
			[
				'labels'       => CampaignPostType::get_labels(),
				'public'       => true,
				'menu_icon'    => 'dashicons-heart',
				'supports'     => [ 'title', 'editor' ],
				'has_archive'  => true,
				'rewrite'      => [ 'slug' => CampaignPostType::REWRITE_SLUG ],
				'show_in_rest' => true,
			]
		);
	}

	/**
	 * Sets up the container for dependency injection.
	 *
	 * @since 1.0.0
	 */
	private function setup_container(): void {

		$fundrik_container = fundrik();

		$fundrik_container->singleton( QueryExecutorInterface::class, WpdbQueryExecutor::class );
	}
}
