<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure;

use Fundrik\Core\Infrastructure\Interfaces\ContainerInterface;
use Fundrik\WordPress\Infrastructure\Container\ContainerRegistry;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;

#[CoversClass( ContainerRegistry::class )]
final class ContainerRegistryTest extends FundrikTestCase {

	#[Test]
	public function get_returns_set_container(): void {

		$mock_container = Mockery::mock( ContainerInterface::class );

		ContainerRegistry::set( $mock_container );

		$this->assertSame( $mock_container, ContainerRegistry::get() );
	}

	#[Test]
	public function get_throws_if_container_not_set(): void {

		ContainerRegistry::set( null );

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'Container instance is not set.' );

		ContainerRegistry::get();
	}

	#[Test]
	public function set_overwrites_existing_container(): void {

		$first_container  = Mockery::mock( ContainerInterface::class );
		$second_container = Mockery::mock( ContainerInterface::class );

		ContainerRegistry::set( $first_container );
		$this->assertSame( $first_container, ContainerRegistry::get() );

		ContainerRegistry::set( $second_container );
		$this->assertSame( $second_container, ContainerRegistry::get() );
	}
}
