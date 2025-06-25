<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Campaigns\Platform;

use Fundrik\WordPress\Infrastructure\Platform\Interfaces\PostTypeInterface;

/**
 * Provides constants and configuration for the campaign post type.
 *
 * @since 1.0.0
 */
class WordPressCampaignPostType implements PostTypeInterface {

	// phpcs:disable
	/**
	 * @var string
	 * 
	 * @todo Replace with native typed constants when upgrading to PHP 8.3.
	 */
	public const META_IS_OPEN = 'is_open';

	/**
	 * @var string
	 * 
	 * @todo Replace with native typed constants when upgrading to PHP 8.3.
	 */
	public const META_HAS_TARGET = 'has_target';

	/**
	 * @var string
	 * 
	 * @todo Replace with native typed constants when upgrading to PHP 8.3.
	 */
	public const META_TARGET_AMOUNT = 'target_amount';
	// phpcs:enable

	public const CAMPAIGN_SETTINGS_BLOCK = 'fundrik/campaign-settings';

	/**
	 * Returns the custom post type identifier for the campaign post type.
	 *
	 * @since 1.0.0
	 *
	 * @return string The campaign post type identifier.
	 */
	public function get_type(): string {

		/**
		 * Filters the custom post type identifier for campaigns.
		 *
		 * @since 1.0.0
		 *
		 * @param string $post_type The default post type slug.
		 */
		return apply_filters( 'fundrik_campaign_post_type', 'fundrik_campaign' );
	}

	// phpcs:disable SlevomatCodingStandard.Functions.FunctionLength.FunctionLength
	/**
	 * Returns labels for the campaign post type.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, string> An associative array where the keys are label names
	 *                               and the values are the corresponding localized label strings
	 *                               for the campaign post type.
	 */
	public function get_labels(): array {

		/**
		 * Filters the labels used for the campaign post type.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, string> $labels An associative array of post type labels.
		 */
		return apply_filters(
			'fundrik_campaign_post_type_labels',
			[
				'name' => __( 'Campaigns', 'fundrik' ),
				'singular_name' => __( 'Campaign', 'fundrik' ),
				'menu_name' => __( 'Campaigns', 'fundrik' ),
				'name_admin_bar' => __( 'Campaign', 'fundrik' ),
				'add_new' => __( 'Add New', 'fundrik' ),
				'add_new_item' => __( 'Add New Campaign', 'fundrik' ),
				'new_item' => __( 'New Campaign', 'fundrik' ),
				'edit_item' => __( 'Edit Campaign', 'fundrik' ),
				'view_item' => __( 'View Campaign', 'fundrik' ),
				'all_items' => __( 'All Campaigns', 'fundrik' ),
				'search_items' => __( 'Search Campaigns', 'fundrik' ),
				'parent_item_colon' => __( 'Parent Campaigns:', 'fundrik' ),
				'not_found' => __( 'No campaigns found.', 'fundrik' ),
				'not_found_in_trash' => __( 'No campaigns found in Trash.', 'fundrik' ),
				'featured_image' => __( 'Campaign Cover Image', 'fundrik' ),
				'set_featured_image' => __( 'Set campaign cover image', 'fundrik' ),
				'remove_featured_image' => __( 'Remove campaign cover image', 'fundrik' ),
				'use_featured_image' => __( 'Use as campaign cover image', 'fundrik' ),
				'archives' => __( 'Campaign archives', 'fundrik' ),
				'insert_into_item' => __( 'Insert into campaign', 'fundrik' ),
				'uploaded_to_this_item' => __( 'Uploaded to this campaign', 'fundrik' ),
				'items_list' => __( 'Campaigns list', 'fundrik' ),
				'items_list_navigation' => __( 'Campaigns list navigation', 'fundrik' ),
				'filter_items_list' => __( 'Filter campaigns list', 'fundrik' ),
			],
		);
	}
	// phpcs:enable SlevomatCodingStandard.Functions.FunctionLength.FunctionLength

	/**
	 * Returns the slug used for the campaign post type.
	 *
	 * @since 1.0.0
	 *
	 * @return string The slug for the campaign post type.
	 */
	public function get_slug(): string {

		/**
		 * Filters the slug for the campaign post type.
		 *
		 * @since 1.0.0
		 *
		 * @param string $slug The default slug.
		 */
		return apply_filters( 'fundrik_campaign_post_type_slug', 'campaigns' );
	}

	// phpcs:disable SlevomatCodingStandard.Files.LineLength.LineTooLong
	/**
	 * Returns an array of meta fields associated with the campaign post type.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, array{type: string, default?: mixed}> An associative array where keys are meta field names,
	 *                                                             and values are arrays of configuration options for each field.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	public function get_meta_fields(): array {

		return [
			self::META_IS_OPEN => [
				'type' => 'boolean',
				'default' => true,
			],
			self::META_HAS_TARGET => [ 'type' => 'boolean' ],
			self::META_TARGET_AMOUNT => [ 'type' => 'string' ],
		];
	}
	// phpcs:enable SlevomatCodingStandard.Files.LineLength.LineTooLong

	/**
	 * Returns the block-based template used to render the campaign post type in the editor.
	 *
	 * @since 1.0.0
	 *
	 * @return array<int, array<string>> A nested array of block names representing
	 *                                   the template layout for the campaign post type.
	 */
	public function get_template_blocks(): array {

		return [
			[ self::CAMPAIGN_SETTINGS_BLOCK ],
		];
	}

	/**
	 * Returns a list of block names that are specifically allowed for the campaign post type.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string> List of block names allowed for the campaign post type.
	 */
	public function get_specific_blocks(): array {

		return [
			self::CAMPAIGN_SETTINGS_BLOCK,
		];
	}
}
