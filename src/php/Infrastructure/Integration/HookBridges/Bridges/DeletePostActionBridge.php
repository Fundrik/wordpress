<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Integration\HookBridges\Bridges;

use Fundrik\WordPress\Infrastructure\EventDispatcher\EventDispatcherInterface;
use Fundrik\WordPress\Infrastructure\Helpers\LoggerFormatter;
use Fundrik\WordPress\Infrastructure\Integration\Events\PostDeletedEvent;
use Fundrik\WordPress\Infrastructure\Integration\HookBridges\HookToEventBridgeInterface;
use Fundrik\WordPress\Infrastructure\Integration\HookBridges\InvalidBridgeArgumentException;
use Fundrik\WordPress\Infrastructure\Integration\WordPressContext\WordPressContextFactory;
use Psr\Log\LoggerInterface;
use WP_Post;

/**
 * Bridges the WordPress 'delete_post' action to internal integration events.
 *
 * Validates the action input before dispatching an internal event.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class DeletePostActionBridge implements HookToEventBridgeInterface {

	private const HOOK_NAME = 'delete_post';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressContextFactory $context_factory Creates WordPressContext instances on demand.
	 * @param EventDispatcherInterface $dispatcher Dispatches the bridged events.
	 * @param LoggerInterface $logger Logs validation errors and bridging-related warnings.
	 */
	public function __construct(
		private WordPressContextFactory $context_factory,
		private EventDispatcherInterface $dispatcher,
		private LoggerInterface $logger,
	) {}

	/**
	 * Registers the 'delete_post' WordPress action and bridge it to the internal events.
	 *
	 * Validates the hook arguments and dispatches an event if they are valid; otherwise, skips processing.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {

		add_action(
			self::HOOK_NAME,
			$this->handle( ... ),
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
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	public function handle( mixed $post_id, mixed $post ): void {

		try {
			$valid_post_id = $this->validate_post_id( $post_id );
			$valid_post = $this->validate_post( $post );
		} catch ( InvalidBridgeArgumentException $e ) {
			$this->logger->warning(
				$e->getMessage(),
				LoggerFormatter::hook_bridge_context( hook: self::HOOK_NAME, bridge: self::class ),
			);
			return;
		}

		$this->dispatcher->dispatch(
			new PostDeletedEvent(
				post_id: $valid_post_id,
				post: $valid_post,
				context: $this->context_factory->create(),
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
			throw InvalidBridgeArgumentException::create( argument: 'post_id', hook: self::HOOK_NAME );
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
			throw InvalidBridgeArgumentException::create( argument: 'post', hook: self::HOOK_NAME );
		}

		return $post;
	}
}
