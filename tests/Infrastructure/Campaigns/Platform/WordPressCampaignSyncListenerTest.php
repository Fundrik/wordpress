<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Campaigns\Platform;

use Fundrik\Core\Domain\EntityId;
use Fundrik\WordPress\Application\Campaigns\Input\AbstractAdminWordPressCampaignInput;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignInput;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignInputFactory;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignPartialInput;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignPartialInputFactory;
use Fundrik\WordPress\Application\Campaigns\Interfaces\WordPressCampaignServiceInterface;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces\WordPressCampaignPostMapperInterface;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignPostType;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignSyncListener;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use stdClass;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use WP_Error;
use WP_REST_Request;

#[CoversClass( WordPressCampaignSyncListener::class )]
#[UsesClass( WordPressCampaignPostType::class )]
#[UsesClass( AbstractAdminWordPressCampaignInput::class )]
#[UsesClass( AdminWordPressCampaignInputFactory::class )]
#[UsesClass( AdminWordPressCampaignPartialInput::class )]
#[UsesClass( AdminWordPressCampaignPartialInputFactory::class )]
#[UsesClass( AdminWordPressCampaignInput::class )]
class WordPressCampaignSyncListenerTest extends FundrikTestCase {

	private WordPressCampaignPostMapperInterface&MockInterface $mapper;
	private WordPressCampaignServiceInterface&MockInterface $service;

	private WordPressCampaignPostType $post_type;
	private AdminWordPressCampaignInputFactory $input_factory;
	private AdminWordPressCampaignPartialInputFactory $partial_input_factory;
	private WordPressCampaignSyncListener $listener;

	protected function setUp(): void {

		parent::setUp();

		$this->mapper  = Mockery::mock( WordPressCampaignPostMapperInterface::class );
		$this->service = Mockery::mock( WordPressCampaignServiceInterface::class );

		$this->post_type             = new WordPressCampaignPostType();
		$this->input_factory         = new AdminWordPressCampaignInputFactory( $this->mapper );
		$this->partial_input_factory = new AdminWordPressCampaignPartialInputFactory();

		$this->listener = new WordPressCampaignSyncListener(
			$this->post_type,
			$this->input_factory,
			$this->partial_input_factory,
			$this->service
		);
	}

	#[Test]
	public function it_registers_hooks(): void {

		$this->listener->register();

		self::assertNotFalse(
			has_filter(
				'rest_pre_insert_' . $this->post_type->get_type(),
				$this->listener->validate( ... )
			)
		);

		self::assertNotFalse(
			has_action(
				'wp_insert_post',
				$this->listener->sync( ... )
			)
		);

		self::assertNotFalse(
			has_action(
				'delete_post',
				$this->listener->delete( ... )
			)
		);
	}

	#[Test]
	public function validate_returns_post_when_validation_successful(): void {

		$prepared_post = new stdClass();
		$request       = Mockery::mock( WP_REST_Request::class );

		$input_data = [
			'id'    => 123,
			'title' => 'Valid Campaign',
			'meta'  => [
				'is_open'       => true,
				'has_target'    => true,
				'target_amount' => 100,
			],
		];

		$request->shouldReceive( 'get_params' )
			->once()
			->andReturn( $input_data );

		$this->service
			->shouldReceive( 'validate_input' )
			->once()
			->with( Mockery::type( AdminWordPressCampaignPartialInput::class ) );

		$result = $this->listener->validate( $prepared_post, $request );

		self::assertSame( $prepared_post, $result );
	}

	#[Test]
	public function validate_returns_wp_error_when_validation_fails(): void {

		$prepared_post = new stdClass();
		$request       = Mockery::mock( WP_REST_Request::class );

		$input_data = [
			'id'    => 456,
			'title' => 'Invalid Campaign',
			'meta'  => [
				'is_open'       => true,
				'has_target'    => false,
				'target_amount' => 100,
			],
		];

		$request->shouldReceive( 'get_params' )
			->once()
			->andReturn( $input_data );

		$mock_violation_list = Mockery::mock( ConstraintViolationListInterface::class );
		$mock_violation_list
			->shouldReceive( 'count' )
			->andReturn( 1 );

		$this->service
			->shouldReceive( 'validate_input' )
			->once()
			->with( Mockery::type( AdminWordPressCampaignPartialInput::class ) )
			->andThrow( new ValidationFailedException( [], $mock_violation_list ) );

		$result = $this->listener->validate( $prepared_post, $request );

		self::assertInstanceOf( WP_Error::class, $result );
		self::assertSame( 'campaign_validation_failed', $result->get_error_code() );
	}

	#[Test]
	public function sync_calls_save_campaign_on_service(): void {

		$post             = Mockery::mock( 'WP_Post' );
		$post->ID         = 123;
		$post->post_title = 'Test Campaign';
		$post->post_type  = $this->post_type->get_type();
		$post->meta       = [
			'is_open'       => true,
			'has_target'    => true,
			'target_amount' => 100,
		];

		$this->mapper
			->shouldReceive( 'to_array_from_post' )
			->once()
			->with( $post )
			->andReturn(
				[
					'id'    => $post->ID,
					'title' => $post->post_title,
					'meta'  => $post->meta,
				]
			);

		$this->service
			->shouldReceive( 'save_campaign' )
			->once()
			->with( Mockery::type( AdminWordPressCampaignInput::class ) );

		$this->listener->sync( $post->ID, $post );
	}

	#[Test]
	public function sync_logs_error_when_validation_exception_thrown(): void {

		$post             = Mockery::mock( 'WP_Post' );
		$post->ID         = 123;
		$post->post_title = 'Test Campaign';
		$post->post_type  = $this->post_type->get_type();
		$post->meta       = [
			'is_open'       => true,
			'has_target'    => true,
			'target_amount' => 0,
		];

		$this->mapper
			->shouldReceive( 'to_array_from_post' )
			->once()
			->with( $post )
			->andReturn(
				[
					'id'    => $post->ID,
					'title' => $post->post_title,
					'meta'  => $post->meta,
				]
			);

		$mock_violation_list = Mockery::mock( ConstraintViolationListInterface::class );
		$mock_violation_list
			->shouldReceive( 'count' )
			->andReturn( 1 );

		$this->service
			->shouldReceive( 'save_campaign' )
			->once()
			->with( Mockery::type( AdminWordPressCampaignInput::class ) )
			->andThrow( new ValidationFailedException( [], $mock_violation_list ) );

		$this->listener->sync( $post->ID, $post );
	}

	#[Test]
	public function sync_does_nothing_if_post_type_is_not_campaign(): void {

		$post             = Mockery::mock( 'WP_Post' );
		$post->ID         = 123;
		$post->post_title = 'Irrelevant Post';
		$post->post_type  = 'other_post_type';

		$this->mapper
			->shouldNotReceive( 'to_array_from_post' );

		$this->service
			->shouldNotReceive( 'save_campaign' );

		$this->listener->sync( $post->ID, $post );
	}

	#[Test]
	public function delete_calls_delete_campaign_on_service(): void {

		$post            = Mockery::mock( 'WP_Post' );
		$post->ID        = 42;
		$post->post_type = $this->post_type->get_type();

		$this->service
			->shouldReceive( 'delete_campaign' )
			->once()
			->with( Mockery::on( fn ( EntityId $id ) => 42 === $id->value ) );

		$this->listener->delete( $post->ID, $post );
	}

	#[Test]
	public function delete_does_nothing_if_post_type_is_not_campaign(): void {

		$post            = Mockery::mock( 'WP_Post' );
		$post->ID        = 42;
		$post->post_type = 'not_campaign';

		$this->service
			->shouldNotReceive( 'delete_campaign' );

		$this->listener->delete( $post->ID, $post );
	}
}
