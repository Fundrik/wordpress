<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Helpers;

use Fundrik\Core\Infrastructure\Interfaces\ContainerInterface;
use Fundrik\WordPress\Infrastructure\Container\ContainerRegistry;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;

use function fundrik;

#[CoversFunction( 'fundrik' )]
#[UsesClass( ContainerRegistry::class )]
final class HelpersTest extends FundrikTestCase {

	#[Test]
	public function fundrik_returns_container_instance(): void {

		$mock_container = Mockery::mock( ContainerInterface::class );

		ContainerRegistry::set( $mock_container );

		$result = fundrik();

		self::assertSame( $mock_container, $result );
	}
}
