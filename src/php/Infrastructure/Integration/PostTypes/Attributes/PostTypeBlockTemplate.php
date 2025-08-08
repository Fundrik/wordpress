<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Integration\PostTypes\Attributes;

use Attribute;

/**
 * Declares the block-based editor template layout for the post type.
 *
 * This layout defines the initial structure of blocks when creating a new post.
 * It corresponds to the `template` argument in `register_post_type()`.
 *
 * Each row is an array of block names; the outer array defines the block order.
 *
 * @since 1.0.0
 *
 * @internal
 */
#[Attribute( Attribute::TARGET_CLASS )]
final readonly class PostTypeBlockTemplate {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array<array<string>> $value The nested layout array of fully qualified block names.
	 */
	public function __construct(
		public array $value,
	) {}
}
