<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Fixtures;

use Fundrik\WordPress\Infrastructure\Migrations\Files\Abstracts\AbstractMigration;

final readonly class TestMigrationFClassName extends AbstractMigration {

	// phpcs:ignore SlevomatCodingStandard.Functions.DisallowEmptyFunction.EmptyFunction, SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
	public function apply( string $charset_collate ): void {}
}
