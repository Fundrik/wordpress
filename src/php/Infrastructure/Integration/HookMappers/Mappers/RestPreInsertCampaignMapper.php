<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Integration\HookMappers\Mappers;

use Fundrik\WordPress\Infrastructure\EventDispatcher\EventDispatcherInterface;
use Fundrik\WordPress\Infrastructure\Helpers\LoggerFormatter;
use Fundrik\WordPress\Infrastructure\Integration\Events\WordPressRestPreInsertCampaignFilterEvent;
use Fundrik\WordPress\Infrastructure\Integration\HookMappers\HookToEventMapperInterface;
use Fundrik\WordPress\Infrastructure\Integration\HookMappers\Mappers\Exceptions\InvalidMapperArgumentException;
use Fundrik\WordPress\Infrastructure\Integration\PostTypes\Attributes\PostTypeIdReader;
use Fundrik\WordPress\Infrastructure\Integration\PostTypes\CampaignPostType;
use Fundrik\WordPress\Infrastructure\Integration\WordPressContext\WordPressContextFactory;
use Fundrik\WordPress\Infrastructure\Integration\WordPressContext\WordPressContextInterface;
use Psr\Log\LoggerInterface;
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
	 * @param LoggerInterface $logger Logs validation errors and mapping-related warnings.
	 */
	public function __construct(
		private readonly WordPressContextFactory $context_factory,
		private readonly EventDispatcherInterface $dispatcher,
		private readonly PostTypeIdReader $post_type_id_reader,
		private LoggerInterface $logger,
	) {}

	/**
	 * Registers the 'rest_pre_insert_(post_type)' WordPress filter and maps it to the internal event.
	 *
	 * Validates the hook arguments and dispatches an event if they are valid; otherwise, skips processing.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {

		$context = $this->context_factory->make();
		$this->post_type = $this->post_type_id_reader->get_id( CampaignPostType::class );

		add_filter(
			$this->get_hook_name(),
			fn ( $prepared_post, $request ) => $this->handle_hook( $prepared_post, $request, $context ),
			10,
			2,
		);
	}

	/**
	 * Handles the 'rest_pre_insert_(post_type)' filter logic for campaigns.
	 *
	 * @since 1.0.0
	 *
	 * // phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong, SlevomatCodingStandard.Commenting.DocCommentSpacing.IncorrectLinesCountBetweenDifferentAnnotationsTypes
	 * @param mixed $prepared_post An object representing a single post prepared for inserting or updating the database.
	 * @param mixed $request Request object.
	 * @param WordPressContextInterface $context The WordPress-specific plugin context.
	 *
	 * @return mixed The modified filtered post object or the original value if validation fails.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	private function handle_hook( mixed $prepared_post, mixed $request, WordPressContextInterface $context ): mixed {

		try {
			$valid_post = $this->validate_prepared_post( $prepared_post );
			$valid_request = $this->validate_request( $request );
		} catch ( InvalidMapperArgumentException $e ) {
			$this->logger->warning(
				$e->getMessage(),
				LoggerFormatter::hook_mapper_context( hook: $this->get_hook_name(), mapper: self::class ),
			);

			return $prepared_post;
		}

		$event = new WordPressRestPreInsertCampaignFilterEvent(
			prepared_post: $valid_post,
			request: $valid_request,
			context: $context,
		);

		$this->dispatcher->dispatch( $event );

		return $event->prepared_post;
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
			throw InvalidMapperArgumentException::create( argument: 'prepared_post', hook: $this->get_hook_name() );
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
			throw InvalidMapperArgumentException::create( argument: 'request', hook: $this->get_hook_name() );
		}

		return $request;
	}

	/**
	 * Returns the dynamic name of the REST pre-insert hook for the campaign post type.
	 *
	 * @since 1.0.0
	 *
	 * @return string The name of the WordPress hook to map.
	 */
	private function get_hook_name(): string {

		return 'rest_pre_insert_' . $this->post_type;
	}
}
