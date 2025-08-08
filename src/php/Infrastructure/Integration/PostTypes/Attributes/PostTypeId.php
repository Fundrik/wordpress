<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Integration\PostTypes\Attributes;

use Attribute;

/**
 * Declares the identifier used to register the post type in WordPress.
 *
 * This identifier corresponds to the `$post_type` key passed to `register_post_type()`.
 * It must be unique and follow the constraints imposed by `sanitize_key()`:
 * lowercase letters, numbers, dashes, and underscores only; max length 20 characters.
 *
 * @since 1.0.0
 *
 * @internal
 */
#[Attribute( Attribute::TARGET_CLASS )]
final readonly class PostTypeId {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value The post type id.
	 */
	public function __construct(
		public string $value,
	) {}
}
