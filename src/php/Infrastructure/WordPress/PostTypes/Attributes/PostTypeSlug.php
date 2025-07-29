<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress\PostTypes\Attributes;

use Attribute;

/**
 * Declares the permastruct slug used for rewrite rules of the post type.
 *
 * This value is passed as `rewrite['slug']` to `register_post_type()` and
 * determines the base part of the URL structure for the post type.
 *
 * If not set, WordPress defaults it to the post type id.
 *
 * @since 1.0.0
 *
 * @internal
 */
#[Attribute( Attribute::TARGET_CLASS )]
final readonly class PostTypeSlug {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value The permastruct slug for the post type URLs.
	 */
	public function __construct(
		public string $value,
	) {}
}
