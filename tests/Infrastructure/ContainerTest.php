<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure;

use Fundrik\WordPress\Infrastructure\Container\Container;
use Illuminate\Container\Container as IlluminateContainer;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass( Container::class )]
final class ContainerTest extends TestCase {

	private Container $container;
	private IlluminateContainer&MockInterface $inner;

	protected function setUp(): void {

		parent::setUp();

		$this->inner     = Mockery::mock( IlluminateContainer::class );
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

		$closure = fn() => new stdClass();

		$this->inner
			->shouldReceive( 'singleton' )
			->once()
			->with(
				'MyService',
				$this->identicalTo( $closure )
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
}
