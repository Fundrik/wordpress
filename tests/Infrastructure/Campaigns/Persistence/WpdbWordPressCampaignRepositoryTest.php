<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Campaigns\Persistence;

use Fundrik\Core\Domain\Campaigns\Campaign;
use Fundrik\Core\Domain\Campaigns\CampaignTarget;
use Fundrik\Core\Domain\EntityId;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignDto;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignDtoFactory;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaign;
use Fundrik\WordPress\Infrastructure\Campaigns\Persistence\WpdbWordPressCampaignRepository;
use Fundrik\WordPress\Infrastructure\Persistence\Interfaces\QueryExecutorInterface;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( WpdbWordPressCampaignRepository::class )]
#[UsesClass( WordPressCampaignDto::class )]
#[UsesClass( WordPressCampaignDtoFactory::class )]
#[UsesClass( WordPressCampaign::class )]
class WpdbWordPressCampaignRepositoryTest extends FundrikTestCase {

	private const TABLE = 'fundrik_campaigns';

	private QueryExecutorInterface&MockInterface $query_executor;

	private WpdbWordPressCampaignRepository $repository;

	protected function setUp(): void {

		parent::setUp();

		$this->query_executor = Mockery::mock( QueryExecutorInterface::class );

		$this->repository = new WpdbWordPressCampaignRepository(
			new WordPressCampaignDtoFactory(),
			$this->query_executor
		);
	}

	#[Test]
	public function get_by_id_returns_campaign_dto_when_found(): void {

		$id = 123;

		$campaign_id = EntityId::create( $id );

		$db_data = [
			'id'            => $id,
			'title'         => 'Test Campaign',
			'slug'          => 'test-campaign',
			'is_enabled'    => true,
			'is_open'       => true,
			'has_target'    => true,
			'target_amount' => 1000,
		];

		$this->query_executor
			->shouldReceive( 'get_by_id' )
			->once()
			->with(
				$this->identicalTo( self::TABLE ),
				$this->identicalTo( $id )
			)
			->andReturn( $db_data );

		$result = $this->repository->get_by_id( $campaign_id );

		$this->assertInstanceOf( WordPressCampaignDto::class, $result );
		$this->assertSame( $id, $result->id );
		$this->assertSame( 'Test Campaign', $result->title );
	}

	#[Test]
	public function get_by_id_returns_null_when_not_found(): void {

		$id = 999;

		$campaign_id = EntityId::create( $id );

		$this->query_executor
			->shouldReceive( 'get_by_id' )
			->once()
			->with(
				$this->identicalTo( self::TABLE ),
				$this->identicalTo( $id )
			)
			->andReturn( null );

		$result = $this->repository->get_by_id( $campaign_id );

		$this->assertNull( $result );
	}

	#[Test]
	public function get_all_returns_array_of_campaigns(): void {

		$db_data = [
			[
				'id'               => 123,
				'title'            => 'Campaign 1',
				'slug'             => 'campaign-1',
				'is_enabled'       => true,
				'is_open'          => true,
				'has_target'       => true,
				'target_amount'    => 1000,
				'collected_amount' => 200,
			],
			[
				'id'               => 124,
				'title'            => 'Campaign 2',
				'slug'             => 'campaign-2',
				'is_enabled'       => true,
				'is_open'          => false,
				'has_target'       => false,
				'target_amount'    => 0,
				'collected_amount' => 0,
			],
		];

		$this->query_executor
			->shouldReceive( 'get_all' )
			->once()
			->with( $this->identicalTo( self::TABLE ) )
			->andReturn( $db_data );

		$result = $this->repository->get_all();

		$this->assertCount( 2, $result );

		$this->assertInstanceOf( WordPressCampaignDto::class, $result[0] );
		$this->assertSame( 123, $result[0]->id );

		$this->assertInstanceOf( WordPressCampaignDto::class, $result[1] );
		$this->assertSame( 124, $result[1]->id );
	}

	#[Test]
	public function exists_returns_true_when_campaign_exists(): void {

		$id       = 123;
		$campaign = $this->create_fake_campaign_with_id( $id );

		$this->query_executor
			->shouldReceive( 'exists' )
			->once()
			->with(
				$this->identicalTo( self::TABLE ),
				$this->identicalTo( $id )
			)
			->andReturn( true );

		$result = $this->repository->exists( $campaign );

		$this->assertTrue( $result );
	}


	#[Test]
	public function exists_returns_false_when_campaign_does_not_exist(): void {

		$id       = 999;
		$campaign = $this->create_fake_campaign_with_id( $id );

		$this->query_executor
			->shouldReceive( 'exists' )
			->once()
			->with(
				$this->identicalTo( self::TABLE ),
				$this->identicalTo( $id )
			)
			->andReturn( false );

		$result = $this->repository->exists( $campaign );

		$this->assertFalse( $result );
	}

	#[Test]
	public function insert_calls_executor_and_returns_true_on_success(): void {

		$campaign = $this->create_fake_campaign_with_id( 1 );

		$this->query_executor
			->shouldReceive( 'insert' )
			->once()
			->with(
				$this->identicalTo( self::TABLE ),
				Mockery::type( 'array' )
			)
			->andReturn( true );

		$result = $this->repository->insert( $campaign );

		$this->assertTrue( $result );
	}

	#[Test]
	public function update_calls_executor_and_returns_true_on_success(): void {

		$id = 1;

		$campaign = $this->create_fake_campaign_with_id( $id );

		$this->query_executor
			->shouldReceive( 'update' )
			->once()
			->with(
				$this->identicalTo( self::TABLE ),
				Mockery::type( 'array' ),
				$this->identicalTo( $id )
			)
			->andReturn( true );

		$result = $this->repository->update( $campaign );

		$this->assertTrue( $result );
	}

	#[Test]
	public function delete_calls_executor_and_returns_true_on_success(): void {

		$id = 1;

		$campaign_id = EntityId::create( $id );

		$this->query_executor
			->shouldReceive( 'delete' )
			->once()
			->with(
				$this->identicalTo( self::TABLE ),
				$this->identicalTo( $id )
			)
			->andReturn( true );

		$result = $this->repository->delete( $campaign_id );

		$this->assertTrue( $result );
	}

	private function create_fake_campaign_with_id( int $id ): WordPressCampaign {

		return new WordPressCampaign(
			new Campaign(
				EntityId::create( $id ),
				'title',
				true,
				true,
				new CampaignTarget( true, 1000 ),
			),
			'slug',
		);
	}
}
