<?php

declare(strict_types=1);

namespace Fundrik\WordPress;

use Fundrik\WordPress\Infrastructure\Container\Container;
use Fundrik\WordPress\Infrastructure\Container\ContainerInterface;
use Fundrik\WordPress\Infrastructure\Container\ServiceBindings;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationRunnerInterface;
use Fundrik\WordPress\Infrastructure\WordPress\WordPressContext;
use Fundrik\WordPress\Infrastructure\WordPress\WordPressEventBridge;
use Illuminate\Container\Container as LaravelContainer;
use Illuminate\Contracts\Container\Container as LaravelContainerInterface;

/**
 * Bootstraps the Fundrik plugin.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class App {

	/**
	 * Private constructor, use factory method.
	 *
	 * @since 1.0.0
	 *
	 * @param ContainerInterface $container Resolves and registers application services.
	 */
	private function __construct(
		private ContainerInterface $container,
	) {}

	/**
	 * Runs the application.
	 *
	 * @since 1.0.0
	 */
	public function run(): void {

		$this->register_bindings();

		$this->container->get( MigrationRunnerInterface::class )->migrate();

		$this->run_wordpress();
	}

	/**
	 * Builds and returns a new App instance with default containers.
	 *
	 * @since 1.0.0
	 *
	 * @return App The application instance ready to run.
	 */
	public static function bootstrap(): self {

		$laravel_container = new LaravelContainer();
		$container = new Container( $laravel_container );

		$container->singleton( ContainerInterface::class, static fn (): ContainerInterface => $container );
		$container->singleton(
			LaravelContainerInterface::class,
			static fn (): LaravelContainerInterface => $laravel_container,
		);

		$container->singleton( ServiceBindings::class );

		return new self( $container );
	}

	/**
	 * Registers service bindings in the container.
	 *
	 * @since 1.0.0
	 */
	private function register_bindings(): void {

		$service_bindings = $this->container->get( ServiceBindings::class );

		$bindings = $service_bindings->get_bindings();

		foreach ( $bindings as $abstract => $concrete ) {

			if ( is_int( $abstract ) ) {
				$abstract = $concrete;
			}

			$this->container->singleton( $abstract, $concrete );
		}
	}

	/**
	 * Boots WordPress-specific infrastructure.
	 *
	 * @since 1.0.0
	 */
	private function run_wordpress(): void {

		$this->container
			->get( WordPressEventBridge::class )
			->register( $this->container->get( WordPressContext::class ) );
	}
}
