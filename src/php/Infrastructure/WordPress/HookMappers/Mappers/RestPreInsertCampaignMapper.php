<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress\HookMappers\Mappers;

use Fundrik\WordPress\Infrastructure\EventDispatcher\EventDispatcherInterface;
use Fundrik\WordPress\Infrastructure\WordPress\Events\WordPressRestPreInsertCampaignFilterEvent;
use Fundrik\WordPress\Infrastructure\WordPress\HookMappers\HookToEventMapperInterface;
use Fundrik\WordPress\Infrastructure\WordPress\PostTypes\Attributes\PostTypeIdReader;
use Fundrik\WordPress\Infrastructure\WordPress\PostTypes\CampaignPostType;
use Fundrik\WordPress\Infrastructure\WordPress\WordPressContext\WordPressContextFactory;
use InvalidArgumentException;
use stdClass;
use WP_REST_Request;

/**
 * Maps the 'rest_pre_insert_fundrik_campaign' WordPress filter to a Fundrik event.
 *
 * Validates input and dispatches an internal event during REST campaign creation.
 *
 * @since 1.0.0
 *
 * @internal
 */
final class RestPreInsertCampaignMapper implements HookToEventMapperInterface {

	/**
	 * The post type id for the campaign post type.
	 *
	 * @since 1.0.0
	 */
	private string $post_type;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressContextFactory $context_factory Creates WordPressContext instances on demand.
	 * @param EventDispatcherInterface $dispatcher Dispatches the mapped event.
	 * @param PostTypeIdReader $post_type_id_reader Resolves the post type ID for CampaignPostType.
	 */
	public function __construct(
		private readonly WordPressContextFactory $context_factory,
		private readonly EventDispatcherInterface $dispatcher,
		private readonly PostTypeIdReader $post_type_id_reader,
	) {}

	/**
	 * Registers the WordPress hook and maps it to the internal event.
	 *
	 * Skips event dispatching if input is invalid or cannot be normalized.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {

		$context = $this->context_factory->make();
		$this->post_type = $this->post_type_id_reader->get_id( CampaignPostType::class );

		add_filter(
			'rest_pre_insert_' . $this->post_type,
			function ( $prepared_post, $request ) use ( $context ) {

				try {
					$valid_post = $this->validate_prepared_post( $prepared_post );
					$valid_request = $this->validate_request( $request );
				} catch ( InvalidArgumentException $e ) {
					fundrik_log( $e->getMessage() );
					return $prepared_post;
				}

				$event = new WordPressRestPreInsertCampaignFilterEvent( $valid_post, $valid_request, $context );
				$this->dispatcher->dispatch( $event );

				return $event->prepared_post;
			},
			10,
			2,
		);
	}

	/**
	 * Validates the 'prepared_post' argument.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $prepared_post The post object from WordPress.
	 *
	 * @return stdClass The validated post.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	private function validate_prepared_post( mixed $prepared_post ): stdClass {

		if ( ! $prepared_post instanceof stdClass ) {
			throw new InvalidArgumentException(
				"Invalid \$prepared_post argument in 'rest_pre_insert_{$this->post_type}' filter.",
			);
		}

		return $prepared_post;
	}

	/**
	 * Validates the 'request' argument.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $request The incoming REST request.
	 *
	 * @return WP_REST_Request The validated request.
	 *
	 * @phpstan-return WP_REST_Request<array<string, mixed>>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	private function validate_request( mixed $request ): WP_REST_Request {

		if ( ! $request instanceof WP_REST_Request ) {
			throw new InvalidArgumentException(
				"Invalid \$request argument in 'rest_pre_insert_{$this->post_type} filter.",
			);
		}

		return $request;
	}
}
