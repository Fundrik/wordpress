<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Persistence;

use Fundrik\WordPress\Infrastructure\Persistence\WpdbQueryExecutor;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use wpdb;

#[CoversClass( WpdbQueryExecutor::class )]
class WpdbQueryExecutorTest extends FundrikTestCase {

	private const TABLE = 'campaigns';

	private wpdb&MockInterface $wpdb;
	private string $table_name;

	protected function setUp(): void {

		parent::setUp();

		$this->wpdb         = Mockery::mock( 'wpdb' );
		$this->wpdb->prefix = 'wp_';

		$this->table_name = $this->wpdb->prefix . self::TABLE;

		if ( ! defined( 'ARRAY_A' ) ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
			define( 'ARRAY_A', 'ARRAY_A' );
		}
	}

	public static function id_provider(): array {

		return [
			'int_id'  => [ 123 ],
			'uuid_id' => [ '0196a2f4-a700-7606-818a-00660fa2be0c' ],
		];
	}

	#[Test]
	#[DataProvider( 'id_provider' )]
	public function returns_single_row_by_id( int|string $id ) {

		$placeholder = is_int( $id ) ? '%d' : '%s';

		$prepared_sql = "SELECT * FROM {$this->table_name} WHERE id = {$id} LIMIT 1";

		$this->wpdb
			->shouldReceive( 'prepare' )
			->once()
			->with(
				$this->identicalTo( "SELECT * FROM %i WHERE id = {$placeholder} LIMIT 1" ),
				$this->identicalTo( self::TABLE ),
				$this->identicalTo( $id )
			)
			->andReturn( $prepared_sql );

		$this->wpdb
			->shouldReceive( 'get_row' )
			->once()
			->with(
				$this->identicalTo( $prepared_sql ),
				$this->identicalTo( ARRAY_A )
			)
			->andReturn(
				[
					'id'    => $id,
					'title' => 'My Campaign',
				]
			);

		$executor = new WpdbQueryExecutor( $this->wpdb );
		$result   = $executor->get_by_id( self::TABLE, $id );

		$this->assertEquals(
			[
				'id'    => $id,
				'title' => 'My Campaign',
			],
			$result
		);
	}

	#[Test]
	public function returns_null_when_row_not_found() {

		$id = 999;

		$prepared_sql = "SELECT * FROM {$this->table_name} WHERE id = {$id} LIMIT 1";

		$this->wpdb
			->shouldReceive( 'prepare' )
			->once()
			->with(
				$this->identicalTo( 'SELECT * FROM %i WHERE id = %d LIMIT 1' ),
				$this->identicalTo( self::TABLE ),
				$this->identicalTo( $id )
			)
			->andReturn( $prepared_sql );

		$this->wpdb
			->shouldReceive( 'get_row' )
			->once()
			->with(
				$this->identicalTo( $prepared_sql ),
				$this->identicalTo( ARRAY_A )
			)
			->andReturn( null );

		$executor = new WpdbQueryExecutor( $this->wpdb );
		$result   = $executor->get_by_id( self::TABLE, $id );

		$this->assertNull( $result );
	}

	#[Test]
	public function returns_all_rows_from_table() {

		$prepared_sql = "SELECT * FROM {$this->table_name}";

		$this->wpdb
			->shouldReceive( 'prepare' )
			->once()
			->with(
				$this->identicalTo( 'SELECT * FROM %i' ),
				$this->identicalTo( self::TABLE ),
			)
			->andReturn( $prepared_sql );

		$this->wpdb
			->shouldReceive( 'get_results' )
			->once()
			->with(
				$this->identicalTo( $prepared_sql ),
				$this->identicalTo( ARRAY_A )
			)
			->andReturn(
				[
					[
						'id'    => 1,
						'title' => 'First',
					],
					[
						'id'    => 2,
						'title' => 'Second',
					],
				]
			);

		$executor = new WpdbQueryExecutor( $this->wpdb );
		$result   = $executor->get_all( self::TABLE );

		$this->assertCount( 2, $result );
		$this->assertSame( 'First', $result[0]['title'] );
	}

	#[Test]
	#[DataProvider( 'id_provider' )]
	public function returns_true_if_record_exists( int|string $id ): void {

		$placeholder  = is_int( $id ) ? '%d' : '%s';
		$prepared_sql = "SELECT id FROM {$this->table_name} WHERE id = {$id} LIMIT 1";

		$this->wpdb
			->shouldReceive( 'prepare' )
			->once()
			->with(
				$this->identicalTo( "SELECT id FROM %i WHERE id = {$placeholder} LIMIT 1" ),
				$this->identicalTo( self::TABLE ),
				$this->identicalTo( $id )
			)
			->andReturn( $prepared_sql );

		$this->wpdb
			->shouldReceive( 'get_var' )
			->once()
			->with( $this->identicalTo( $prepared_sql ) )
			->andReturn( $id );

		$executor = new WpdbQueryExecutor( $this->wpdb );

		$this->assertTrue( $executor->exists( self::TABLE, $id ) );
	}

	#[Test]
	#[DataProvider( 'id_provider' )]
	public function returns_false_if_record_does_not_exist( int|string $id ): void {

		$placeholder  = is_int( $id ) ? '%d' : '%s';
		$prepared_sql = "SELECT id FROM {$this->table_name} WHERE id = {$id} LIMIT 1";

		$this->wpdb
			->shouldReceive( 'prepare' )
			->once()
			->with(
				$this->identicalTo( "SELECT id FROM %i WHERE id = {$placeholder} LIMIT 1" ),
				$this->identicalTo( self::TABLE ),
				$this->identicalTo( $id )
			)
			->andReturn( $prepared_sql );

		$this->wpdb
			->shouldReceive( 'get_var' )
			->once()
			->with( $this->identicalTo( $prepared_sql ) )
			->andReturn( null );

		$executor = new WpdbQueryExecutor( $this->wpdb );

		$this->assertFalse( $executor->exists( self::TABLE, $id ) );
	}

	#[Test]
	public function inserts_new_row_successfully(): void {

		$data = [
			'title' => 'New Campaign',
		];

		$this->wpdb
			->shouldReceive( 'insert' )
			->once()
			->with(
				$this->identicalTo( self::TABLE ),
				$this->identicalTo( $data )
			)
			->andReturn( 1 );

		$executor = new WpdbQueryExecutor( $this->wpdb );

		$this->assertTrue( $executor->insert( self::TABLE, $data ) );
	}

	#[Test]
	public function fails_to_insert_row_and_returns_false(): void {

		$data = [
			'title' => 'Invalid Campaign',
		];

		$this->wpdb
			->shouldReceive( 'insert' )
			->once()
			->with(
				$this->identicalTo( self::TABLE ),
				$this->identicalTo( $data )
			)
			->andReturn( false );

		$executor = new WpdbQueryExecutor( $this->wpdb );

		$this->assertFalse( $executor->insert( self::TABLE, $data ) );
	}

	#[Test]
	#[DataProvider( 'id_provider' )]
	public function updates_row_successfully( int|string $id ): void {

		$data = [
			'title' => 'Updated Campaign',
		];

		$this->wpdb
			->shouldReceive( 'update' )
			->once()
			->with(
				$this->identicalTo( self::TABLE ),
				$this->identicalTo( $data ),
				$this->identicalTo( [ 'id' => $id ] )
			)
			->andReturn( 1 );

		$executor = new WpdbQueryExecutor( $this->wpdb );

		$this->assertTrue( $executor->update( self::TABLE, $data, $id ) );
	}

	#[Test]
	#[DataProvider( 'id_provider' )]
	public function fails_to_update_row_and_returns_false( int|string $id ): void {

		$data = [
			'title' => 'Will Not Update',
		];

		$this->wpdb
			->shouldReceive( 'update' )
			->once()
			->with(
				$this->identicalTo( self::TABLE ),
				$this->identicalTo( $data ),
				$this->identicalTo( [ 'id' => $id ] )
			)
			->andReturn( false );

		$executor = new WpdbQueryExecutor( $this->wpdb );

		$this->assertFalse( $executor->update( self::TABLE, $data, $id ) );
	}

	#[Test]
	#[DataProvider( 'id_provider' )]
	public function deletes_row_successfully( int|string $id ): void {

		$this->wpdb
			->shouldReceive( 'delete' )
			->once()
			->with(
				$this->identicalTo( self::TABLE ),
				$this->identicalTo( [ 'id' => $id ] )
			)
			->andReturn( 1 );

		$executor = new WpdbQueryExecutor( $this->wpdb );

		$this->assertTrue( $executor->delete( self::TABLE, $id ) );
	}

	#[Test]
	#[DataProvider( 'id_provider' )]
	public function fails_to_delete_row_and_returns_false( int|string $id ): void {

		$this->wpdb
			->shouldReceive( 'delete' )
			->once()
			->with(
				$this->identicalTo( self::TABLE ),
				$this->identicalTo( [ 'id' => $id ] )
			)
			->andReturn( false );

		$executor = new WpdbQueryExecutor( $this->wpdb );

		$this->assertFalse( $executor->delete( self::TABLE, $id ) );
	}
}
