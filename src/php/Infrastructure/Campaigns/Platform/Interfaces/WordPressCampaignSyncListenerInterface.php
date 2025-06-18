<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces;

use Fundrik\WordPress\Infrastructure\Platform\Interfaces\ListenerInterface;
use WP_Post;

/**
 * Interface for synchronizing WordPress campaign posts with the corresponding
 * WordPressCampaign entities.
 *
 * @since 1.0.0
 */
interface WordPressCampaignSyncListenerInterface extends ListenerInterface {

	/**
	 * Synchronizes a WordPress campaign post with the WordPressCampaign entity.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_id The ID of the post being synchronized.
	 * @param WP_Post $post The post object being synchronized.
	 */
	public function sync( int $post_id, WP_Post $post ): void;

	/**
	 * Deletes the corresponding WordPressCampaign entity when the related post is deleted.
	 *
	 * @param int     $post_id The ID of the post being deleted.
	 * @param WP_Post $post The post object being deleted.
	 */
	public function delete( int $post_id, WP_Post $post ): void;
}
