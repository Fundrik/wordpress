<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Container;

use Fundrik\WordPress\Infrastructure\Container\Container;
use Fundrik\WordPress\Tests\MockeryTestCase;
use Illuminate\Contracts\Container\Container as LaravelContainerInterface;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use stdClass;

#[CoversClass( Container::class )]
final class ContainerTest extends MockeryTestCase {

	private Container $container;
	private LaravelContainerInterface&MockInterface $inner;

	protected function setUp(): void {

		parent::setUp();

		$this->inner = Mockery::mock( LaravelContainerInterface::class );
		$this->container = new Container( $this->inner );
	}

	#[Test]
	public function get_delegates_to_inner_container(): void {

		$instance = new stdClass();

		$this->inner
			->shouldReceive( 'get' )
			->once()
			->with( $this->identicalTo( $instance::class ) )
			->andReturn( $instance );

		$result = $this->container->get( $instance::class );

		$this->assertSame( $instance, $result );
	}

	#[Test]
	public function get_throws_if_resolved_instance_does_not_implement_expected_type(): void {

		$this->inner
			->shouldReceive( 'get' )
			->once()
			->with( 'MyClass' )
			->andReturn( 'not_an_object' );

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'Container returned instance of string, but expected implementation of MyClass.' );

		$this->container->get( 'MyClass' );
	}

	#[Test]
	public function has_delegates_to_inner_container(): void {

		$this->inner
			->shouldReceive( 'has' )
			->once()
			->with( 'MyClass' )
			->andReturn( true );

		$result = $this->container->has( 'MyClass' );

		$this->assertTrue( $result );
	}

	#[Test]
	public function singleton_registers_closure_binding(): void {

		$closure = static fn () => new stdClass();

		$this->inner
			->shouldReceive( 'singleton' )
			->once()
			->with(
				'MyService',
				$this->identicalTo( $closure ),
			);

		$this->container->singleton( 'MyService', $closure );
	}

	#[Test]
	public function singleton_registers_string_binding(): void {

		$this->inner
			->shouldReceive( 'singleton' )
			->once()
			->with( 'MyService', 'MyImplementation' );

		$this->container->singleton( 'MyService', 'MyImplementation' );
	}

	#[Test]
	public function singleton_registers_self_binding_when_null(): void {

		$this->inner
			->shouldReceive( 'singleton' )
			->once()
			->with( 'MyService', null );

		$this->container->singleton( 'MyService' );
	}

	#[Test]
	public function make_delegates_to_inner_container_without_parameters(): void {

		$instance = new stdClass();

		$this->inner
		->shouldReceive( 'make' )
			->once()
			->with(
				$this->identicalTo( $instance::class ),
				[],
			)
			->andReturn( $instance );

		$result = $this->container->make( $instance::class );

		$this->assertSame( $instance, $result );
	}

	#[Test]
	public function make_delegates_to_inner_container_with_parameters(): void {

		$instance = new stdClass();

		$params = [
			'id' => 123,
			'name' => 'Test',
		];

		$this->inner
		->shouldReceive( 'make' )
			->once()
			->with(
				$this->identicalTo( $instance::class ),
				$this->identicalTo( $params ),
			)
			->andReturn( $instance );

		$result = $this->container->make( $instance::class, $params );

		$this->assertSame( $instance, $result );
	}

	#[Test]
	public function make_throws_if_created_instance_does_not_implement_expected_type(): void {

		$this->inner
			->shouldReceive( 'make' )
			->once()
			->with( 'MyClass', [] )
			->andReturn( 42 );

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'Container made instance of int, but expected implementation of MyClass.' );

		$this->container->make( 'MyClass' );
	}
}
