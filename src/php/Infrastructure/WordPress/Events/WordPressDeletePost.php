<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress\Events;

use Fundrik\WordPress\Infrastructure\WordPress\WordPressContext\WordPressContextInterface;
use WP_Post;

/**
 * Represents the 'delete_post' WordPress action as a Fundrik event.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class WordPressDeletePost {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 * @param WP_Post $post Post object.
	 * @param WordPressContextInterface $context The WordPress-specific plugin context.
	 */
	public function __construct(
		public int $post_id,
		public WP_Post $post,
		public readonly WordPressContextInterface $context,
	) {}
}
