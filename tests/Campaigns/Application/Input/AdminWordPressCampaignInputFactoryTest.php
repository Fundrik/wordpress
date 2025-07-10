<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Campaigns\Application\Input;

use Brain\Monkey\Functions;
use Fundrik\Core\Infrastructure\Interfaces\ContainerInterface;
use Fundrik\WordPress\Campaigns\Application\Input\AdminWordPressCampaignInput;
use Fundrik\WordPress\Campaigns\Application\Input\AdminWordPressCampaignInputFactory;
use Fundrik\WordPress\Campaigns\Application\Input\Exceptions\InvalidAdminWordPressCampaignInputException;
use Fundrik\WordPress\Infrastructure\Campaigns\WordPressCampaignPostType;
use Fundrik\WordPress\Infrastructure\Container\ContainerRegistry;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use stdClass;

#[CoversClass( AdminWordPressCampaignInputFactory::class )]
final class AdminWordPressCampaignInputFactoryTest extends FundrikTestCase {

	private ContainerInterface&MockInterface $container;
	private WordPressCampaignPostType&MockInterface $post_type;

	private AdminWordPressCampaignInputFactory $factory;

	protected function setUp(): void {

		parent::setUp();

		$this->container = Mockery::mock( ContainerInterface::class );
		$this->post_type = Mockery::mock( WordPressCampaignPostType::class );

		$this->factory = new AdminWordPressCampaignInputFactory( $this->post_type );

		ContainerRegistry::set( $this->container );
	}

	#[Test]
	public function from_array_creates_input_correctly(): void {

		$expected = new AdminWordPressCampaignInput(
			id: 10,
			title: 'Sample Campaign',
			slug: 'sample-campaign',
			is_enabled: true,
			is_open: true,
			has_target: true,
			target_amount: 1_500,
		);

		$data = [
			'id' => 10,
			'title' => 'Sample Campaign',
			'slug' => 'sample-campaign',
			'is_enabled' => true,
			'is_open' => true,
			'has_target' => true,
			'target_amount' => 1_500,
		];

		$this->container
			->shouldReceive( 'make' )
			->once()
			->with( AdminWordPressCampaignInput::class, $data )
			->andReturn( $expected );

		$input = $this->factory->from_array( $data );

		$this->assertSame( $expected, $input );
	}

	#[Test]
	#[DataProvider( 'incomplete_input_provider' )]
	public function from_array_throws_exception_when_required_fields_missing( array $data, string $key, ): void {

		$this->expectException( InvalidAdminWordPressCampaignInputException::class );
		$this->expectExceptionMessage( "Failed to build AdminWordPressCampaignInput: Missing required key '{$key}'" );

		$this->factory->from_array( $data );
	}

	#[Test]
	#[DataProvider( 'invalid_type_provider' )]
	public function from_array_throws_exception_when_field_has_invalid_type( array $data, string $key ): void {

		$this->expectException( InvalidAdminWordPressCampaignInputException::class );
		$this->expectExceptionMessageMatches(
			"/Failed to build AdminWordPressCampaignInput: Invalid value type at key '{$key}'/",
		);

		$this->factory->from_array( $data );
	}

	#[Test]
	public function from_array_throws_runtime_exception_if_returned_object_invalid(): void {

		$valid_data = [
			'id' => 1,
			'title' => 'Valid Title',
			'slug' => 'valid-slug',
			'is_enabled' => true,
			'is_open' => false,
			'has_target' => true,
			'target_amount' => 100,
		];

		$this->container
			->shouldReceive( 'make' )
			->once()
			->with( AdminWordPressCampaignInput::class, Mockery::type( 'array' ) )
			->andReturn( new stdClass() );

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage(
			'Factory returned an instance of stdClass, but Fundrik\WordPress\Campaigns\Application\Input\AdminWordPressCampaignInput expected.',
		);

		$this->factory->from_array( $valid_data );
	}

	#[Test]
	public function from_wp_post_creates_input_correctly(): void {

		$id = 10;

		$data = [
			'id' => $id,
			'title' => 'Test Campaign',
			'slug' => 'test-campaign',
			'is_enabled' => true,
			'is_open' => false,
			'has_target' => true,
			'target_amount' => 1_500,
		];

		$expected = new AdminWordPressCampaignInput(
			id: 10,
			title: 'Test Campaign',
			slug: 'test-campaign',
			is_enabled: true,
			is_open: false,
			has_target: true,
			target_amount: 1_500,
		);

		$wp_post = Mockery::mock( 'WP_Post' );
		$wp_post->ID = $id;
		$wp_post->post_title = 'Test Campaign';
		$wp_post->post_name = 'test-campaign';
		$wp_post->post_status = 'publish';

		Functions\expect( 'metadata_exists' )
			->times( 3 )
			->andReturn( true );

		Functions\when( 'get_post_meta' )->alias(
			// phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
			static function ( $post_id, $key ) {
				$meta = [
					'is_open' => '0',
					'has_target' => '1',
					'target_amount' => '1500',
				];

				return $meta[ $key ] ?? '';
			},
		);

		$this->container
			->shouldReceive( 'make' )
			->once()
			->with( AdminWordPressCampaignInput::class, $data )
			->andReturn( $expected );

		$input = $this->factory->from_wp_post( $wp_post );

		$this->assertSame( $expected, $input );
	}

	#[Test]
	#[DataProvider( 'missing_meta_provider' )]
	public function from_wp_post_throws_exception_when_meta_missing( string $missing_key ): void {

		$id = 42;

		$wp_post = Mockery::mock( 'WP_Post' );
		$wp_post->ID = $id;
		$wp_post->post_title = 'Title';
		$wp_post->post_name = 'slug';
		$wp_post->post_status = 'publish';

		Functions\when( 'metadata_exists' )->alias(
			// phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
			static fn ( $type, $post_id, $key ) => $key !== $missing_key,
		);

		Functions\when( 'get_post_meta' )->alias(
			// phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
			static fn ( $post_id, $key ) => match ( $key ) {
				'is_open' => '1',
				'has_target' => '1',
				'target_amount' => '1000',
			},
		);

		$this->expectException( InvalidAdminWordPressCampaignInputException::class );
		$this->expectExceptionMessage(
			"Invalid or missing post meta when building AdminWordPressCampaignInput from WP_Post: Missing required meta key '{$missing_key}' for post {$id}",
		);

		$this->factory->from_wp_post( $wp_post );
	}

	#[Test]
	#[DataProvider( 'invalid_meta_value_provider' )]
	public function from_wp_post_throws_exception_when_meta_invalid_type( string $key, string $raw_value ): void {

		$id = 77;

		$wp_post = Mockery::mock( 'WP_Post' );
		$wp_post->ID = $id;
		$wp_post->post_title = 'Title';
		$wp_post->post_name = 'slug';
		$wp_post->post_status = 'publish';

		Functions\when( 'metadata_exists' )->alias(
			// phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
			static fn ( $type, $post_id, $meta_key ) => true,
		);

		Functions\when( 'get_post_meta' )->alias(
			// phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
			static fn ( $post_id, $meta_key ) => match ( $meta_key ) {
				'is_open' => $key === 'is_open' ? $raw_value : '1',
				'has_target' => $key === 'has_target' ? $raw_value : '1',
				'target_amount' => $key === 'target_amount' ? $raw_value : '1000',
			},
		);

		$this->expectException( InvalidAdminWordPressCampaignInputException::class );
		$this->expectExceptionMessageMatches(
			"/Invalid or missing post meta when building AdminWordPressCampaignInput from WP_Post: Invalid value for meta key '{$key}'/",
		);

		$this->factory->from_wp_post( $wp_post );
	}

	public static function incomplete_input_provider(): array {

		return [
			'missing id' => [
				[
					'title' => 'Title',
					'slug' => 'slug',
					'is_enabled' => true,
					'is_open' => true,
					'has_target' => true,
					'target_amount' => 100,
				],
				'id',
			],
			'missing title' => [
				[
					'id' => 1,
					'slug' => 'slug',
					'is_enabled' => true,
					'is_open' => true,
					'has_target' => true,
					'target_amount' => 100,
				],
				'title',
			],
			'missing slug' => [
				[
					'id' => 1,
					'title' => 'Title',
					'is_enabled' => true,
					'is_open' => true,
					'has_target' => true,
					'target_amount' => 100,
				],
				'slug',
			],
			'missing is_enabled' => [
				[
					'id' => 1,
					'title' => 'Title',
					'slug' => 'slug',
					'is_open' => true,
					'has_target' => true,
					'target_amount' => 100,
				],
				'is_enabled',
			],
			'missing is_open' => [
				[
					'id' => 1,
					'title' => 'Title',
					'slug' => 'slug',
					'is_enabled' => true,
					'has_target' => true,
					'target_amount' => 100,
				],
				'is_open',
			],
			'missing has_target' => [
				[
					'id' => 1,
					'title' => 'Title',
					'slug' => 'slug',
					'is_enabled' => true,
					'is_open' => true,
					'target_amount' => 100,
				],
				'has_target',
			],
			'missing target_amount' => [
				[
					'id' => 1,
					'title' => 'Title',
					'slug' => 'slug',
					'is_enabled' => true,
					'is_open' => true,
					'has_target' => true,
				],
				'target_amount',
			],
		];
	}

	public static function invalid_type_provider(): array {

		return [
			'id as string' => [
				[
					'id' => 'not-int',
					'title' => 'Title',
					'slug' => 'slug',
					'is_enabled' => true,
					'is_open' => true,
					'has_target' => true,
					'target_amount' => 100,
				],
				'id',
			],
			'title as int' => [
				[
					'id' => 1,
					'title' => 123,
					'slug' => 'slug',
					'is_enabled' => true,
					'is_open' => true,
					'has_target' => true,
					'target_amount' => 100,
				],
				'title',
			],
			'slug as array' => [
				[
					'id' => 1,
					'title' => 'Title',
					'slug' => [ 'slug' ],
					'is_enabled' => true,
					'is_open' => true,
					'has_target' => true,
					'target_amount' => 100,
				],
				'slug',
			],
			'is_enabled as string' => [
				[
					'id' => 1,
					'title' => 'Title',
					'slug' => 'slug',
					'is_enabled' => 'enabled',
					'is_open' => true,
					'has_target' => true,
					'target_amount' => 100,
				],
				'is_enabled',
			],
			'is_open as int' => [
				[
					'id' => 1,
					'title' => 'Title',
					'slug' => 'slug',
					'is_enabled' => true,
					'is_open' => 'open',
					'has_target' => true,
					'target_amount' => 100,
				],
				'is_open',
			],
			'has_target as null' => [
				[
					'id' => 1,
					'title' => 'Title',
					'slug' => 'slug',
					'is_enabled' => true,
					'is_open' => true,
					'has_target' => null,
					'target_amount' => 100,
				],
				'has_target',
			],
			'target_amount as string' => [
				[
					'id' => 1,
					'title' => 'Title',
					'slug' => 'slug',
					'is_enabled' => true,
					'is_open' => true,
					'has_target' => true,
					'target_amount' => 'one hundred',
				],
				'target_amount',
			],
		];
	}

	public static function missing_meta_provider(): array {

		return [
			'missing is_open' => [ 'is_open' ],
			'missing has_target' => [ 'has_target' ],
			'missing target_amount' => [ 'target_amount' ],
		];
	}

	public static function invalid_meta_value_provider(): array {

		return [
			'is_open not bool' => [ 'is_open', 'not-a-bool' ],
			'has_target not bool' => [ 'has_target', 'not-a-bool' ],
			'target_amount not int' => [ 'target_amount', 'not-an-int' ],
		];
	}
}
