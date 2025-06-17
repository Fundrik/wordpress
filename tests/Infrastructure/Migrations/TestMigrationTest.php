<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Fixtures;

use Fundrik\WordPress\Infrastructure\Persistence\Interfaces\QueryExecutorInterface;
use Fundrik\WordPress\Support\Path;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( TestMigration::class )]
final class TestMigrationTest extends FundrikTestCase {

	private QueryExecutorInterface&MockInterface $query_executor;

	private TestMigration $migration;

	protected function setUp(): void {

		parent::setUp();

		require_once Path::PHP_BASE . '../../tests/Fixtures/2025_06_15_00_test_migration.php';

		$this->query_executor = Mockery::mock( QueryExecutorInterface::class );

		$this->migration = new TestMigration( $this->query_executor );
	}

	#[Test]
	public function it_executes_query(): void {

		$this->query_executor
			->shouldReceive( 'query' )
			->once()
			->with( 'SELECT 1' );

		$this->migration->apply( '' );
	}
}
