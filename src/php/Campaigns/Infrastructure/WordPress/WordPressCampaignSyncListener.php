<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Campaigns\Infrastructure;

use Fundrik\Core\Domain\EntityId;
use Fundrik\WordPress\Campaigns\Application\Input\AdminWordPressCampaignInputFactory;
use Fundrik\WordPress\Campaigns\Application\Input\AdminWordPressCampaignPartialInputFactory;
use Fundrik\WordPress\Campaigns\Application\Interfaces\WordPressCampaignServiceInterface;
use InvalidArgumentException;
use RuntimeException;
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
final readonly class WordPressCampaignSyncListener {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressCampaignPostType $post_type Post type definition for campaign posts.
	 * @param AdminWordPressCampaignInputFactory $input_factory Factory to create full input DTOs.
	 * @param AdminWordPressCampaignPartialInputFactory $partial_input_factory Factory to create partial input DTOs.
	 * @param WordPressCampaignServiceInterface $service Service to manage WordPress campaign entities.
	 */
	public function __construct(
		private WordPressCampaignPostType $post_type,
		private AdminWordPressCampaignInputFactory $input_factory,
		private AdminWordPressCampaignPartialInputFactory $partial_input_factory,
		private WordPressCampaignServiceInterface $service,
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
			2,
		);

		add_action(
			'wp_insert_post',
			$this->sync( ... ),
			10,
			2,
		);

		add_action(
			'delete_post',
			$this->delete( ... ),
			10,
			2,
		);
	}

	/**
	 * Validates the incoming request data before inserting a new campaign post.
	 *
	 * @since 1.0.0
	 *
	 * @param stdClass $prepared_post The post object prepared for insertion.
	 * @param WP_REST_Request $request The REST request containing input data.
	 *
	 * @phpstan-param WP_REST_Request<array<string, mixed>> $request
	 *
	 * @return stdClass|WP_Error The validated post object, or WP_Error on failure.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	public function validate( stdClass $prepared_post, WP_REST_Request $request ): stdClass|WP_Error {

		$input_data = $request->get_params();

		try {
			$input = $this->partial_input_factory->from_array( $input_data );
			$this->service->validate_input( $input );
		} catch ( ValidationFailedException ) {
			return new WP_Error(
				'campaign_validation_failed',
				'error',
				[ 'status' => 400 ],
			);
		} catch ( InvalidArgumentException | RuntimeException $e ) {
			return new WP_Error(
				'invalid_campaign_input',
				'Invalid input data: ' . $e->getMessage(),
				[ 'status' => 400 ],
			);
		}

		return $prepared_post;
	}

	// phpcs:disable SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
	/**
	 * Synchronizes a WordPress campaign post with the WordPressCampaign entity.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The ID of the post being synchronized.
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
		} catch ( InvalidArgumentException | RuntimeException $e ) {
			// phpcs:ignore
			error_log( 'Campaign sync input error: ' . $e->getMessage() );
		}
	}
	// phpcs:enable SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter

	/**
	 * Deletes the corresponding WordPressCampaign entity when the related post is deleted.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The ID of the post being deleted.
	 * @param WP_Post $post The post object being deleted.
	 */
	public function delete( int $post_id, WP_Post $post ): void {

		if ( $this->post_type->get_type() !== $post->post_type ) {
			return;
		}

		$this->service->delete_campaign( EntityId::create( $post_id ) );
	}
}
