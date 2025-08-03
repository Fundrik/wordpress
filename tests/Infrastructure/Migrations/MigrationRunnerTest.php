<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Migrations;

use Fundrik\WordPress\Infrastructure\Container\ContainerInterface;
use Fundrik\WordPress\Infrastructure\DatabaseInterface;
use Fundrik\WordPress\Infrastructure\Helpers\LoggerFormatter;
use Fundrik\WordPress\Infrastructure\Migrations\AbstractMigration;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationRegistry;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationRunner;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationVersion;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationVersionReader;
use Fundrik\WordPress\Infrastructure\StorageInterface;
use Fundrik\WordPress\Tests\Fixtures\NewMigration1;
use Fundrik\WordPress\Tests\Fixtures\NewMigration2;
use Fundrik\WordPress\Tests\Fixtures\OldMigration;
use Fundrik\WordPress\Tests\Fixtures\TestMigrationTrace;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use RuntimeException;

#[CoversClass( MigrationRunner::class )]
#[UsesClass( AbstractMigration::class )]
#[UsesClass( MigrationVersion::class )]
#[UsesClass( MigrationVersionReader::class )]
#[UsesClass( LoggerFormatter::class )]
final class MigrationRunnerTest extends FundrikTestCase {

	private ContainerInterface&MockObject $container;
	private DatabaseInterface&MockObject $database;
	private StorageInterface&MockObject $storage;
	private MigrationRegistry&MockObject $registry;
	private LoggerInterface&MockObject $logger;
	private MigrationVersionReader $version_reader;
	private MigrationRunner $runner;

	protected function setUp(): void {

		parent::setUp();

		$this->container = $this->createMock( ContainerInterface::class );
		$this->database = $this->createMock( DatabaseInterface::class );
		$this->storage = $this->createMock( StorageInterface::class );
		$this->registry = $this->createMock( MigrationRegistry::class );
		$this->logger = $this->createMock( LoggerInterface::class );
		$this->version_reader = new MigrationVersionReader();

		$this->runner = new MigrationRunner(
			$this->container,
			$this->database,
			$this->storage,
			$this->version_reader,
			$this->registry,
			$this->logger,
		);

		$this->registry
			->expects( $this->once() )
			->method( 'get_target_db_version' )
			->willReturn( '2025_06_15_00' );
	}

	#[Test]
	public function it_skips_migration_if_current_version_is_equal(): void {

		$this->storage
			->expects( $this->once() )
			->method( 'get' )
			->with( 'fundrik_db_version', '0000_00_00_00' )
			->willReturn( '2025_06_15_00' );

		$this->database
			->expects( $this->never() )
			->method( 'get_charset_collate' );

		$this->runner->migrate();
	}

	#[Test]
	public function it_skips_migration_if_current_version_is_newer(): void {

		$this->storage
			->expects( $this->once() )
			->method( 'get' )
			->with( 'fundrik_db_version', '0000_00_00_00' )
			->willReturn( '2400_01_01_00' );

		$this->database
			->expects( $this->never() )
			->method( 'get_charset_collate' );

		$this->runner->migrate();
	}

	#[Test]
	public function it_applies_pending_migrations_in_correct_order_and_updates_db_version(): void {

		TestMigrationTrace::reset();

		$charset_collate = 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci';

		$this->storage
			->expects( $this->once() )
			->method( 'get' )
			->with( 'fundrik_db_version', '0000_00_00_00' )
			->willReturn( '2025_06_14_00' );

		$old = new OldMigration( $this->database );
		$new1 = new NewMigration1( $this->database );
		$new2 = new NewMigration2( $this->database );

		$old_class = $old::class;
		$new1_class = $new1::class;
		$new2_class = $new2::class;

		$this->registry
			->expects( $this->once() )
			->method( 'get_migration_classes' )
			->willReturn( [ $old_class, $new2_class, $new1_class ] ); // wrong order.

		$this->container
			->method( 'get' )
			->willReturnCallback(
				static fn ( string $class ) => match ( $class ) {
					$new1_class => $new1,
					$new2_class => $new2,
					default => throw new RuntimeException( "Unexpected class requested: $class" ),
				},
			);

		$this->database
			->expects( $this->once() )
			->method( 'get_charset_collate' )
			->willReturn( $charset_collate );

		$this->storage
			->expects( $this->exactly( 2 ) )
			->method( 'set' )
			->willReturn( true );

		$this->runner->migrate();

		$this->assertSame(
			[ NewMigration1::class, NewMigration2::class ],
			TestMigrationTrace::$calls,
		);
	}

	#[Test]
	public function it_logs_warning_if_version_update_fails(): void {

		TestMigrationTrace::reset();

		$migration = new NewMigration1( $this->database );
		$migration_class = $migration::class;

		$this->storage
			->expects( $this->once() )
			->method( 'get' )
			->with( 'fundrik_db_version', '0000_00_00_00' )
			->willReturn( '0000_00_00_00' );

		$this->registry
			->expects( $this->once() )
			->method( 'get_migration_classes' )
			->willReturn( [ $migration_class ] );

		$this->container
			->expects( $this->once() )
			->method( 'get' )
			->with( $migration_class )
			->willReturn( $migration );

		$this->database
			->expects( $this->once() )
			->method( 'get_charset_collate' )
			->willReturn( 'utf8mb4_unicode_ci' );

		$this->storage
			->expects( $this->once() )
			->method( 'set' )
			->with( 'fundrik_db_version', $this->version_reader->get_version( $migration_class ) )
			->willReturn( false );

		$this->logger
			->expects( $this->once() )
			->method( 'warning' )
			->with(
				'Failed to update stored DB version after migration.',
				[
					'migration_class' => $migration_class,
					'migration_version' => $this->version_reader->get_version( $migration_class ),
				],
			);

		$this->runner->migrate();
	}
}
