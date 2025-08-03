<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress\HookMappers;

/**
 * Provides methods for mapping a specific WordPress hook to an internal event.
 *
 * @since 1.0.0
 *
 * @internal
 */
interface HookToEventMapperInterface {

	/**
	 * Registers a WordPress hook and maps it to an internal event.
	 *
	 * Skips event dispatching if input is invalid.
	 *
	 * @since 1.0.0
	 */
	public function register(): void;
}
