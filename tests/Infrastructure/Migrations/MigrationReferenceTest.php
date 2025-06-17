<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Migrations;

use Fundrik\WordPress\Infrastructure\Migrations\MigrationReference;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( MigrationReference::class )]
final class MigrationReferenceTest extends FundrikTestCase {

	#[Test]
	public function it_initializes_with_version_and_class_name(): void {

		$version    = '2025_06_16_01';
		$class_name = 'SomeMigrationClass';

		$migration_reference = new MigrationReference( $version, $class_name );

		$this->assertSame( $version, $migration_reference->version );
		$this->assertSame( $class_name, $migration_reference->class_name );
	}
}
