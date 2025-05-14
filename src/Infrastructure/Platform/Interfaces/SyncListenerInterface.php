<?php
/**
 * Defines the interface for synchronizing WordPress posts with internal entities.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Platform\Interfaces;

use WP_Post;

interface SyncListenerInterface {

	/**
	 * Registers the actions to synchronize post data.
	 *
	 * @since 1.0.0
	 */
	public function register(): void;

	/**
	 * Synchronizes a post with the internal entity.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_id The ID of the post being synchronized.
	 * @param WP_Post $post The post object being synchronized.
	 */
	public function sync( int $post_id, WP_Post $post ): void;

	/**
	 * Deletes the corresponding internal entity when the associated post is deleted.
	 *
	 * @param int     $post_id The ID of the post being deleted.
	 * @param WP_Post $post The post object being deleted.
	 */
	public function delete( int $post_id, WP_Post $post ): void;
}
