<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Integration\HookMappers\Mappers;

use Fundrik\WordPress\Infrastructure\EventDispatcher\EventDispatcherInterface;
use Fundrik\WordPress\Infrastructure\Helpers\LoggerFormatter;
use Fundrik\WordPress\Infrastructure\Integration\Events\WordPressDeletePost;
use Fundrik\WordPress\Infrastructure\Integration\HookMappers\HookToEventMapperInterface;
use Fundrik\WordPress\Infrastructure\Integration\HookMappers\Mappers\Exceptions\InvalidMapperArgumentException;
use Fundrik\WordPress\Infrastructure\Integration\WordPressContext\WordPressContextFactory;
use Fundrik\WordPress\Infrastructure\Integration\WordPressContext\WordPressContextInterface;
use Psr\Log\LoggerInterface;
use WP_Post;

/**
 * Maps the 'delete_post' WordPress action to a Fundrik event.
 *
 * Dispatches an event when a post is deleted.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class DeletePostMapper implements HookToEventMapperInterface {

	private const HOOK_NAME = 'delete_post';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressContextFactory $context_factory Creates WordPressContext instances on demand.
	 * @param EventDispatcherInterface $dispatcher Dispatches the mapped event.
	 * @param LoggerInterface $logger Logs validation errors and mapping-related warnings.
	 */
	public function __construct(
		private WordPressContextFactory $context_factory,
		private EventDispatcherInterface $dispatcher,
		private LoggerInterface $logger,
	) {}

	/**
	 * Registers the 'delete_post' WordPress action and maps it to the internal event.
	 *
	 * Validates the hook arguments and dispatches an event if they are valid; otherwise, skips processing.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {

		$context = $this->context_factory->make();

		add_action(
			self::HOOK_NAME,
			fn ( $post_id, $post ) => $this->handle_hook( $post_id, $post, $context ),
			10,
			2,
		);
	}

	/**
	 * Handles the 'delete_post' action logic.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $post_id Post ID.
	 * @param mixed $post Post object.
	 * @param WordPressContextInterface $context The WordPress-specific plugin context.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	private function handle_hook( mixed $post_id, mixed $post, WordPressContextInterface $context ): void {

		try {
			$valid_post_id = $this->validate_post_id( $post_id );
			$valid_post = $this->validate_post( $post );
		} catch ( InvalidMapperArgumentException $e ) {
			$this->logger->warning(
				$e->getMessage(),
				LoggerFormatter::hook_mapper_context( hook: self::HOOK_NAME, mapper: self::class ),
			);
			return;
		}

		$this->dispatcher->dispatch(
			new WordPressDeletePost(
				post_id: $valid_post_id,
				post: $valid_post,
				context: $context,
			),
		);
	}

	/**
	 * Validates the 'post_id' argument.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $post_id The incoming post ID.
	 *
	 * @return int The validated post ID.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	private function validate_post_id( mixed $post_id ): int {

		if ( ! is_int( $post_id ) ) {
			throw InvalidMapperArgumentException::create( argument: 'post_id', hook: self::HOOK_NAME );
		}

		return $post_id;
	}

	/**
	 * Validates the 'post' argument.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $post The incoming post object.
	 *
	 * @return WP_Post The validated post.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	private function validate_post( mixed $post ): WP_Post {

		if ( ! $post instanceof WP_Post ) {
			throw InvalidMapperArgumentException::create( argument: 'post', hook: self::HOOK_NAME );
		}

		return $post;
	}
}
