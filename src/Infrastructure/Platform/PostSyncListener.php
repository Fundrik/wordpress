<?php
/**
 * PostSyncListener class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Platform;

use Fundrik\Core\Application\Interfaces\EntityServiceInterface;
use Fundrik\Core\Domain\EntityId;
use Fundrik\WordPress\Infrastructure\Platform\Interfaces\PostToEntityDtoMapperInterface;
use Fundrik\WordPress\Infrastructure\Platform\Interfaces\SyncListenerInterface;
use WP_Post;

/**
 * Listens for changes in the WordPress posts and synchronizes them with the internal entities.
 *
 * @since 1.0.0
 */
final readonly class PostSyncListener implements SyncListenerInterface {

	/**
	 * PostSyncListener constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string                         $post_type The post type to listen for.
	 * @param PostToEntityDtoMapperInterface $mapper The mapper responsible for converting WP_Post to a DTO.
	 * @param EntityServiceInterface         $service The service responsible for managing the entity.
	 */
	public function __construct(
		private string $post_type,
		private PostToEntityDtoMapperInterface $mapper,
		private EntityServiceInterface $service
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
	 * Synchronizes a post with the internal entity.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_id The ID of the post being synchronized.
	 * @param WP_Post $post The post object being synchronized.
	 */
	public function sync( int $post_id, WP_Post $post ): void {

		if ( $post->post_type !== $this->post_type ) {
			return;
		}

		$dto = $this->mapper->from_wp_post( $post );

		$this->service->save( $dto );
	}

	/**
	 * Deletes the corresponding internal entity when the associated post is deleted.
	 *
	 * @param int     $post_id The ID of the post being deleted.
	 * @param WP_Post $post The post object being deleted.
	 */
	public function delete( int $post_id, WP_Post $post ): void {

		if ( $post->post_type !== $this->post_type ) {
			return;
		}

		$this->service->delete( EntityId::create( $post_id ) );
	}
}
