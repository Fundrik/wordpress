<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Campaigns\Platform;

use Fundrik\Core\Domain\EntityId;
use Fundrik\Core\Infrastructure\Interfaces\ContainerInterface;
use Fundrik\Core\Support\TypeCaster;
use Fundrik\Core\Support\TypedArrayExtractor;
use Fundrik\WordPress\Application\Campaigns\Input\Abstracts\AbstractAdminWordPressCampaignInput;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignInput;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignInputFactory;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignPartialInput;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignPartialInputFactory;
use Fundrik\WordPress\Application\Campaigns\Interfaces\WordPressCampaignServiceInterface;
use Fundrik\WordPress\Application\Validation\Interfaces\ValidationErrorTransformerInterface;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces\WordPressCampaignPostMapperInterface;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignPostType;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignSyncListener;
use Fundrik\WordPress\Infrastructure\Container\ContainerRegistry;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesFunction;
use stdClass;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use WP_Error;
use WP_REST_Request;

#[CoversClass( WordPressCampaignSyncListener::class )]
#[UsesClass( WordPressCampaignPostType::class )]
#[UsesClass( AdminWordPressCampaignInputFactory::class )]
#[UsesClass( AdminWordPressCampaignPartialInput::class )]
#[UsesClass( AdminWordPressCampaignPartialInputFactory::class )]
#[UsesClass( AdminWordPressCampaignInput::class )]
#[UsesClass( ContainerRegistry::class )]
#[UsesFunction( 'fundrik' )]
final class WordPressCampaignSyncListenerTest extends FundrikTestCase {

	private ContainerInterface&MockInterface $container;
	private WordPressCampaignPostType&MockInterface $post_type;
	private WordPressCampaignPostMapperInterface&MockInterface $mapper;
	private WordPressCampaignServiceInterface&MockInterface $service;
	private ValidationErrorTransformerInterface&MockInterface $error_transformer;

	private AdminWordPressCampaignInputFactory $input_factory;
	private AdminWordPressCampaignPartialInputFactory $partial_input_factory;
	private WordPressCampaignSyncListener $listener;

	protected function setUp(): void {

		parent::setUp();

		$this->container = Mockery::mock( ContainerInterface::class );

		ContainerRegistry::set( $this->container );

		$this->post_type = Mockery::mock( WordPressCampaignPostType::class );
		$this->mapper = Mockery::mock( WordPressCampaignPostMapperInterface::class );
		$this->service = Mockery::mock( WordPressCampaignServiceInterface::class );
		$this->error_transformer = Mockery::mock( ValidationErrorTransformerInterface::class );

		$this->input_factory = new AdminWordPressCampaignInputFactory( $this->mapper );
		$this->partial_input_factory = new AdminWordPressCampaignPartialInputFactory();

		$this->container
			->shouldReceive( 'make' )
			->with(
				AdminWordPressCampaignPartialInput::class,
				Mockery::type( 'array' ),
			)
			->andReturnUsing(
				// phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
				static fn ( string $class_name, array $data ) => new AdminWordPressCampaignPartialInput(
					id: TypeCaster::to_id( $data['id'] ),
					title: TypedArrayExtractor::extract_string_or_null( $data, 'title' ),
					slug: TypedArrayExtractor::extract_string_or_null( $data, 'slug' ),
					is_open: TypedArrayExtractor::extract_bool_or_false( $data, 'is_open' ),
					has_target: TypedArrayExtractor::extract_bool_or_false( $data, 'has_target' ),
					target_amount: TypedArrayExtractor::extract_int_or_zero( $data, 'target_amount' ),
				),
			);

		$this->container
			->shouldReceive( 'make' )
			->with(
				Mockery::on(
					static fn ( $class_name ) => is_a(
						$class_name,
						AbstractAdminWordPressCampaignInput::class,
						true,
					),
				),
				Mockery::type( 'array' ),
			)
			->andReturnUsing(
				// phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
				static fn ( string $class_name, array $data ) => new AdminWordPressCampaignInput(
					id: TypeCaster::to_id( $data['id'] ),
					title: TypedArrayExtractor::extract_string_or_null( $data, 'title' ),
					slug: TypedArrayExtractor::extract_string_or_null( $data, 'slug' ),
					is_enabled: TypedArrayExtractor::extract_bool_or_false( $data, 'is_enabled' ),
					is_open: TypedArrayExtractor::extract_bool_or_false( $data, 'is_open' ),
					has_target: TypedArrayExtractor::extract_bool_or_false( $data, 'has_target' ),
					target_amount: TypedArrayExtractor::extract_int_or_zero( $data, 'target_amount' ),
				),
			);

		$this->listener = new WordPressCampaignSyncListener(
			$this->post_type,
			$this->input_factory,
			$this->partial_input_factory,
			$this->service,
			$this->error_transformer,
		);
	}

	#[Test]
	public function it_registers_hooks(): void {

		$post_type = 'campaign';

		$this->post_type
			->shouldReceive( 'get_type' )
			->once()
			->andReturn( $post_type );

		$this->listener->register();

		self::assertNotFalse(
			has_filter(
				"rest_pre_insert_{$post_type}",
				$this->listener->validate( ... ),
			),
		);

		self::assertNotFalse(
			has_action(
				'wp_insert_post',
				$this->listener->sync( ... ),
			),
		);

		self::assertNotFalse(
			has_action(
				'delete_post',
				$this->listener->delete( ... ),
			),
		);
	}

	#[Test]
	public function validate_returns_post_when_validation_successful(): void {

		$prepared_post = new stdClass();
		$request = Mockery::mock( WP_REST_Request::class );

		$input_data = [
			'id' => 123,
			'title' => 'Valid Campaign',
			'meta' => [
				'is_open' => true,
				'has_target' => true,
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
		$request = Mockery::mock( WP_REST_Request::class );

		$input_data = [
			'id' => 456,
			'title' => 'Invalid Campaign',
			'meta' => [
				'is_open' => true,
				'has_target' => false,
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

		$exception = new ValidationFailedException( [], $mock_violation_list );

		$this->service
			->shouldReceive( 'validate_input' )
			->once()
			->with( Mockery::type( AdminWordPressCampaignPartialInput::class ) )
			->andThrow( $exception );

		$this->error_transformer
			->shouldReceive( 'to_string' )
			->once()
			->with( $this->identicalTo( $exception ) )
			->andReturn( 'validation failed error' );

		$result = $this->listener->validate( $prepared_post, $request );

		self::assertInstanceOf( WP_Error::class, $result );
		self::assertSame( 'campaign_validation_failed', $result->get_error_code() );
	}

	#[Test]
	public function sync_calls_save_campaign_on_service(): void {

		$post_type = 'campaign';

		$post = Mockery::mock( 'WP_Post' );
		$post->ID = 123;
		$post->post_title = 'Test Campaign';
		$post->post_type = $post_type;
		$post->meta = [
			'is_open' => true,
			'has_target' => true,
			'target_amount' => 100,
		];

		$this->post_type
			->shouldReceive( 'get_type' )
			->once()
			->andReturn( $post_type );

		$this->mapper
			->shouldReceive( 'to_array_from_post' )
			->once()
			->with( $this->identicalTo( $post ) )
			->andReturn(
				[
					'id' => $post->ID,
					'title' => $post->post_title,
					'meta' => $post->meta,
				],
			);

		$this->service
			->shouldReceive( 'save_campaign' )
			->once()
			->with( Mockery::type( AdminWordPressCampaignInput::class ) );

		$this->listener->sync( $post->ID, $post );
	}

	#[Test]
	public function sync_logs_error_when_validation_exception_thrown(): void {

		$post_type = 'campaign';

		$post = Mockery::mock( 'WP_Post' );
		$post->ID = 123;
		$post->post_title = 'Test Campaign';
		$post->post_type = $post_type;
		$post->meta = [
			'is_open' => true,
			'has_target' => true,
			'target_amount' => 0,
		];

		$this->post_type
			->shouldReceive( 'get_type' )
			->once()
			->andReturn( $post_type );

		$this->mapper
			->shouldReceive( 'to_array_from_post' )
			->once()
			->with( $this->identicalTo( $post ) )
			->andReturn(
				[
					'id' => $post->ID,
					'title' => $post->post_title,
					'meta' => $post->meta,
				],
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

		$this->post_type
			->shouldReceive( 'get_type' )
			->once()
			->andReturn( 'campaign' );

		$post = Mockery::mock( 'WP_Post' );
		$post->ID = 123;
		$post->post_title = 'Irrelevant Post';
		$post->post_type = 'other_post_type';

		$this->mapper
			->shouldNotReceive( 'to_array_from_post' );

		$this->service
			->shouldNotReceive( 'save_campaign' );

		$this->listener->sync( $post->ID, $post );
	}

	#[Test]
	public function delete_calls_delete_campaign_on_service(): void {

		$post_type = 'campaign';

		$post = Mockery::mock( 'WP_Post' );
		$post->ID = 42;
		$post->post_type = $post_type;

		$this->post_type
			->shouldReceive( 'get_type' )
			->once()
			->andReturn( $post_type );

		$this->service
			->shouldReceive( 'delete_campaign' )
			->once()
			->with( Mockery::on( static fn ( EntityId $id ) => $id->value === 42 ) );

		$this->listener->delete( $post->ID, $post );
	}

	#[Test]
	public function delete_does_nothing_if_post_type_is_not_campaign(): void {

		$post = Mockery::mock( 'WP_Post' );
		$post->ID = 42;
		$post->post_type = 'not_campaign';

		$this->post_type
			->shouldReceive( 'get_type' )
			->once()
			->andReturn( 'campaign' );

		$this->service
			->shouldNotReceive( 'delete_campaign' );

		$this->listener->delete( $post->ID, $post );
	}
}
