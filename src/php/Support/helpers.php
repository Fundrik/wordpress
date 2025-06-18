<?php
/**
 * Helper functions for Fundrik plugin.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

use Fundrik\Core\Infrastructure\Interfaces\ContainerInterface;
use Fundrik\WordPress\Infrastructure\Container\ContainerRegistry;

/**
 * Retrieves the Fundrik container instance.
 *
 * This function provides access to the Fundrik dependency injection container.
 * The container is managed internally and reused across the plugin lifecycle.
 *
 * @since 1.0.0
 *
 * @return ContainerInterface The instance of the Fundrik container.
 */
function fundrik(): ContainerInterface {

	return ContainerRegistry::get();
}
