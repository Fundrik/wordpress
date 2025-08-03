<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Migrations;

use Fundrik\WordPress\Infrastructure\Migrations\MigrationVersion;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationVersionReader;
use Fundrik\WordPress\Tests\Fixtures\EmptyVersionMigration;
use Fundrik\WordPress\Tests\Fixtures\InvalidVersionPrefixMigration;
use Fundrik\WordPress\Tests\Fixtures\InvalidVersionSuffixMigration;
use Fundrik\WordPress\Tests\Fixtures\OldMigration;
use Fundrik\WordPress\Tests\Fixtures\UnversionedMigration;
use Fundrik\WordPress\Tests\Fixtures\WhitespacedVersionMigration;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use RuntimeException;

#[CoversClass( MigrationVersionReader::class )]
#[UsesClass( MigrationVersion::class )]
final class MigrationVersionReaderTest extends FundrikTestCase {

	#[Test]
	public function it_reads_the_version_from_a_class_with_attribute(): void {

		$reader = new MigrationVersionReader();

		$version = $reader->get_version( OldMigration::class );

		$this->assertSame( '2000_01_16_00', $version );
	}

	#[Test]
	public function it_throws_if_attribute_is_missing(): void {

		$reader = new MigrationVersionReader();

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'is missing #[MigrationVersion] attribute' );

		$reader->get_version( UnversionedMigration::class );
	}

	#[Test]
	public function it_throws_if_version_is_empty(): void {

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'has an empty #[MigrationVersion] value' );

		$reader = new MigrationVersionReader();
		$reader->get_version( EmptyVersionMigration::class );
	}

	#[Test]
	public function it_throws_if_version_is_only_whitespace(): void {

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'has an empty #[MigrationVersion] value' );

		$reader = new MigrationVersionReader();
		$reader->get_version( WhitespacedVersionMigration::class );
	}

	#[Test]
	public function it_throws_if_version_has_invalid_format_due_to_prefix(): void {

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'has an invalid #[MigrationVersion] format' );

		$reader = new MigrationVersionReader();
		$reader->get_version( InvalidVersionPrefixMigration::class );
	}

	#[Test]
	public function it_throws_if_version_has_invalid_format_due_to_suffix(): void {

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'has an invalid #[MigrationVersion] format' );

		$reader = new MigrationVersionReader();
		$reader->get_version( InvalidVersionSuffixMigration::class );
	}
}
