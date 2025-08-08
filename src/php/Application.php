<?php

declare(strict_types=1);

namespace Fundrik\WordPress;

use Fundrik\WordPress\Infrastructure\Container\ContainerInterface;
use Fundrik\WordPress\Infrastructure\EventDispatcher\EventListenerRegistrarInterface;
use Fundrik\WordPress\Infrastructure\Helpers\PluginPath;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationRunnerInterface;
use Fundrik\WordPress\Infrastructure\Integration\HookMappers\HookMapperRegistrarInterface;

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
	 * Builds and returns a new App instance using the given container.
	 *
	 * @since 1.0.0
	 *
	 * @param ContainerInterface $container Provides access to resolved application services.
	 *
	 * @return self The application instance ready to run.
	 */
	public static function bootstrap( ContainerInterface $container ): self {

		return new self(
			$container->get( EventListenerRegistrarInterface::class ),
			$container->get( MigrationRunnerInterface::class ),
			$container->get( HookMapperRegistrarInterface::class ),
		);
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
