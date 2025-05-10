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

		register_post_type( 'fundrik_campaign', $this->get_campaign_post_type_config() );
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

	/**
	 * Retrieves the configuration for the 'fundrik_campaign' post type.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, mixed> The configuration for the 'fundrik_campaign' post type.
	 */
	private function get_campaign_post_type_config(): array {

		return [
			'labels'       => [
				'name'                  => __( 'Campaigns', 'fundrik' ),
				'singular_name'         => __( 'Campaign', 'fundrik' ),
				'menu_name'             => __( 'Campaigns', 'fundrik' ),
				'name_admin_bar'        => __( 'Campaign', 'fundrik' ),
				'add_new'               => __( 'Add New', 'fundrik' ),
				'add_new_item'          => __( 'Add New Campaign', 'fundrik' ),
				'new_item'              => __( 'New Campaign', 'fundrik' ),
				'edit_item'             => __( 'Edit Campaign', 'fundrik' ),
				'view_item'             => __( 'View Campaign', 'fundrik' ),
				'all_items'             => __( 'All Campaigns', 'fundrik' ),
				'search_items'          => __( 'Search Campaigns', 'fundrik' ),
				'parent_item_colon'     => __( 'Parent Campaigns:', 'fundrik' ),
				'not_found'             => __( 'No campaigns found.', 'fundrik' ),
				'not_found_in_trash'    => __( 'No campaigns found in Trash.', 'fundrik' ),
				'featured_image'        => __( 'Campaign Cover Image', 'fundrik' ),
				'set_featured_image'    => __( 'Set campaign cover image', 'fundrik' ),
				'remove_featured_image' => __( 'Remove campaign cover image', 'fundrik' ),
				'use_featured_image'    => __( 'Use as campaign cover image', 'fundrik' ),
				'archives'              => __( 'Campaign archives', 'fundrik' ),
				'insert_into_item'      => __( 'Insert into campaign', 'fundrik' ),
				'uploaded_to_this_item' => __( 'Uploaded to this campaign', 'fundrik' ),
				'items_list'            => __( 'Campaigns list', 'fundrik' ),
				'items_list_navigation' => __( 'Campaigns list navigation', 'fundrik' ),
				'filter_items_list'     => __( 'Filter campaigns list', 'fundrik' ),
			],
			'public'       => true,
			'menu_icon'    => 'dashicons-heart',
			'supports'     => [ 'title', 'editor' ],
			'has_archive'  => true,
			'rewrite'      => [ 'slug' => 'campaigns' ],
			'show_in_rest' => true,
		];
	}
}
