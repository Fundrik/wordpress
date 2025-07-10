<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Shared\Infrastructure\WordPress;

use Fundrik\WordPress\Shared\Infrastructure\WordPress\Events\AllowedBlockTypesFilterWordPressEvent;
use Fundrik\WordPress\Shared\Infrastructure\WordPress\Events\InitWordPressEvent;
use Fundrik\WordPress\Shared\Infrastructure\WordPress\Listeners\FilterAllowedBlocksByPostTypeWordPressListener;
use Fundrik\WordPress\Shared\Infrastructure\WordPress\Listeners\RegisterBlocksWordPressListener;
use Fundrik\WordPress\Shared\Infrastructure\WordPress\Listeners\RegisterPostTypesWordPressListener;
use Illuminate\Contracts\Events\Dispatcher;
use WP_Block_Editor_Context;

/**
 * Connects native WordPress hooks to Fundrik's event system.
 *
 * @since 1.0.0
 */
final readonly class WordPressEventBridge {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Dispatcher $dispatcher Broadcasts internal events.
	 */
	public function __construct(
		private Dispatcher $dispatcher,
	) {}

	// phpcs:disable SlevomatCodingStandard.Functions.FunctionLength.FunctionLength
	/**
	 * Registers WordPress hooks and dispatches mapped internal events.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressContext $context The WordPress-specific plugin context.
	 */
	public function register( WordPressContext $context ): void {

		$this->dispatcher->listen( InitWordPressEvent::class, RegisterPostTypesWordPressListener::class );
		$this->dispatcher->listen( InitWordPressEvent::class, RegisterBlocksWordPressListener::class );

		$this->dispatcher->listen(
			AllowedBlockTypesFilterWordPressEvent::class,
			FilterAllowedBlocksByPostTypeWordPressListener::class,
		);

		/**
		 * Fires after all internal event listeners have been registered.
		 *
		 * Allows to register additional listeners before WordPress triggers events.
		 *
		 * @since 1.0.0
		 *
		 * @param Dispatcher $dispatcher The internal event dispatcher.
		 * @param WordPressContext $context The WordPress-specific plugin context.
		 */
		do_action( 'fundrik_wordpress_event_bridge_registered', $this->dispatcher, $context );

		add_action(
			'init',
			fn () => $this->dispatcher->dispatch(
				new InitWordPressEvent( $context ),
			),
		);

		// @todo Remove strict callback argument types because WordPress can pass unexpected types.
		// Inside the dispatcher, catch TypeError exceptions and handle them gracefully,
		// e.g. by logging or displaying admin notices.

		add_filter(
			'allowed_block_types_all',
			function ( bool|array $allowed, WP_Block_Editor_Context $editor_context ) use ( $context ) {

				$event = new AllowedBlockTypesFilterWordPressEvent( $allowed, $editor_context, $context );
				$this->dispatcher->dispatch( $event );

				return $event->allowed;
			},
			10,
			2,
		);
	}
	// phpcs:enable
}
