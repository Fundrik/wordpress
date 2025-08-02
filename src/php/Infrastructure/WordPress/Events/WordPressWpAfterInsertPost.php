<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress\Events;

use Fundrik\WordPress\Infrastructure\WordPress\WordPressContext\WordPressContext;
use WP_Post;

/**
 * Represents the 'wp_after_insert_post' WordPress action as a Fundrik event.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class WordPressWpAfterInsertPost {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 * @param WP_Post $post Post object.
	 * @param bool $update Whether this is an existing post being updated.
	 * @param WP_Post|null $post_before Null for new posts, the WP_Post object prior
	 *                                  to the update for updated posts.
	 * @param WordPressContext $context The WordPress-specific plugin context.
	 */
	public function __construct(
		public int $post_id,
		public WP_Post $post,
		public bool $update,
		public WP_Post|null $post_before,
		public readonly WordPressContext $context,
	) {}
}
