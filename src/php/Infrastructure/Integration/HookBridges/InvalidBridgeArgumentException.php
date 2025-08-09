<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Integration\HookBridges;

use InvalidArgumentException;

/**
 * Indicates a type or structure mismatch in arguments passed by WordPress to the bridged hook.
 *
 * @since 1.0.0
 */
final class InvalidBridgeArgumentException extends InvalidArgumentException {

	/**
	 * Creates a standardized exception for invalid arguments passed to a bridged hook.
	 *
	 * @since 1.0.0
	 *
	 * @param string $argument The name of the invalid argument (without `$`).
	 * @param string $hook The name of the hook where the argument was encountered.
	 *
	 * @return self The constructed exception describing the invalid hook argument.
	 */
	public static function create( string $argument, string $hook ): self {

		return new self( "Invalid \${$argument} argument in '{$hook}' hook." );
	}
}
