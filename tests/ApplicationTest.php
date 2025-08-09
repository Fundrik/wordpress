<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests;

use Fundrik\WordPress\Application;
use Fundrik\WordPress\Infrastructure\Container\ContainerInterface;
use Fundrik\WordPress\Infrastructure\EventDispatcher\EventListenerRegistrarInterface;
use Fundrik\WordPress\Infrastructure\Helpers\PluginPath;
use Fundrik\WordPress\Infrastructure\Integration\HookBridges\HookBridgeRegistrarInterface;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationRunnerInterface;
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
	private HookBridgeRegistrarInterface&MockInterface $hook_bridge_registrar;
	private Application $app;

	protected function setUp(): void {

		parent::setUp();

		$this->event_listener_registrar = Mockery::mock( EventListenerRegistrarInterface::class );
		$this->migration_runner = Mockery::mock( MigrationRunnerInterface::class );
		$this->hook_bridge_registrar = Mockery::mock( HookBridgeRegistrarInterface::class );

		$this->app = new Application(
			$this->event_listener_registrar,
			$this->migration_runner,
			$this->hook_bridge_registrar,
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

		$this->hook_bridge_registrar
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

		$container = Mockery::mock( ContainerInterface::class );

		$container
			->shouldReceive( 'get' )
			->once()
			->with( EventListenerRegistrarInterface::class )
			->andReturn( $this->event_listener_registrar );

		$container
			->shouldReceive( 'get' )
			->once()
			->with( MigrationRunnerInterface::class )
			->andReturn( $this->migration_runner );

		$container
			->shouldReceive( 'get' )
			->once()
			->with( HookBridgeRegistrarInterface::class )
			->andReturn( $this->hook_bridge_registrar );

		$app = Application::bootstrap( $container );

		$this->assertInstanceOf( Application::class, $app );
	}
}
