<?php
/**
 * Interface for providing synchronization functionality for a custom post type.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Platform\Interfaces;

interface SyncProviderInterface {

	/**
	 * Registers the post synchronization listener for the custom post type.
	 *
	 * @since 1.0.0
	 */
	public function register(): void;
}
