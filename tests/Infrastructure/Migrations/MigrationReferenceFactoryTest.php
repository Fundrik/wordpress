<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Migrations;

use Brain\Monkey\Functions;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationReference;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationReferenceFactory;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationValidationResult;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationValidator;
use Fundrik\WordPress\Support\Nspace;
use Fundrik\WordPress\Support\Path;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( MigrationReferenceFactory::class )]
#[UsesClass( MigrationReference::class )]
#[UsesClass( MigrationValidationResult::class )]
#[UsesClass( MigrationValidator::class )]
#[UsesClass( Nspace::class )]
final class MigrationReferenceFactoryTest extends FundrikTestCase {

	private MigrationReferenceFactory $factory;

	protected function setUp(): void {

		parent::setUp();

		$this->factory = new MigrationReferenceFactory(
			new MigrationValidator()
		);
	}

	#[Test]
	public function it_creates_all_references_and_sorts_by_version(): void {

		$directory = Path::PHP_BASE . '../../tests/Fixtures/';

		$migration1 = $directory . '2025_06_15_00_test_migration.php';
		$migration2 = $directory . '2025_06_16_00_test_migration2.php';

		Functions\expect( 'glob' )
			->once()
			->with(
				$this->identicalTo( $directory . '/*.php' )
			)
			->andReturn( [ $migration2, $migration1 ] );

		$result = $this->factory->create_all( $directory );

		$this->assertCount( 2, $result );

		$this->assertSame( '2025_06_15_00', $result[0]->version );
		$this->assertSame( 'Fundrik\WordPress\Tests\Fixtures\TestMigration', $result[0]->class_name );

		$this->assertSame( '2025_06_16_00', $result[1]->version );
		$this->assertSame( 'Fundrik\WordPress\Tests\Fixtures\TestMigration2', $result[1]->class_name );
	}

	#[Test]
	public function it_creates_migration_reference_from_single_file(): void {

		$filepath = Path::PHP_BASE . '../../tests/Fixtures/2025_06_15_00_test_migration.php';

		$reference = $this->factory->create_from_file( $filepath );

		$this->assertInstanceOf( MigrationReference::class, $reference );
		$this->assertSame( '2025_06_15_00', $reference->version );
		$this->assertSame( 'Fundrik\WordPress\Tests\Fixtures\TestMigration', $reference->class_name );
	}
}
