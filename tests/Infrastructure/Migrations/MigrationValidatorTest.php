<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Migrations;

use Fundrik\WordPress\Infrastructure\Migrations\MigrationValidator;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationValidationResult;
use Fundrik\WordPress\Support\Nspace;
use Fundrik\WordPress\Support\Path;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass( MigrationValidator::class )]
#[UsesClass( MigrationValidationResult::class )]
#[UsesClass( Nspace::class )]
final class MigrationValidatorTest extends TestCase {

	private MigrationValidator $validator;

	protected function setUp(): void {
		$this->validator = new MigrationValidator();
	}

	#[Test]
	public function it_validates_valid_migration_file(): void {

		$filepath = Path::PHP_BASE . '../../tests/Fixtures/2025_06_15_00_test_migration.php';

		$result = $this->validator->validate_by_filepath( $filepath );

		$this->assertInstanceOf( MigrationValidationResult::class, $result );
		$this->assertSame( '2025_06_15_00', $result->version );
		$this->assertSame(
			'Fundrik\WordPress\Tests\Fixtures\TestMigration',
			$result->class_name
		);
	}


	#[Test]
	public function it_throws_if_filename_format_is_invalid(): void {

		$filepath = '/path/to/invalid_name.php';

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( "Invalid migration file name format: expected 'YYYY_MM_DD_XX_name', got 'invalid_name'" );

		$this->validator->validate_by_filepath( $filepath );
	}


	#[Test]
	public function it_throws_if_class_does_not_exist(): void {

		$filepath = Path::PHP_BASE . '../../tests/Fixtures/2025_06_15_00_test_migration_failed_class_name.php';

		$this->expectException( RuntimeException::class );

		$this->validator->validate_by_filepath( $filepath );
	}

	#[Test]
	public function it_throws_if_class_does_not_extend_abstract_migration(): void {

		$filepath = Path::PHP_BASE . '../../tests/Fixtures/2025_06_15_00_test_migration_failed_extend.php';

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( "Migration class 'TestMigrationFailedExtend' must extend AbstractMigration" );

		$this->validator->validate_by_filepath( $filepath );
	}
}
