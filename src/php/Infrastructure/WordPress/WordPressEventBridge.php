<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress;

use Fundrik\Core\Support\TypeCaster;
use Fundrik\WordPress\Infrastructure\EventDispatcher\EventDispatcherInterface;
use Fundrik\WordPress\Infrastructure\WordPress\Events\WordPressAllowedBlockTypesFilterEvent;
use Fundrik\WordPress\Infrastructure\WordPress\Events\WordPressInitEvent;
use Fundrik\WordPress\Infrastructure\WordPress\Listeners\FilterAllowedBlocksByPostTypeListener;
use Fundrik\WordPress\Infrastructure\WordPress\Listeners\RegisterBlocksListener;
use Fundrik\WordPress\Infrastructure\WordPress\Listeners\RegisterPostTypesListener;
use InvalidArgumentException;
use WP_Block_Editor_Context;

/**
 * Listens to native WordPress actions and dispatches equivalent Fundrik events.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class WordPressEventBridge {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param EventDispatcherInterface $dispatcher Dispatches mapped internal events.
	 */
	public function __construct(
		private EventDispatcherInterface $dispatcher,
	) {}

	// phpcs:disable SlevomatCodingStandard.Functions.FunctionLength.FunctionLength
	/**
	 * Registers WordPress event hooks and dispatches equivalent Fundrik events.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressContext $context Provides access to WordPress state and metadata.
	 */
	public function register( WordPressContext $context ): void {

		$this->dispatcher->listen( WordPressInitEvent::class, RegisterPostTypesListener::class );
		$this->dispatcher->listen( WordPressInitEvent::class, RegisterBlocksListener::class );

		$this->dispatcher->listen(
			WordPressAllowedBlockTypesFilterEvent::class,
			FilterAllowedBlocksByPostTypeListener::class,
		);

		add_action(
			'init',
			fn () => $this->dispatcher->dispatch(
				new WordPressInitEvent( $context ),
			),
		);

		add_filter(
			'allowed_block_types_all',
			fn ( $allowed, $editor_context ) => $this->filter_allowed_block_types_all(
				$allowed,
				$editor_context,
				$context,
			),
			10,
			2,
		);
	}
	// phpcs:enable

	// Temporary phpcs fix. See @todo.
	// phpcs:disable SlevomatCodingStandard.Complexity.Cognitive.ComplexityTooHigh
	/**
	 * Filters and validates the allowed block types list from WordPress.
	 *
	 * Normalizes the input, validates types, and dispatches the internal filter event.
	 * Falls back to the original value if the input is invalid.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $allowed The value from WordPress core indicating allowed blocks.
	 * @param mixed $editor_context The editor context passed by WordPress.
	 * @param WordPressContext $context The internal plugin context.
	 *
	 * @return mixed The final allowed blocks value after filtering or fallback.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 *
	 * @todo Extract normalization and validation logic into a dedicated class
	 *       to reduce cognitive complexity and improve separation of concerns.
	 */
	private function filter_allowed_block_types_all(
		mixed $allowed,
		mixed $editor_context,
		WordPressContext $context,
	): mixed {

		if ( is_array( $allowed ) ) {

			try {
				$allowed = array_map( TypeCaster::to_string( ... ), $allowed );
			} catch ( InvalidArgumentException $e ) {
				fundrik_log( 'Invalid block type list: ' . $e->getMessage() );
				return $allowed;
			}
		} elseif ( $allowed !== true && $allowed !== false ) {
			fundrik_log( 'Invalid $allowed type in allowed_block_types_all filter.' );
			return $allowed;
		}

		if ( ! $editor_context instanceof WP_Block_Editor_Context ) {
			fundrik_log( 'Invalid $editor_context in allowed_block_types_all filter.' );
			return $allowed;
		}

		$event = new WordPressAllowedBlockTypesFilterEvent( $allowed, $editor_context, $context );
		$this->dispatcher->dispatch( $event );

		return $event->allowed;
	}
	// phpcs:enable
}
