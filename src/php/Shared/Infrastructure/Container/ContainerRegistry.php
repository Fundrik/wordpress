<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Shared\Infrastructure\Container;

use Fundrik\Core\Infrastructure\Interfaces\ContainerInterface;
use RuntimeException;

/**
 * Registry for the global Fundrik container instance.
 *
 * Provides static access to the shared DI container.
 *
 * @since 1.0.0
 *
 * @internal
 */
final class ContainerRegistry {

	/**
	 * The shared container instance.
	 */
	private static ?ContainerInterface $container = null;

	/**
	 * Returns the current container instance.
	 *
	 * @since 1.0.0
	 *
	 * @return ContainerInterface The current shared container instance.
	 */
	public static function get(): ContainerInterface {

		if ( self::$container === null ) {
			throw new RuntimeException( 'Container instance is not set.' );
		}

		return self::$container;
	}

	/**
	 * Sets the container instance.
	 *
	 * Used in bootstrap or tests to inject a concrete container implementation.
	 *
	 * @since 1.0.0
	 *
	 * @param ContainerInterface|null $container The container to set, or null to clear.
	 */
	public static function set( ?ContainerInterface $container ): void {

		self::$container = $container;
	}
}
