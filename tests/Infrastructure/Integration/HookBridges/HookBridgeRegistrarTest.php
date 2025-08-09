<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Integration\HookBridges;

use Fundrik\WordPress\Infrastructure\Container\ContainerInterface;
use Fundrik\WordPress\Infrastructure\Integration\HookBridges\HookBridgeRegistrar;
use Fundrik\WordPress\Infrastructure\Integration\HookBridges\HookBridgeRegistry;
use Fundrik\WordPress\Infrastructure\Integration\HookBridges\HookToEventBridgeInterface;
use Fundrik\WordPress\Tests\MockeryTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( HookBridgeRegistrar::class )]
final class HookBridgeRegistrarTest extends MockeryTestCase {

	private HookBridgeRegistry&MockInterface $registry;
	private ContainerInterface&MockInterface $container;
	private HookBridgeRegistrar $registrar;

	protected function setUp(): void {

		parent::setUp();

		$this->registry = Mockery::mock( HookBridgeRegistry::class );
		$this->container = Mockery::mock( ContainerInterface::class );

		$this->registrar = new HookBridgeRegistrar( $this->registry, $this->container );
	}

	#[Test]
	public function it_registers_all_bridge_classes(): void {

		$bridge1 = Mockery::mock( HookToEventBridgeInterface::class );
		$bridge2 = Mockery::mock( HookToEventBridgeInterface::class );

		$class1 = 'BridgeClass1';
		$class2 = 'BridgeClass2';

		$this->registry
			->shouldReceive( 'get_bridge_classes' )
			->once()
			->andReturn( [ $class1, $class2 ] );

		$this->container
			->shouldReceive( 'get' )
			->with( $class1 )
			->once()
			->andReturn( $bridge1 );

		$this->container
			->shouldReceive( 'get' )
			->with( $class2 )
			->once()
			->andReturn( $bridge2 );

		$bridge1->shouldReceive( 'register' )->once();
		$bridge2->shouldReceive( 'register' )->once();

		$this->registrar->register_all();
	}
}
