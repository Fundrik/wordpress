<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Components\Campaigns\Application;

use Fundrik\Core\Components\Campaigns\Application\CampaignAssembler as CoreCampaignAssembler;
use Fundrik\Core\Components\Campaigns\Application\CampaignDtoFactory as CoreCampaignDtoFactory;
use Fundrik\Core\Components\Shared\Domain\EntityId;
use Fundrik\WordPress\Components\Campaigns\Application\CampaignAssembler;
use Fundrik\WordPress\Components\Campaigns\Application\CampaignDto;
use Fundrik\WordPress\Components\Campaigns\Application\CampaignService;
use Fundrik\WordPress\Components\Campaigns\Application\Ports\Out\CampaignRepositoryPortInterface;
use Fundrik\WordPress\Components\Campaigns\Domain\Campaign;
use Fundrik\WordPress\Components\Campaigns\Domain\CampaignSlug;
use Fundrik\WordPress\Tests\MockeryTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( CampaignService::class )]
#[UsesClass( CampaignAssembler::class )]
#[UsesClass( CampaignDto::class )]
#[UsesClass( Campaign::class )]
#[UsesClass( CampaignSlug::class )]
final class CampaignServiceTest extends MockeryTestCase {

	private CampaignRepositoryPortInterface&MockInterface $repository;

	private CampaignService $service;

	protected function setUp(): void {

		parent::setUp();

		$this->repository = Mockery::mock( CampaignRepositoryPortInterface::class );

		$this->service = new CampaignService(
			new CampaignAssembler(
				new CoreCampaignDtoFactory(),
				new CoreCampaignAssembler(),
			),
			$this->repository,
		);
	}

	#[Test]
	public function find_campaign_by_id_returns_campaign(): void {

		$campaign_id = EntityId::create( 1 );

		$this->repository
			->shouldReceive( 'find_by_id' )
			->once()
			->with( $this->identicalTo( $campaign_id ) )
			->andReturn( $this->make_campaign_dto() );

		$result = $this->service->find_campaign_by_id( $campaign_id );

		$this->assertInstanceOf( Campaign::class, $result );
	}

	#[Test]
	public function find_campaign_by_id_returns_null_when_not_found(): void {

		$campaign_id = EntityId::create( 999 );

		$this->repository
			->shouldReceive( 'find_by_id' )
			->once()
			->with( $this->identicalTo( $campaign_id ) )
			->andReturn( null );

		$result = $this->service->find_campaign_by_id( $campaign_id );

		$this->assertNull( $result );
	}

	#[Test]
	public function find_all_campaigns_campaigns_returns_list_of_campaigns(): void {

		$dto1 = $this->make_campaign_dto();
		$dto2 = $this->make_campaign_dto( id: 2 );

		$this->repository
			->shouldReceive( 'find_all' )
			->once()
			->andReturn( [ $dto1, $dto2 ] );

		$result = $this->service->find_all_campaigns();

		$this->assertCount( 2, $result );
		$this->assertInstanceOf( Campaign::class, $result[0] );
		$this->assertInstanceOf( Campaign::class, $result[1] );
	}

	#[Test]
	public function find_all_campaigns_returns_empty_array_when_no_campaigns_found(): void {

		$this->repository
			->shouldReceive( 'find_all' )
			->once()
			->andReturn( [] );

		$result = $this->service->find_all_campaigns();

		$this->assertIsArray( $result );
		$this->assertCount( 0, $result );
	}

	#[Test]
	public function save_campaign_inserts_when_campaign_does_not_exist(): void {

		$this->repository
			->shouldReceive( 'exists' )
			->once()
			->with( Mockery::type( Campaign::class ) )
			->andReturn( false );

		$this->repository
			->shouldReceive( 'insert' )
			->once()
			->with( Mockery::type( Campaign::class ) )
			->andReturn( true );

		$result = $this->service->save_campaign( $this->make_campaign() );

		$this->assertTrue( $result );
	}

	#[Test]
	public function save_campaign_updates_when_campaign_exists(): void {

		$this->repository
			->shouldReceive( 'exists' )
			->once()
			->with( Mockery::type( Campaign::class ) )
			->andReturn( true );

		$this->repository
			->shouldReceive( 'update' )
			->once()
			->with( Mockery::type( Campaign::class ) )
			->andReturn( true );

		$result = $this->service->save_campaign( $this->make_campaign() );

		$this->assertTrue( $result );
	}

	#[Test]
	public function delete_campaign_returns_true_on_success(): void {

		$campaign_id = EntityId::create( 42 );

		$this->repository
			->shouldReceive( 'delete' )
			->once()
			->with( $this->identicalTo( $campaign_id ) )
			->andReturn( true );

		$result = $this->service->delete_campaign( $campaign_id );

		$this->assertTrue( $result );
	}

	#[Test]
	public function delete_campaign_returns_false_on_failure(): void {

		$campaign_id = EntityId::create( 999 );

		$this->repository
			->shouldReceive( 'delete' )
			->once()
			->with( $this->identicalTo( $campaign_id ) )
			->andReturn( false );

		$result = $this->service->delete_campaign( $campaign_id );

		$this->assertFalse( $result );
	}
}
