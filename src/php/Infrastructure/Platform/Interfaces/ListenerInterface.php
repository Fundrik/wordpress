<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Platform\Interfaces;

/**
 * Interface for classes that register WordPress hooks or listeners.
 *
 * @since 1.0.0
 */
interface ListenerInterface {

	/**
	 * Registers necessary WordPress hooks or actions.
	 *
	 * @since 1.0.0
	 */
	public function register(): void;
}
