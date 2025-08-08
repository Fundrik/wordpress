<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress\WordPressContext;

use Fundrik\WordPress\Infrastructure\WordPress\PostTypes\CampaignPostType;
use WP_Block_Type_Registry;

/**
 * Provides assess to WordPress-specific plugin context.
 *
 * @since 1.0.0
 *
 * @internal
 */
final class WordPressContext implements WordPressContextInterface {

	/**
	 * Caches the result of get_post_types() to avoid repeated queries to WordPress core.
	 *
	 * Lazily initialized on first access to get_registered_post_types().
	 *
	 * @since 1.0.0
	 *
	 * @var array<string, \WP_Post_Type>|null
	 */
	private ?array $registered_post_types = null;

	/**
	 * Caches the result of WP_Block_Type_Registry to avoid repeated queries to WordPress core.
	 *
	 * Lazily initialized on first access to get_registered_block_types().
	 *
	 * @since 1.0.0
	 *
	 * @var array<string, \WP_Block_Type>|null
	 */
	private ?array $registered_block_types = null;

	/**
	 * Returns the list of declared post type class names.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string> The list of post type class names.
	 *
	 * @phpstan-return list<class-string<\Fundrik\WordPress\Infrastructure\WordPress\PostTypes\PostTypeInterface>>
	 */
	public function get_declared_post_types(): array {

		return [
			CampaignPostType::class,
		];
	}

	/**
	 * Retrieves the registered WordPress post types.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, \WP_Post_Type> Registered post type objects keyed by slug.
	 */
	public function get_registered_post_types(): array {

		if ( $this->registered_post_types === null ) {
			$this->registered_post_types = get_post_types( output: 'objects' );
		}

		return $this->registered_post_types;
	}

	/**
	 * Retrieves the registered WordPress block types.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, \WP_Block_Type> Registered block type objects keyed by name.
	 */
	public function get_registered_block_types(): array {

		if ( $this->registered_block_types === null ) {

			$all_blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();

			// phpcs:disable SlevomatCodingStandard.Commenting.InlineDocCommentDeclaration.MissingVariable, Generic.Commenting.DocComment.MissingShort
			/** @var array<string, \WP_Block_Type> $all_blocks */
			$this->registered_block_types = $all_blocks;
		}

		return $this->registered_block_types;
	}
}
