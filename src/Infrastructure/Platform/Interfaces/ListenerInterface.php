<?php
/**
 * Defines an interface for classes that register WordPress hooks or listeners.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Platform\Interfaces;

interface ListenerInterface {

	/**
	 * Registers necessary WordPress hooks or actions.
	 *
	 * @since 1.0.0
	 */
	public function register(): void;
}
