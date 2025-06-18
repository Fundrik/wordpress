<?php

declare(strict_types=1);

namespace Fundrik\WordPress;

use Fundrik\Core\App as CoreApp;
use Fundrik\Core\Infrastructure\Internal\Container;
use Fundrik\WordPress\Infrastructure\DependencyProvider;
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
	 * @param CoreApp            $core     The core application instance.
	 * @param DependencyProvider $provider Dependency provider.
	 */
	public function __construct(
		private CoreApp $core,
		private DependencyProvider $provider,
	) {}

	/**
	 * Runs the application.
	 *
	 * @since 1.0.0
	 */
	public function run(): void {

		$this->core->register_bindings( $this->provider );

		$this->platform()->init();
	}

	/**
	 * Handles plugin activation.
	 *
	 * @since 1.0.0
	 */
	public function activate() {

		$this->run();

		$this->platform()->on_activate();
	}

	/**
	 * Returns the Fundrik dependency injection container.
	 *
	 * @since 1.0.0
	 *
	 * @return Container The instance of the Fundrik container.
	 */
	public function container(): Container {

		return $this->core->container();
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
}
