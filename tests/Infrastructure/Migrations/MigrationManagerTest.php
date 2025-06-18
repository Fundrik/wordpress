<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Migrations;

use Brain\Monkey\Functions;
use Fundrik\Core\Infrastructure\Interfaces\ContainerInterface;
use Fundrik\WordPress\Infrastructure\Container\ContainerRegistry;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationManager;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationReference;
use Fundrik\WordPress\Infrastructure\Migrations\Files\Abstracts\AbstractMigration;
use Fundrik\WordPress\Infrastructure\Migrations\Interfaces\MigrationReferenceFactoryInterface;
use Fundrik\WordPress\Support\Path;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesFunction;
use RuntimeException;
use stdClass;
use wpdb;

#[CoversClass( MigrationManager::class )]
#[UsesClass( ContainerRegistry::class )]
#[UsesClass( MigrationReference::class )]
#[UsesClass( Path::class )]
#[UsesFunction( 'fundrik' )]
final class MigrationManagerTest extends FundrikTestCase {

	private const OPTION_KEY                = 'fundrik_migration_version';
	private const DEFAULT_MIGRATION_VERSION = '0000_00_00_00';
	private const CHARSET_COLLATE           = 'CHARSET';

	private wpdb&MockInterface $wpdb;
	private MigrationReferenceFactoryInterface&MockInterface $reference_factory;
	private ContainerInterface&MockInterface $container;

	private MigrationManager $manager;

	protected function setUp(): void {

		parent::setUp();

		$this->wpdb              = Mockery::mock( wpdb::class );
		$this->reference_factory = Mockery::mock( MigrationReferenceFactoryInterface::class );
		$this->container         = Mockery::mock( ContainerInterface::class );

		ContainerRegistry::set( $this->container );

		$this->manager = new MigrationManager(
			$this->wpdb,
			$this->reference_factory,
		);

		$this->wpdb
			->shouldReceive( 'get_charset_collate' )
			->once()
			->andReturn( self::CHARSET_COLLATE );
	}

	#[Test]
	public function it_applies_new_migration_and_updates_version(): void {

		$current_version = '2025_01_01_00';

		$reference1 = new MigrationReference( '2025_01_01_00', 'MigrationOld' );
		$reference2 = new MigrationReference( '2025_06_01_00', 'MigrationNew' );

		Functions\expect( 'get_option' )
			->once()
			->with(
				$this->identicalTo( self::OPTION_KEY ),
				$this->identicalTo( self::DEFAULT_MIGRATION_VERSION )
			)
			->andReturn( $current_version );

		$this->reference_factory
			->shouldReceive( 'create_all' )
			->once()
			->with( Mockery::type( 'string' ) )
			->andReturn( [ $reference1, $reference2 ] );

		// @todo Replace with Mockery::mock() once https://github.com/mockery/mockery/pull/1319 is fixed.
		$migration_old = $this->createMock( AbstractMigration::class );
		$migration_new = $this->createMock( AbstractMigration::class );
		$migration_new
			->expects( $this->once() )
			->method( 'apply' )
			->with( $this->identicalTo( self::CHARSET_COLLATE ) );

		$this->container->shouldReceive( 'get' )
			->with( 'MigrationOld' )
			->andReturn( $migration_old );
		$this->container->shouldReceive( 'get' )
			->with( 'MigrationNew' )
			->andReturn( $migration_new );

		Functions\expect( 'update_option' )
			->once()
			->with(
				$this->identicalTo( self::OPTION_KEY ),
				'2025_06_01_00',
				false
			)
			->andReturnTrue();

		$this->manager->migrate();
	}

	#[Test]
	public function it_skips_migrations_with_version_less_or_equal_to_current(): void {

		$current_version = '2025_06_02_00';

		$reference1 = new MigrationReference( '2025_06_01_00', 'MigrationOld' );
		$reference2 = new MigrationReference( '2025_06_02_00', 'MigrationCurrent' );

		Functions\expect( 'get_option' )
			->once()
			->with(
				$this->identicalTo( self::OPTION_KEY ),
				$this->identicalTo( self::DEFAULT_MIGRATION_VERSION )
			)
			->andReturn( $current_version );

		$this->reference_factory
			->shouldReceive( 'create_all' )
			->once()
			->with( Mockery::type( 'string' ) )
			->andReturn( [ $reference1, $reference2 ] );

		Functions\expect( 'update_option' )->never();

		$this->manager->migrate();
	}

	#[Test]
	public function it_throws_if_migration_is_not_instance_of_abstract_migration(): void {

		$current_version = self::DEFAULT_MIGRATION_VERSION;
		$reference       = new MigrationReference( '2025_06_16_00', 'MigrationNew' );

		Functions\expect( 'get_option' )
			->once()
			->with(
				$this->identicalTo( self::OPTION_KEY ),
				$this->identicalTo( self::DEFAULT_MIGRATION_VERSION )
			)
			->andReturn( $current_version );

		$this->reference_factory
			->shouldReceive( 'create_all' )
			->once()
			->with( Mockery::type( 'string' ) )
			->andReturn( [ $reference ] );

		$this->container->shouldReceive( 'get' )
			->with( 'MigrationNew' )
			->andReturn( new stdClass() );

		$this->expectException( RuntimeException::class );

		$this->manager->migrate();
	}
}
