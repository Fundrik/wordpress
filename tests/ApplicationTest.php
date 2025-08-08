<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests;

use Fundrik\WordPress\Application;
use Fundrik\WordPress\Infrastructure\Container\ContainerInterface;
use Fundrik\WordPress\Infrastructure\EventDispatcher\EventListenerRegistrarInterface;
use Fundrik\WordPress\Infrastructure\Helpers\PluginPath;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationRunnerInterface;
use Fundrik\WordPress\Infrastructure\Integration\HookMappers\HookMapperRegistrarInterface;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( Application::class )]
#[UsesClass( PluginPath::class )]
final class ApplicationTest extends MockeryTestCase {

	private EventListenerRegistrarInterface&MockInterface $event_listener_registrar;
	private MigrationRunnerInterface&MockInterface $migration_runner;
	private HookMapperRegistrarInterface&MockInterface $hook_mapper_registrar;
	private Application $app;

	protected function setUp(): void {

		parent::setUp();

		$this->event_listener_registrar = Mockery::mock( EventListenerRegistrarInterface::class );
		$this->migration_runner = Mockery::mock( MigrationRunnerInterface::class );
		$this->hook_mapper_registrar = Mockery::mock( HookMapperRegistrarInterface::class );

		$this->app = new Application(
			$this->event_listener_registrar,
			$this->migration_runner,
			$this->hook_mapper_registrar,
		);
	}

	#[Test]
	public function it_runs_migrations_and_registers_listeners_and_hooks(): void {

		$this->migration_runner
			->shouldReceive( 'migrate' )
			->once();

		$this->event_listener_registrar
			->shouldReceive( 'register_all' )
			->once();

		$this->hook_mapper_registrar
			->shouldReceive( 'register_all' )
			->once();

		$this->app->run();
	}

	#[Test]
	public function it_returns_the_correct_blocks_path(): void {

		$expected = PluginPath::Blocks->get_full_path();

		$this->assertSame( $expected, $this->app->get_blocks_path() );
	}

	#[Test]
	public function it_returns_the_correct_blocks_manifest_path(): void {

		$expected = PluginPath::BlocksManifest->get_full_path();

		$this->assertSame( $expected, $this->app->get_blocks_manifest_path() );
	}

	#[Test]
	public function it_bootstraps_application_with_container(): void {

		$event_listener = Mockery::mock( EventListenerRegistrarInterface::class );
		$migration_runner = Mockery::mock( MigrationRunnerInterface::class );
		$hook_mapper = Mockery::mock( HookMapperRegistrarInterface::class );

		$container = Mockery::mock( ContainerInterface::class );

		$container
			->shouldReceive( 'get' )
			->once()
			->with( EventListenerRegistrarInterface::class )
			->andReturn( $event_listener );

		$container
			->shouldReceive( 'get' )
			->once()
			->with( MigrationRunnerInterface::class )
			->andReturn( $migration_runner );

		$container
			->shouldReceive( 'get' )
			->once()
			->with( HookMapperRegistrarInterface::class )
			->andReturn( $hook_mapper );

		$app = Application::bootstrap( $container );

		$this->assertInstanceOf( Application::class, $app );
	}
}
