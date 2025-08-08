<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\WordPress\HookMappers;

use Fundrik\WordPress\Infrastructure\Container\ContainerInterface;
use Fundrik\WordPress\Infrastructure\Integration\HookMappers\HookMapperRegistrar;
use Fundrik\WordPress\Infrastructure\Integration\HookMappers\HookMapperRegistry;
use Fundrik\WordPress\Infrastructure\Integration\HookMappers\HookToEventMapperInterface;
use Fundrik\WordPress\Tests\MockeryTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( HookMapperRegistrar::class )]
final class HookMapperRegistrarTest extends MockeryTestCase {

	private HookMapperRegistry&MockInterface $registry;
	private ContainerInterface&MockInterface $container;
	private HookMapperRegistrar $registrar;

	protected function setUp(): void {

		parent::setUp();

		$this->registry = Mockery::mock( HookMapperRegistry::class );
		$this->container = Mockery::mock( ContainerInterface::class );

		$this->registrar = new HookMapperRegistrar( $this->registry, $this->container );
	}

	#[Test]
	public function it_registers_all_mapper_classes(): void {

		$mapper1 = Mockery::mock( HookToEventMapperInterface::class );
		$mapper2 = Mockery::mock( HookToEventMapperInterface::class );

		$class1 = 'MapperClass1';
		$class2 = 'MapperClass2';

		$this->registry
			->shouldReceive( 'get_mapper_classes' )
			->once()
			->andReturn( [ $class1, $class2 ] );

		$this->container
			->shouldReceive( 'get' )
			->with( $class1 )
			->once()
			->andReturn( $mapper1 );

		$this->container
			->shouldReceive( 'get' )
			->with( $class2 )
			->once()
			->andReturn( $mapper2 );

		$mapper1->shouldReceive( 'register' )->once();
		$mapper2->shouldReceive( 'register' )->once();

		$this->registrar->register_all();
	}
}
