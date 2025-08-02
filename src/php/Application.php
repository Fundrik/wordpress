<?php

declare(strict_types=1);

namespace Fundrik\WordPress;

use Fundrik\WordPress\Infrastructure\Container\Container;
use Fundrik\WordPress\Infrastructure\Container\ContainerInterface;
use Fundrik\WordPress\Infrastructure\Container\ServiceBindings;
use Fundrik\WordPress\Infrastructure\EventDispatcher\EventListenerRegistrarInterface;
use Fundrik\WordPress\Infrastructure\Helpers\PluginPath;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationRunnerInterface;
use Fundrik\WordPress\Infrastructure\WordPress\HookMappers\HookMapperRegistrarInterface;
use Illuminate\Container\Container as LaravelContainer;
use Illuminate\Contracts\Container\Container as LaravelContainerInterface;

/**
 * Bootstraps the Fundrik plugin.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class Application {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param EventListenerRegistrarInterface $event_listener_registrar Registers application event listeners.
	 * @param MigrationRunnerInterface $migration_runner Applies database schema migrations.
	 * @param HookMapperRegistrarInterface $hook_mapper_registrar Registers WordPress hook-to-event mappers.
	 */
	public function __construct(
		private EventListenerRegistrarInterface $event_listener_registrar,
		private MigrationRunnerInterface $migration_runner,
		private HookMapperRegistrarInterface $hook_mapper_registrar,
	) {}

	/**
	 * Runs the application.
	 *
	 * @since 1.0.0
	 */
	public function run(): void {

		$this->migration_runner->migrate();

		$this->event_listener_registrar->register_all();

		$this->run_wordpress();
	}

	/**
	 * Returns the path to the custom Gutenberg blocks directory.
	 *
	 * @since 1.0.0
	 *
	 * @return string The absolute path to the block source directory.
	 */
	public function get_blocks_path(): string {

		return PluginPath::Blocks->get_full_path();
	}

	/**
	 * Returns the path to the block manifest file.
	 *
	 * @since 1.0.0
	 *
	 * @return string The absolute path to the PHP block manifest file.
	 */
	public function get_blocks_manifest_path(): string {

		return PluginPath::BlocksManifest->get_full_path();
	}

	/**
	 * Builds and returns a new App instance with default containers.
	 *
	 * @since 1.0.0
	 *
	 * @return self The application instance ready to run.
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

		self::register_bindings( $container );

		return new self(
			$container->get( EventListenerRegistrarInterface::class ),
			$container->get( MigrationRunnerInterface::class ),
			$container->get( HookMapperRegistrarInterface::class ),
		);
	}

	/**
	 * Registers service bindings in the container.
	 *
	 * @since 1.0.0
	 *
	 * @param ContainerInterface $container Provides access to the service container for binding resolution.
	 */
	private static function register_bindings( ContainerInterface $container ): void {

		$service_bindings = $container->get( ServiceBindings::class );

		$bindings = $service_bindings->get_bindings();

		foreach ( $bindings as $abstract => $concrete ) {

			if ( is_int( $abstract ) ) {
				$abstract = $concrete;
			}

			$container->singleton( $abstract, $concrete );
		}
	}

	/**
	 * Boots WordPress-specific infrastructure.
	 *
	 * @since 1.0.0
	 */
	private function run_wordpress(): void {

		$this->hook_mapper_registrar->register_all();
	}
}
