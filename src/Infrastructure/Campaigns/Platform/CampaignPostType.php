<?php
/**
 * CampaignPostType class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Campaigns\Platform;

/**
 * Contains constants and configuration for the 'fundrik_campaign' post type.
 *
 * @since 1.0.0
 */
final readonly class CampaignPostType {

	public const TYPE = 'fundrik_campaign';

	public const META_IS_OPEN          = 'is_open';
	public const META_HAS_TARGET       = 'has_target';
	public const META_TARGET_AMOUNT    = 'target_amount';
	public const META_COLLECTED_AMOUNT = 'collected_amount';

	public const REWRITE_SLUG = 'campaigns';

	/**
	 * Returns labels for the custom post type.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, string>
	 */
	public static function get_labels(): array {

		return [
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
		];
	}
}
