<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Container;

use Closure;
use Fundrik\Core\Infrastructure\Interfaces\ContainerInterface;
use Illuminate\Container\Container as IlluminateContainer;

/**
 * Fundrik Dependency Injection Container.
 *
 * Adapter over Laravel's service container that implements the core container interface.
 * This container is used internally within the WordPress-specific layer to manage
 * dependency resolution and singleton registrations.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class Container implements ContainerInterface {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param IlluminateContainer $inner The Laravel container instance used for resolution.
	 */
	public function __construct(
		private IlluminateContainer $inner
	) {}

	/**
	 * Resolves a class or interface by its identifier.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Class or interface name.
	 *
	 * @return object The resolved instance.
	 */
	public function get( string $id ): object {

		return $this->inner->get( $id );
	}

	/**
	 * Checks whether the given identifier has been bound.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Class or interface name.
	 *
	 * @return bool True if the binding exists, false otherwise.
	 */
	public function has( string $id ): bool {

		return $this->inner->has( $id );
	}

	/**
	 * Registers a singleton binding in the container.
	 *
	 * If the concrete implementation is:
	 * - `null`: the container will instantiate `$abstract` directly.
	 * - `string`: the container will resolve the given class name when `$abstract` is requested.
	 * - `Closure`: the closure will be executed once and reused.
	 *
	 * @since 1.0.0
	 *
	 * @param string              $abstract Class or interface name.
	 * @param Closure|string|null $concrete Optional implementation or factory closure.
	 */
	public function singleton(
		// phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.abstractFound
		string $abstract,
		Closure|string|null $concrete = null
	): void {

		$this->inner->singleton( $abstract, $concrete );
	}
}
