<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress\Listeners;

use Fundrik\WordPress\Application;

/**
 * Registers all custom blocks.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class RegisterBlocksListener {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Application $application Provides access to plugin-level paths and configuration.
	 */
	public function __construct(
		private Application $application,
	) {}

	/**
	 * Handles the given event.
	 *
	 * @since 1.0.0
	 */
	public function handle(): void {

		wp_register_block_types_from_metadata_collection(
			$this->application->get_blocks_path(),
			$this->application->get_blocks_manifest_path(),
		);
	}
}
