<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Fixtures;

use Fundrik\WordPress\Infrastructure\Migrations\Files\Abstracts\AbstractMigration;

final readonly class TestMigration2 extends AbstractMigration {

	public function apply( string $charset_collate ): void {}
}
