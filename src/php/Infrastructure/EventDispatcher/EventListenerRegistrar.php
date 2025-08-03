<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\EventDispatcher;

use Fundrik\WordPress\Infrastructure\WordPress\Events\WordPressAllowedBlockTypesAllFilterEvent;
use Fundrik\WordPress\Infrastructure\WordPress\Events\WordPressInitEvent;
use Fundrik\WordPress\Infrastructure\WordPress\Listeners\FilterAllowedBlocksByPostTypeListener;
use Fundrik\WordPress\Infrastructure\WordPress\Listeners\RegisterBlocksListener;
use Fundrik\WordPress\Infrastructure\WordPress\Listeners\RegisterPostTypesListener;

/**
 * Registers all event listeners.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class EventListenerRegistrar implements EventListenerRegistrarInterface {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param EventDispatcherInterface $dispatcher Registers event listeners.
	 */
	public function __construct(
		private EventDispatcherInterface $dispatcher,
	) {}

	/**
	 * Registers all declared event listeners.
	 *
	 * @since 1.0.0
	 */
	public function register_all(): void {

		$this->dispatcher->listen( WordPressInitEvent::class, RegisterPostTypesListener::class );
		$this->dispatcher->listen( WordPressInitEvent::class, RegisterBlocksListener::class );

		$this->dispatcher->listen(
			WordPressAllowedBlockTypesAllFilterEvent::class,
			FilterAllowedBlocksByPostTypeListener::class,
		);
	}
}
