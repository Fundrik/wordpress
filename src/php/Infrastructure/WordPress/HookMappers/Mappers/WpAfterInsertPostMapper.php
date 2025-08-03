<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress\HookMappers\Mappers;

use Fundrik\WordPress\Infrastructure\EventDispatcher\EventDispatcherInterface;
use Fundrik\WordPress\Infrastructure\Helpers\LoggerFormatter;
use Fundrik\WordPress\Infrastructure\WordPress\Events\WordPressWpAfterInsertPost;
use Fundrik\WordPress\Infrastructure\WordPress\HookMappers\HookToEventMapperInterface;
use Fundrik\WordPress\Infrastructure\WordPress\HookMappers\Mappers\Exceptions\InvalidMapperArgumentException;
use Fundrik\WordPress\Infrastructure\WordPress\WordPressContext\WordPressContextFactory;
use Fundrik\WordPress\Infrastructure\WordPress\WordPressContext\WordPressContextInterface;
use Psr\Log\LoggerInterface;
use WP_Post;

/**
 * Maps the 'wp_after_insert_post' WordPress action to a Fundrik event.
 *
 * Dispatches an event after a post is inserted or updated.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class WpAfterInsertPostMapper implements HookToEventMapperInterface {

	private const HOOK_NAME = 'wp_after_insert_post';

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
	 * Registers the 'wp_after_insert_post' WordPress action and maps it to an internal event.
	 *
	 * Validates the hook arguments and dispatches an event if they are valid; otherwise, skips processing.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {

		$context = $this->context_factory->make();

		add_action(
			self::HOOK_NAME,
			fn ( $post_id, $post, $update, $post_before ) => $this->handle_hook(
				$post_id,
				$post,
				$update,
				$post_before,
				$context,
			),
			10,
			4,
		);
	}

	// phpcs:disable SlevomatCodingStandard.Functions.FunctionLength.FunctionLength
	/**
	 * Handles the 'wp_after_insert_post' action logic.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $post_id Post ID.
	 * @param mixed $post Post object.
	 * @param mixed $update Whether this is an existing post being updated.
	 * @param mixed $post_before Null for new posts, the WP_Post object prior
	 *                                  to the update for updated posts.
	 * @param WordPressContextInterface $context The WordPress-specific plugin context.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	private function handle_hook(
		mixed $post_id,
		mixed $post,
		mixed $update,
		mixed $post_before,
		WordPressContextInterface $context,
	): void {

		try {
			$valid_post_id = $this->validate_post_id( $post_id );
			$valid_post = $this->validate_post( $post );
			$valid_update = $this->validate_update( $update );
			$valid_post_before = $this->validate_post_before( $post_before );
		} catch ( InvalidMapperArgumentException $e ) {
			$this->logger->warning(
				$e->getMessage(),
				LoggerFormatter::hook_mapper_context( hook: self::HOOK_NAME, mapper: self::class ),
			);
			return;
		}

		$this->dispatcher->dispatch(
			new WordPressWpAfterInsertPost(
				post_id: $valid_post_id,
				post: $valid_post,
				update: $valid_update,
				post_before: $valid_post_before,
				context: $context,
			),
		);
	}
	// phpcs:enable

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

	/**
	 * Validates the 'update' argument.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $update The incoming update flag.
	 *
	 * @return bool The validated update flag.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	private function validate_update( mixed $update ): bool {

		if ( ! is_bool( $update ) ) {
			throw InvalidMapperArgumentException::create( argument: 'update', hook: self::HOOK_NAME );
		}

		return $update;
	}

	/**
	 * Validates the 'post_before' argument.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $post_before The incoming pre-update post object.
	 *
	 * @return WP_Post|null The validated object or null.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	private function validate_post_before( mixed $post_before ): ?WP_Post {

		if ( $post_before !== null && ! $post_before instanceof WP_Post ) {
			throw InvalidMapperArgumentException::create( argument: 'post_before', hook: self::HOOK_NAME );
		}

		return $post_before;
	}
}
