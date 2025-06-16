<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Application\Campaigns;

use Fundrik\Core\Application\Campaigns\CampaignDtoFactory;
use Fundrik\Core\Domain\Campaigns\CampaignFactory;
use Fundrik\Core\Domain\EntityId;
use Fundrik\WordPress\Application\Campaigns\Input\AbstractAdminWordPressCampaignInput;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignInput;
use Fundrik\WordPress\Application\Campaigns\Interfaces\WordPressCampaignRepositoryInterface;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignDto;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignDtoFactory;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignService;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaign;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaignFactory;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaignSlug;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[CoversClass( WordPressCampaignService::class )]
#[UsesClass( WordPressCampaign::class )]
#[UsesClass( WordPressCampaignFactory::class )]
#[UsesClass( WordPressCampaignSlug::class )]
#[UsesClass( AbstractAdminWordPressCampaignInput::class )]
#[UsesClass( AdminWordPressCampaignInput::class )]
#[UsesClass( WordPressCampaignDtoFactory::class )]
#[UsesClass( WordPressCampaignDto::class )]
final class WordPressCampaignServiceTest extends FundrikTestCase {

	private WordPressCampaignRepositoryInterface&MockInterface $repository;
	private ValidatorInterface&MockInterface $validator;

	private WordPressCampaignService $service;

	protected function setUp(): void {

		parent::setUp();

		$this->repository = Mockery::mock( WordPressCampaignRepositoryInterface::class );
		$this->validator  = Mockery::mock( ValidatorInterface::class );

		$this->service = new WordPressCampaignService(
			new WordPressCampaignFactory(
				new CampaignFactory(),
				new CampaignDtoFactory(),
			),
			new WordPressCampaignDtoFactory(),
			$this->repository,
			$this->validator,
		);
	}

	#[Test]
	public function get_by_id_returns_campaign(): void {

		$campaign_id = EntityId::create( 123 );

		$dto = new WordPressCampaignDto(
			id            : 123,
			title         : 'Array Campaign',
			slug          : 'array-campaign',
			is_enabled    : true,
			is_open       : true,
			has_target    : true,
			target_amount : 1500,
		);

		$this->repository
			->shouldReceive( 'get_by_id' )
			->once()
			->with( $this->identicalTo( $campaign_id ) )
			->andReturn( $dto );

		$result = $this->service->get_campaign_by_id( $campaign_id );

		$this->assertInstanceOf( WordPressCampaign::class, $result );
	}


	#[Test]
	public function get_by_id_returns_null_when_not_found(): void {

		$campaign_id = EntityId::create( 999 );

		$this->repository
			->shouldReceive( 'get_by_id' )
			->once()
			->with( $this->identicalTo( $campaign_id ) )
			->andReturn( null );

		$result = $this->service->get_campaign_by_id( $campaign_id );

		$this->assertNull( $result );
	}

	#[Test]
	public function get_all_campaigns_returns_list_of_campaigns(): void {

		$dto1 = new WordPressCampaignDto(
			id            : 123,
			title         : 'Campaign One',
			slug          : 'campaign-one',
			is_enabled    : true,
			is_open       : true,
			has_target    : true,
			target_amount : 1500,
		);

		$dto2 = new WordPressCampaignDto(
			id            : 124,
			title         : 'Campaign Two',
			slug          : 'campaign-two',
			is_enabled    : true,
			is_open       : true,
			has_target    : true,
			target_amount : 1500,
		);

		$this->repository
			->shouldReceive( 'get_all' )
			->once()
			->andReturn( [ $dto1, $dto2 ] );

		$result = $this->service->get_all_campaigns();

		$this->assertCount( 2, $result );
		$this->assertInstanceOf( WordPressCampaign::class, $result[0] );
		$this->assertInstanceOf( WordPressCampaign::class, $result[1] );
	}

	#[Test]
	public function save_campaign_inserts_when_not_exists(): void {

		$input = new AdminWordPressCampaignInput(
			id            : 555,
			title         : 'New Campaign',
			slug          : 'new-campaign',
			is_enabled    : true,
			is_open       : true,
			has_target    : false,
			target_amount : 0,
		);

		$mock_violation_list = Mockery::mock( ConstraintViolationListInterface::class );
		$mock_violation_list
			->shouldReceive( 'count' )
			->andReturn( 0 );

		$this->validator
			->shouldReceive( 'validate' )
			->once()
			->with( $this->identicalTo( $input ) )
			->andReturn( $mock_violation_list );

		$this->repository
			->shouldReceive( 'exists' )
			->once()
			->with( Mockery::type( WordPressCampaign::class ) )
			->andReturn( false );

		$this->repository
			->shouldReceive( 'insert' )
			->once()
			->with( Mockery::type( WordPressCampaign::class ) )
			->andReturn( true );

		$result = $this->service->save_campaign( $input );

		$this->assertTrue( $result );
	}

	#[Test]
	public function save_campaign_updates_when_exists(): void {

		$input = new AdminWordPressCampaignInput(
			id            : 777,
			title         : 'Existing Campaign',
			slug          : 'existing-campaign',
			is_enabled    : true,
			is_open       : false,
			has_target    : true,
			target_amount : 999,
		);

		$mock_violation_list = Mockery::mock( ConstraintViolationListInterface::class );
		$mock_violation_list
			->shouldReceive( 'count' )
			->andReturn( 0 );

		$this->validator
			->shouldReceive( 'validate' )
			->once()
			->with( $this->identicalTo( $input ) )
			->andReturn( $mock_violation_list );

		$this->repository
			->shouldReceive( 'exists' )
			->once()
			->with( Mockery::type( WordPressCampaign::class ) )
			->andReturn( true );

		$this->repository
			->shouldReceive( 'update' )
			->once()
			->with( Mockery::type( WordPressCampaign::class ) )
			->andReturn( true );

		$result = $this->service->save_campaign( $input );

		$this->assertTrue( $result );
	}

	#[Test]
	public function save_campaign_throws_exception_when_validation_fails(): void {

		$input = new AdminWordPressCampaignInput(
			id            : 999,
			title         : 'Invalid Campaign',
			slug          : 'invalid-campaign',
			is_enabled    : true,
			is_open       : true,
			has_target    : false,
			target_amount : 0,
		);

		$mock_violation_list = Mockery::mock( ConstraintViolationListInterface::class );
		$mock_violation_list
			->shouldReceive( 'count' )
			->andReturn( 1 );

		$this->validator
			->shouldReceive( 'validate' )
			->once()
			->with( $this->identicalTo( $input ) )
			->andReturn( $mock_violation_list );

		$this->expectException( ValidationFailedException::class );

		$this->service->save_campaign( $input );
	}

	#[Test]
	public function delete_campaign_returns_true_when_successful(): void {

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
	public function delete_campaign_returns_false_when_failed(): void {

		$campaign_id = EntityId::create( 999 );

		$this->repository
			->shouldReceive( 'delete' )
			->once()
			->with( $this->identicalTo( $campaign_id ) )
			->andReturn( false );

		$result = $this->service->delete_campaign( $campaign_id );

		$this->assertFalse( $result );
	}

	#[Test]
	public function validate_input_passes_when_no_errors(): void {

		$input = new AdminWordPressCampaignInput(
			id            : 1,
			title         : 'Valid Campaign',
			slug          : 'valid-campaign',
			is_enabled    : true,
			is_open       : true,
			has_target    : false,
			target_amount : 0,
		);

		$mock_violation_list = Mockery::mock( ConstraintViolationListInterface::class );
		$mock_violation_list
			->shouldReceive( 'count' )
			->once()
			->andReturn( 0 );

		$this->validator
			->shouldReceive( 'validate' )
			->once()
			->with( $this->identicalTo( $input ) )
			->andReturn( $mock_violation_list );

		$this->service->validate_input( $input );

		$this->assertTrue( true );
	}

	#[Test]
	public function validate_input_throws_exception_when_errors_present(): void {

		$input = new AdminWordPressCampaignInput(
			id            : 2,
			title         : 'Invalid Campaign',
			slug          : 'invalid-campaign',
			is_enabled    : false,
			is_open       : false,
			has_target    : true,
			target_amount : 1000,
		);

		$mock_violation_list = Mockery::mock( ConstraintViolationListInterface::class );
		$mock_violation_list
			->shouldReceive( 'count' )
			->once()
			->andReturn( 3 );

		$this->validator
			->shouldReceive( 'validate' )
			->once()
			->with( $this->identicalTo( $input ) )
			->andReturn( $mock_violation_list );

		$this->expectException( ValidationFailedException::class );

		$this->service->validate_input( $input );
	}
}
