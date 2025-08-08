<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Integration\HookMappers;

/**
 * Provides a method for registering all hook-to-event mappers.
 *
 * @since 1.0.0
 */
interface HookMapperRegistrarInterface {

	/**
	 * Registers all declared hook-to-event mappers.
	 *
	 * @since 1.0.0
	 */
	public function register_all(): void;
}
