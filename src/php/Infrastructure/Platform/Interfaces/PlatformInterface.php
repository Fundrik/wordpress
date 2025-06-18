<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Platform\Interfaces;

/**
 * Interface for platform integration within Fundrik.
 *
 * Implementations are responsible for platform-specific initialization,
 * activation hooks, and registration logic (e.g., post types, blocks, listeners).
 *
 * This abstraction allows Fundrik core to remain platform-agnostic.
 *
 * @since 1.0.0
 */
interface PlatformInterface {

	/**
	 * Initializes the platform integration.
	 *
	 * This method should register post types, blocks, listeners, and other
	 * platform-specific features. Typically called during plugin bootstrapping.
	 *
	 * @since 1.0.0
	 */
	public function init(): void;

	/**
	 * Performs setup logic on plugin activation.
	 *
	 * This method is intended for one-time operations like database migrations
	 * or capability assignments.
	 *
	 * @since 1.0.0
	 */
	public function on_activate(): void;
}
