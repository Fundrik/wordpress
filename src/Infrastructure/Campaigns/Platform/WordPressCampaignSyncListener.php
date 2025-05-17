<?php
/**
 * WordPressCampaignSyncListener class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Campaigns\Platform;

use Fundrik\Core\Domain\EntityId;
use Fundrik\WordPress\Application\Campaigns\Interfaces\WordPressCampaignServiceInterface;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces\WordPressCampaignPostMapperInterface;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces\WordPressCampaignSyncListenerInterface;
use WP_Post;

/**
 * Listens for changes in the WordPress posts and synchronizes them with the WordPressCampaign entity.
 *
 * @since 1.0.0
 */
final readonly class WordPressCampaignSyncListener implements WordPressCampaignSyncListenerInterface {

	/**
	 * WordPressCampaignSyncListener constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressCampaignPostType            $post_type Post type definition for campaign posts.
	 * @param WordPressCampaignPostMapperInterface $mapper    Mapper to convert WP_Post to a WordPressCampaignDto.
	 * @param WordPressCampaignServiceInterface    $service   Service to manage the WordPressCampaign entity.
	 */
	public function __construct(
		private WordPressCampaignPostType $post_type,
		private WordPressCampaignPostMapperInterface $mapper,
		private WordPressCampaignServiceInterface $service
	) {
	}

	/**
	 * Registers the actions to synchronize post data.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {

		add_action(
			'wp_after_insert_post',
			$this->sync( ... ),
			10,
			2
		);

		add_action(
			'delete_post',
			$this->delete( ... ),
			10,
			2
		);
	}

	/**
	 * Synchronizes a WordPress campaign post with the WordPressCampaign entity.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_id The ID of the post being synchronized.
	 * @param WP_Post $post The post object being synchronized.
	 */
	public function sync( int $post_id, WP_Post $post ): void {

		if ( $this->post_type->get_type() !== $post->post_type ) {
			return;
		}

		$dto = $this->mapper->from_wp_post( $post );

		$this->service->save_campaign( $dto );
	}

	/**
	 * Deletes the corresponding WordPressCampaign entity when the related post is deleted.
	 *
	 * @param int     $post_id The ID of the post being deleted.
	 * @param WP_Post $post The post object being deleted.
	 */
	public function delete( int $post_id, WP_Post $post ): void {

		if ( $this->post_type->get_type() !== $post->post_type ) {
			return;
		}

		$this->service->delete_campaign( EntityId::create( $post_id ) );
	}
}
