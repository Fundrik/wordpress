<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Shared\Infrastructure\WordPress\Interfaces;

/**
 * The interface for the WordPress platform integration within Fundrik.
 *
 * Implementations are responsible for the WordPress-specific initialization,
 * activation hooks, and registration logic.
 *
 * @since 1.0.0
 */
interface WordPressPlatformInterface {

	/**
	 * Initializes the WordPress platform integration.
	 *
	 * This method should register the post types, blocks, listeners,
	 * and other WordPress-specific features.
	 * It is typically called during the plugin's bootstrap process.
	 *
	 * @since 1.0.0
	 */
	public function init(): void;

	/**
	 * Executes the setup logic on plugin activation.
	 *
	 * This method is intended for one-time operations such as database migrations,
	 * capability assignments, or other activation-specific tasks.
	 *
	 * @since 1.0.0
	 */
	public function on_activate(): void;
}
