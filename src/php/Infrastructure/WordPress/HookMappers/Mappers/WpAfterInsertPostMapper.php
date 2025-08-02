<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress\HookMappers\Mappers;

use Fundrik\WordPress\Infrastructure\EventDispatcher\EventDispatcherInterface;
use Fundrik\WordPress\Infrastructure\WordPress\Events\WordPressWpAfterInsertPost;
use Fundrik\WordPress\Infrastructure\WordPress\HookMappers\HookToEventMapperInterface;
use Fundrik\WordPress\Infrastructure\WordPress\WordPressContext\WordPressContextFactory;
use InvalidArgumentException;
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

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressContextFactory $context_factory Creates WordPressContext instances on demand.
	 * @param EventDispatcherInterface $dispatcher Dispatches the mapped event.
	 */
	public function __construct(
		private WordPressContextFactory $context_factory,
		private EventDispatcherInterface $dispatcher,
	) {}

	// phpcs:disable SlevomatCodingStandard.Functions.FunctionLength.FunctionLength
	/**
	 * Registers the WordPress hook and maps it to the internal event.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {

		$context = $this->context_factory->make();

		add_action(
			'wp_after_insert_post',
			function ( $post_id, $post, $update, $post_before ) use ( $context ): void {

				try {
					$valid_post_id = $this->validate_post_id( $post_id );
					$valid_post = $this->validate_post( $post );
					$valid_update = $this->validate_update( $update );
					$valid_post_before = $this->validate_post_before( $post_before );
				} catch ( InvalidArgumentException $e ) {
					fundrik_log( $e->getMessage() );
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
			},
			10,
			4,
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
			throw new InvalidArgumentException( "Invalid \$post_id argument in 'wp_after_insert_post' action." );
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
			throw new InvalidArgumentException( "Invalid \$post argument in 'wp_after_insert_post' action." );
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
			throw new InvalidArgumentException( "Invalid \$update argument in 'wp_after_insert_post' action." );
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
			throw new InvalidArgumentException( "Invalid \$post_before argument in 'wp_after_insert_post' action." );
		}

		return $post_before;
	}
}
