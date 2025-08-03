<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Migrations\Files;

use Fundrik\WordPress\Infrastructure\DatabaseInterface;
use Fundrik\WordPress\Infrastructure\Migrations\Files\CreateFundrikCampaignsTable;
use Fundrik\WordPress\Infrastructure\Migrations\Files\Exceptions\MigrationException;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationVersion;
use Fundrik\WordPress\Tests\MockeryTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( CreateFundrikCampaignsTable::class )]
#[UsesClass( MigrationVersion::class )]
final class CreateFundrikCampaignsTableTest extends MockeryTestCase {

	private CreateFundrikCampaignsTable $migration;
	private DatabaseInterface&MockInterface $db;

	protected function setUp(): void {

		parent::setUp();

		$this->db = Mockery::mock( DatabaseInterface::class );
		$this->migration = new CreateFundrikCampaignsTable( $this->db );
	}

	#[Test]
	public function apply_executes_expected_create_table_query(): void {

		$charset = 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci';

		$this->db
			->shouldReceive( 'query' )
			->once()
			->with(
				Mockery::on(
					static fn ( string $sql ): bool => str_contains(
						$sql,
						'CREATE TABLE IF NOT EXISTS `fundrik_campaigns`',
					)
					&& str_contains( $sql, $charset )
					&& str_contains( $sql, '`slug` varchar(200) NOT NULL' ),
				),
			)
			->andReturn( true );

		$this->migration->apply( $charset );
	}

	#[Test]
	public function it_throws_exception_if_query_fails(): void {

		$charset = 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci';

		$this->db
			->shouldReceive( 'query' )
			->once()
			->andReturnFalse();

		$this->expectException( MigrationException::class );
		$this->expectExceptionMessage( 'Failed to create fundrik_campaigns table.' );

		$this->migration->apply( $charset );
	}

	#[Test]
	public function it_has_the_migration_version_attribute(): void {

		$this->assert_class_has_attribute(
			class_name: CreateFundrikCampaignsTable::class,
			attribute_class: MigrationVersion::class,
			expected_values: [ 'value' => '2025_06_15_00' ],
		);
	}
}
