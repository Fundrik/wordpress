<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress;

use Fundrik\WordPress\Infrastructure\FundrikContext;
use WP_Block_Type_Registry;

/**
 * Provides WordPress-specific context for the Fundrik plugin.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class WordPressContext {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param FundrikContext $plugin The plugin-specific context.
	 */
	public function __construct(
		public FundrikContext $plugin,
	) {}

	/**
	 * Retrieves the registered WordPress post types.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, \WP_Post_Type> Registered post type objects keyed by slug.
	 */
	public function get_post_types(): array {

		return get_post_types( output: 'objects' );
	}

	/**
	 * Retrieves the registered WordPress block types.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, \WP_Block_Type> Registered block type objects keyed by name.
	 */
	public function get_block_types(): array {

		// phpcs:ignore SlevomatCodingStandard.Commenting.InlineDocCommentDeclaration.InvalidFormat, Generic.Commenting.DocComment.MissingShort
		/** @var array<string, \WP_Block_Type> */
		return WP_Block_Type_Registry::get_instance()->get_all_registered();
	}
}
