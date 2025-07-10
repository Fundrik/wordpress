<?php

declare(strict_types=1);

namespace Fundrik\WordPress;

use Fundrik\WordPress\Shared\Infrastructure\Container\ServiceBindings;
use Fundrik\WordPress\Shared\Infrastructure\WordPress\Interfaces\WordPressPlatformInterface;

/**
 * Bootstrapps for the Fundrik plugin.
 *
 * @since 1.0.0
 */
final readonly class App {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param ServiceBindings $service_bindings Supplies all service bindings to be registered into the container.
	 */
	public function __construct(
		private ServiceBindings $service_bindings,
	) {}

	/**
	 * Runs the application.
	 *
	 * @since 1.0.0
	 */
	public function run(): void {

		$this->register_bindings();

		$this->platform()->init();
	}

	/**
	 * Handles the plugin activation.
	 *
	 * @since 1.0.0
	 */
	public function activate(): void {

		$this->run();

		$this->platform()->on_activate();
	}

	/**
	 * Registers bindings from a dependency provider into the container.
	 *
	 * @since 1.0.0
	 */
	private function register_bindings(): void {

		$bindings = $this->service_bindings->get_bindings();

		foreach ( $bindings as $abstract => $concrete ) {

			fundrik()->singleton( $abstract, $concrete );
		}
	}

	/**
	 * Returns the WordPress platform integration instance.
	 *
	 * Cannot be injected via constructor because the platform binding is
	 * not available until after {@see App::register_bindings()} is called.
	 *
	 * @since 1.0.0
	 *
	 * @return WordPressPlatformInterface The resolved platform integration instance.
	 */
	private function platform(): WordPressPlatformInterface {

		return fundrik()->get( WordPressPlatformInterface::class );
	}
}
