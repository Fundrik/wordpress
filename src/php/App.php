<?php

declare(strict_types=1);

namespace Fundrik\WordPress;

use Fundrik\Core\Infrastructure\Interfaces\ContainerInterface;
use Fundrik\Core\Infrastructure\Interfaces\DependencyProviderInterface;
use Fundrik\WordPress\Infrastructure\Container\ContainerRegistry;
use Fundrik\WordPress\Infrastructure\Platform\Interfaces\PlatformInterface;

/**
 * Bootstraps and initializes the plugin's core components.
 *
 * @since 1.0.0
 */
final readonly class App {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param DependencyProviderInterface $provider Dependency provider.
	 */
	public function __construct(
		private DependencyProviderInterface $provider,
	) {}

	/**
	 * Runs the application.
	 *
	 * Registers container bindings and initializes the platform.
	 *
	 * @since 1.0.0
	 */
	public function run(): void {

		$this->register_bindings( $this->provider );

		$this->platform()->init();
	}

	/**
	 * Handles plugin activation.
	 *
	 * @since 1.0.0
	 */
	public function activate(): void {

		$this->run();

		$this->platform()->on_activate();
	}

	/**
	 * Returns the Fundrik dependency injection container.
	 *
	 * @since 1.0.0
	 *
	 * @return ContainerInterface The instance of the Fundrik container.
	 */
	public function container(): ContainerInterface {

		return ContainerRegistry::get();
	}

	/**
	 * Returns the platform integration instance.
	 *
	 * @since 1.0.0
	 *
	 * @return PlatformInterface The platform integration instance.
	 */
	public function platform(): PlatformInterface {

		return $this->container()->get( PlatformInterface::class );
	}

	// phpcs:disable SlevomatCodingStandard.Complexity.Cognitive.ComplexityTooHigh
	/**
	 * Registers bindings from a dependency provider into the container.
	 *
	 * @since 1.0.0
	 *
	 * @param DependencyProviderInterface $provider The dependency provider.
	 * @param string $category Optional category of bindings.
	 */
	public function register_bindings( DependencyProviderInterface $provider, string $category = '' ): void {

		$bindings = $provider->get_bindings( $category );

		foreach ( $bindings as $abstract => $concrete ) {

			if ( is_array( $concrete ) ) {

				foreach ( $concrete as $a => $c ) {
					$this->container()->singleton( $a, $c );
				}
			} else {
				$this->container()->singleton( $abstract, $concrete );
			}
		}
	}
	// phpcs:enable SlevomatCodingStandard.Complexity.Cognitive.ComplexityTooHigh
}
