<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Shared\Infrastructure\WordPress;

use Fundrik\WordPress\Shared\Infrastructure\Migrations\MigrationManager;
use Fundrik\WordPress\Shared\Infrastructure\WordPress\Interfaces\WordPressPlatformInterface;

/**
 * Coordinates the WordPress-related bootstrapping logic.
 *
 * @since 1.0.0
 */
final readonly class WordPressPlatform implements WordPressPlatformInterface {

	/**
	 * Constructor.
	 *
	 * @param WordPressContext $context Provides WordPress-specific configuration and state.
	 * @param WordPressEventBridge $event_bridge Connects WordPress hooks to Fundrik's internal event dispatcher.
	 * @param MigrationManager $migration_manager Runs the plugin's database migrations on activation.
	 *
	 * @since 1.0.0
	 */
	public function __construct(
		private WordPressContext $context,
		private WordPressEventBridge $event_bridge,
		private MigrationManager $migration_manager,
	) {}

	/**
	 * Initializes the WordPress platform integration.
	 *
	 * @since 1.0.0
	 */
	public function init(): void {

		$this->event_bridge->register( $this->context );
	}

	/**
	 * Executes the platform-specific logic on plugin activation.
	 *
	 * @since 1.0.0
	 */
	public function on_activate(): void {

		$this->migration_manager->migrate();
	}
}
