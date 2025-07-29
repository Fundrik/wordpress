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
	 * @param string $id The class or interface name to resolve.
	 *
	 * @template T of object
	 *
	 * @phpstan-param class-string<T> $id
	 *
	 * @phpstan-return T
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
	 * @param string $id The class or interface name to instantiate.
	 * @param array<string, mixed> $parameters Optional constructor parameters.
	 *
	 * @phpstan-param class-string $id
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
	 * @param string $id The class or interface name to check.
	 *
	 * @phpstan-param class-string $id
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
	 * @param string $abstract The class or interface name to bind.
	 * @param Closure|string|null $concrete The implementation or factory to bind, or null to use the abstract.
	 *
	 * @phpstan-param class-string<object> $abstract
	 */
	public function singleton(
		// phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.abstractFound
		string $abstract,
		Closure|string|null $concrete = null,
	): void;
}
