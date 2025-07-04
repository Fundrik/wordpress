<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Container;

use Fundrik\WordPress\Infrastructure\Container\Container;
use Illuminate\Container\Container as IlluminateContainer;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

#[CoversClass( Container::class )]
final class ContainerTest extends TestCase {

	private Container $container;
	private IlluminateContainer&MockInterface $inner;

	protected function setUp(): void {

		parent::setUp();

		$this->inner = Mockery::mock( IlluminateContainer::class );
		$this->container = new Container( $this->inner );
	}

	#[Test]
	public function get_delegates_to_inner_container(): void {

		$instance = new stdClass();

		$this->inner
			->shouldReceive( 'get' )
			->once()
			->with( 'MyClass' )
			->andReturn( $instance );

		$result = $this->container->get( 'MyClass' );

		$this->assertSame( $instance, $result );
	}

	#[Test]
	public function get_throws_if_inner_returns_non_object(): void {

		$this->inner
			->shouldReceive( 'get' )
			->once()
			->with( 'MyClass' )
			->andReturn( 'not_an_object' );

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'Container returned a non-object for id MyClass: string' );

		$this->container->get( 'MyClass' );
	}

	#[Test]
	public function has_delegates_to_inner_container(): void {

		$this->inner
			->shouldReceive( 'has' )
			->once()
			->with( 'MyClass' )
			->andReturnTrue();

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

		$this->addToAssertionCount( 1 );
	}

	#[Test]
	public function singleton_registers_string_binding(): void {

		$this->inner
			->shouldReceive( 'singleton' )
			->once()
			->with( 'MyService', 'MyImplementation' );

		$this->container->singleton( 'MyService', 'MyImplementation' );

		$this->addToAssertionCount( 1 );
	}

	#[Test]
	public function singleton_registers_self_binding_when_null(): void {

		$this->inner
			->shouldReceive( 'singleton' )
			->once()
			->with( 'MyService', null );

		$this->container->singleton( 'MyService' );

		$this->addToAssertionCount( 1 );
	}

	#[Test]
	public function make_delegates_to_inner_container_without_parameters(): void {

		$instance = new stdClass();

		$this->inner
			->shouldReceive( 'make' )
			->once()
			->with( 'MyClass', [] )
			->andReturn( $instance );

		$result = $this->container->make( 'MyClass' );

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
			->with( 'MyClass', $this->identicalTo( $params ) )
			->andReturn( $instance );

		$result = $this->container->make( 'MyClass', $params );

		$this->assertSame( $instance, $result );
	}

	#[Test]
	public function make_throws_if_inner_returns_non_object(): void {

		$this->inner
			->shouldReceive( 'make' )
			->once()
			->with( 'MyClass', [] )
			->andReturn( 42 );

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'Container made a non-object for id MyClass: integer' );

		$this->container->make( 'MyClass' );
	}
}
