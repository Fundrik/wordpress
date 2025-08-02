<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\EventDispatcher;

use Illuminate\Contracts\Events\Dispatcher as IlluminateDispatcher;

/**
 * Dispatches events and registers listeners.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class EventDispatcher implements EventDispatcherInterface {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param IlluminateDispatcher $inner Dispatches events using Laravel's event system.
	 */
	public function __construct(
		private IlluminateDispatcher $inner,
	) {}

	/**
	 * Dispatches an event to all registered listeners.
	 *
	 * @since 1.0.0
	 *
	 * @param object $event The event object to dispatch.
	 */
	public function dispatch( object $event ): void {

		$this->inner->dispatch( $event );
	}

	/**
	 * Registers a listener for the given event class.
	 *
	 * @since 1.0.0
	 *
	 * @param string $event_class The class name of the event to listen for.
	 * @param string $listener_class The class name of the listener that handles the event.
	 */
	public function listen( string $event_class, string $listener_class ): void {

		$this->inner->listen( $event_class, $listener_class );
	}
}
