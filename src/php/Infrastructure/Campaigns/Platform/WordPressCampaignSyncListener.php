<?php
/**
 * WordPressCampaignSyncListener class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Campaigns\Platform;

use Fundrik\Core\Domain\EntityId;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignInputFactory;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignPartialInputFactory;
use Fundrik\WordPress\Application\Campaigns\Interfaces\WordPressCampaignServiceInterface;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces\WordPressCampaignSyncListenerInterface;
use stdClass;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use WP_Error;
use WP_Post;
use WP_REST_Request;

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
	 * @param WordPressCampaignPostType                 $post_type             Post type definition for campaign posts.
	 * @param AdminWordPressCampaignInputFactory        $input_factory         Factory to create full input DTOs.
	 * @param AdminWordPressCampaignPartialInputFactory $partial_input_factory Factory to create partial input DTOs.
	 * @param WordPressCampaignServiceInterface         $service               Service to manage WordPress campaign entities.
	 */
	public function __construct(
		private WordPressCampaignPostType $post_type,
		private AdminWordPressCampaignInputFactory $input_factory,
		private AdminWordPressCampaignPartialInputFactory $partial_input_factory,
		private WordPressCampaignServiceInterface $service
	) {
	}

	/**
	 * Registers the actions to synchronize post data.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {

		add_filter(
			'rest_pre_insert_' . $this->post_type->get_type(),
			$this->validate( ... ),
			10,
			2
		);

		add_action(
			'wp_insert_post',
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
	 * Validates the incoming request data before inserting a new campaign post.
	 *
	 * @since 1.0.0
	 *
	 * @param stdClass        $prepared_post The post object prepared for insertion.
	 * @param WP_REST_Request $request       The REST request containing input data.
	 *
	 * @return stdClass|WP_Error The validated post object, or WP_Error on failure.
	 */
	public function validate( stdClass $prepared_post, WP_REST_Request $request ): stdClass|WP_Error {

		$input_data = $request->get_params();

		try {
			$input = $this->partial_input_factory->from_array( $input_data );
			$this->service->validate_input( $input );
		} catch ( ValidationFailedException $e ) {
			return new WP_Error(
				'campaign_validation_failed',
				$e->getMessage(),
				[ 'status' => 400 ]
			);
		}

		return $prepared_post;
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

		try {
			$input = $this->input_factory->from_wp_post( $post );
			$this->service->save_campaign( $input );
		} catch ( ValidationFailedException $e ) {
			// phpcs:ignore
			error_log( 'Campaign validation failed: ' . $e->getMessage() );
		}
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
