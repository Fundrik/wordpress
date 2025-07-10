<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests;

use Fundrik\Core\Infrastructure\Interfaces\ContainerInterface;
use Fundrik\Core\Infrastructure\Interfaces\DependencyProviderInterface;
use Fundrik\WordPress\App;
use Fundrik\WordPress\Infrastructure\Container\ContainerRegistry;
use Fundrik\WordPress\Infrastructure\Interfaces\PlatformInterface;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use RuntimeException;
use stdClass;

#[CoversClass( App::class )]
#[UsesClass( ContainerRegistry::class )]
final class AppTest extends FundrikTestCase {

	private ContainerInterface&MockInterface $container;
	private DependencyProviderInterface&MockInterface $provider;
	private PlatformInterface&MockInterface $platform;
	private App $app;

	protected function setUp(): void {

		parent::setUp();

		$this->container = Mockery::mock( ContainerInterface::class );
		$this->provider = Mockery::mock( DependencyProviderInterface::class );
		$this->platform = Mockery::mock( PlatformInterface::class );

		ContainerRegistry::set( $this->container );

		$this->app = new App( $this->provider );
	}

	#[Test]
	public function it_registers_bindings_and_initializes_platform_on_run(): void {

		$this->container
			->shouldReceive( 'get' )
			->once()
			->with( PlatformInterface::class )
			->andReturn( $this->platform );

		$bindings = [
			'default' => [
				'SomeInterface' => static fn () => new stdClass(),
			],
			'Grouped' => [
				'Nested1' => 'NestedImpl1',
				'Nested2' => 'NestedImpl2',
			],
		];

		$this->provider
			->shouldReceive( 'get_bindings' )
			->once()
			->with( '' )
			->andReturn( $bindings );

		$this->container
			->shouldReceive( 'singleton' )
			->once()
			->with( 'SomeInterface', Mockery::type( 'callable' ) );

		$this->container
			->shouldReceive( 'singleton' )
			->once()
			->with( 'Nested1', 'NestedImpl1' );

		$this->container
			->shouldReceive( 'singleton' )
			->once()
			->with( 'Nested2', 'NestedImpl2' );

		$this->platform
			->shouldReceive( 'init' )
			->once();

		$this->app->run();
	}

	#[Test]
	public function it_runs_and_executes_platform_activation_on_activate(): void {

		$this->container
			->shouldReceive( 'get' )
			->twice()
			->with( PlatformInterface::class )
			->andReturn( $this->platform );

		$this->provider
			->shouldReceive( 'get_bindings' )
			->once()
			->with( '' )
			->andReturn( [] );

		$this->container
			->shouldReceive( 'singleton' )
			->never();

		$this->platform
			->shouldReceive( 'init' )
			->once();

		$this->platform
			->shouldReceive( 'on_activate' )
			->once();

		$this->app->activate();
	}

	#[Test]
	public function it_returns_container_from_registry(): void {

		$this->assertSame( $this->container, $this->app->container() );
	}

	#[Test]
	public function platform_returns_platform_interface_instance(): void {

		$this->container
			->shouldReceive( 'get' )
			->once()
			->with( PlatformInterface::class )
			->andReturn( $this->platform );

		$result = $this->app->platform();

		$this->assertSame( $this->platform, $result );
	}

	#[Test]
	public function platform_throws_if_container_returns_invalid_instance(): void {

		$invalid_object = new stdClass();

		$this->container
			->shouldReceive( 'get' )
			->once()
			->with( PlatformInterface::class )
			->andReturn( $invalid_object );

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage(
			// phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong
			'Container returned an instance of stdClass, but Fundrik\WordPress\Infrastructure\Interfaces\PlatformInterface expected.',
		);

		$this->app->platform();
	}

	#[Test]
	public function register_bindings_registers_singletons(): void {

		$bindings = [
			'default' => [
				'abstract1' => static fn () => (object) [ 'tag' => 'stdClass1' ],
			],
			'group' => [
				'abstract2a' => static fn () => (object) [ 'tag' => 'stdClass2a' ],
				'abstract2b' => static fn () => (object) [ 'tag' => 'stdClass2b' ],
			],
		];

		$this->provider
			->shouldReceive( 'get_bindings' )
			->once()
			->with( '' )
			->andReturn( $bindings );

		$this->container
			->shouldReceive( 'singleton' )
			->with(
				'abstract1',
				Mockery::on(
					static function ( $func ) {
						$result = $func();
						return is_object( $result ) && $result->tag === 'stdClass1';
					},
				),
			)
			->once();

		$this->container
			->shouldReceive( 'singleton' )
			->with(
				'abstract2a',
				Mockery::on(
					static function ( $func ) {
						$result = $func();
						return is_object( $result ) && $result->tag === 'stdClass2a';
					},
				),
			)
			->once();

		$this->container
			->shouldReceive( 'singleton' )
			->with(
				'abstract2b',
				Mockery::on(
					static function ( $func ) {
						$result = $func();
						return is_object( $result ) && $result->tag === 'stdClass2b';
					},
				),
			)
			->once();

		$this->app->register_bindings( $this->provider );
	}

	#[Test]
	public function register_bindings_passes_category_to_provider(): void {

		$category = 'platform';

		$bindings = [
			'default' => [
				'some.abstract' => static fn () => new stdClass(),
			],
		];

		$this->provider
			->shouldReceive( 'get_bindings' )
			->once()
			->with( $category )
			->andReturn( $bindings );

		$this->container
			->shouldReceive( 'singleton' )
			->once()
			->with( 'some.abstract', Mockery::type( 'callable' ) );

		$this->app->register_bindings( $this->provider, $category );
	}
}
