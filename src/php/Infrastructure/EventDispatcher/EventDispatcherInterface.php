<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\EventDispatcher;

/**
 * Provides methods for dispatching events and registering listeners.
 *
 * @since 1.0.0
 *
 * @internal
 */
interface EventDispatcherInterface {

	/**
	 * Dispatches an event to all registered listeners.
	 *
	 * @since 1.0.0
	 *
	 * @param object $event The event object to dispatch.
	 */
	public function dispatch( object $event ): void;

	/**
	 * Registers a listener for the given event class.
	 *
	 * @since 1.0.0
	 *
	 * @param string $event_class The class name of the event to listen for.
	 * @param string $listener_class The class name of the listener that handles the event.
	 *
	 * @phpstan-param class-string $event_class
	 * @phpstan-param class-string $listener_class
	 */
	public function listen( string $event_class, string $listener_class ): void;
}
