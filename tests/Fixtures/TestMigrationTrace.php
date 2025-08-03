<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Fixtures;

final class TestMigrationTrace {

	public static array $calls = [];

	public static function reset(): void {

		self::$calls = [];
	}
}
