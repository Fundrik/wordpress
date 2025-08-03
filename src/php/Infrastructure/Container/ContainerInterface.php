<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Container;

use Closure;

/**
 * Provides methods for resolving, instantiating, and binding dependencies.
 *
 * @since 1.0.0
 *
 * @internal
 */
interface ContainerInterface {

	/**
	 * Resolves and returns the instance bound to the given identifier.
	 *
	 * Ensures the resolved object matches the expected type, otherwise throws.
	 *
	 * @since 1.0.0
	 *
	 * @template T of object
	 *
	 * @phpstan-param class-string<T> $id
	 *
	 * @phpstan-return T
	 *
	 * @param string $id The class or interface name to resolve.
	 *
	 * @return object The resolved instance matching the given identifier.
	 */
	public function get( string $id ): object;

	/**
	 * Instantiates a class or interface, optionally with constructor parameters.
	 *
	 * Ensures the created instance matches the expected type, otherwise throws.
	 *
	 * @since 1.0.0
	 *
	 * @template T of object
	 *
	 * @phpstan-param class-string<T> $id
	 *
	 * @phpstan-return T
	 *
	 * @param string $id The class or interface name to instantiate.
	 * @param array<string, mixed> $parameters Optional constructor parameters.
	 *
	 * @return object The newly created instance matching the expected type.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	public function make( string $id, array $parameters = [] ): object;

	/**
	 * Checks whether a binding exists for the given identifier.
	 *
	 * @since 1.0.0
	 *
	 * @phpstan-param class-string $id
	 *
	 * @param string $id The class or interface name to check.
	 *
	 * @return bool True if the binding exists in the container, false otherwise.
	 */
	public function has( string $id ): bool;

	/**
	 * Registers a singleton binding into the container.
	 *
	 * - If $concrete is `null`, the container instantiates `$abstract` directly.
	 * - If $concrete is a `string`, the container resolves it when `$abstract` is requested.
	 * - If $concrete is a `Closure`, the result is cached and reused.
	 *
	 * @since 1.0.0
	 *
	 * @phpstan-param class-string $abstract
	 * @phpstan-param Closure|class-string|null $concrete
	 *
	 * @param string $abstract The class or interface name to bind.
	 * @param Closure|string|null $concrete The implementation or factory to bind, or null to use the abstract.
	 */
	public function singleton(
		// phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.abstractFound
		string $abstract,
		Closure|string|null $concrete = null,
	): void;

	/**
	 * Registers an existing instance as a singleton binding.
	 *
	 * @since 1.0.0
	 *
	 * @phpstan-param class-string $abstract
	 *
	 * @param string $abstract The class or interface name to bind.
	 * @param object $instance The already constructed instance.
	 */
	public function instance(
		// phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.abstractFound
		string $abstract,
		object $instance,
	): void;
}
