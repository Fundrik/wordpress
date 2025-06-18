<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests;

use Fundrik\Core\App as CoreApp;
use Fundrik\Core\Infrastructure\Internal\ContainerManager;
use Fundrik\WordPress\App;
use Fundrik\WordPress\Infrastructure\DependencyProvider;
use Fundrik\WordPress\Infrastructure\Platform\Interfaces\PlatformInterface;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use stdClass;

#[CoversClass( App::class )]
final class AppTest extends FundrikTestCase {

	private PlatformInterface&MockInterface $platform;
	private DependencyProvider&MockInterface $provider;

	private CoreApp $core;
	private App $app;

	protected function setUp(): void {

		parent::setUp();

		$this->core = new CoreApp();

		$this->platform = Mockery::mock( PlatformInterface::class );
		$this->provider = Mockery::mock( DependencyProvider::class );

		ContainerManager::get_fresh();
		fundrik()->singleton( PlatformInterface::class, fn() => $this->platform );

		$this->app = new App(
			$this->core,
			$this->provider
		);
	}

	#[Test]
	public function it_registers_bindings_and_initializes_platform_on_run(): void {

		$this->provider
			->shouldReceive( 'get_bindings' )
			->andReturn(
				[
					'abstract1' => fn() => new stdClass(),
				]
			);

		$this->platform
			->shouldReceive( 'init' )
			->once();

		$this->app->run();

		$container = $this->app->container();

		$this->assertInstanceOf( stdClass::class, $container->get( 'abstract1' ) );
	}

	#[Test]
	public function it_runs_and_executes_platform_activation_on_activate(): void {

		$this->provider
			->shouldReceive( 'get_bindings' )
			->andReturn( [] );

		$this->platform
			->shouldReceive( 'init' )
			->once();

		$this->platform
			->shouldReceive( 'on_activate' )
			->once();

		$this->app->activate();
	}

	#[Test]
	public function it_returns_core_container(): void {
		$this->assertSame( $this->core->container(), $this->app->container() );
	}
}
