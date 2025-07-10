<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Campaigns\Application\Input;

use Fundrik\Core\Infrastructure\Interfaces\ContainerInterface;
use Fundrik\WordPress\Campaigns\Application\Input\AdminWordPressCampaignPartialInput;
use Fundrik\WordPress\Campaigns\Application\Input\AdminWordPressCampaignPartialInputFactory;
use Fundrik\WordPress\Campaigns\Application\Input\Exceptions\InvalidAdminWordPressCampaignPartialInputException;
use Fundrik\WordPress\Infrastructure\Container\ContainerRegistry;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesFunction;
use RuntimeException;
use stdClass;

#[CoversClass( AdminWordPressCampaignPartialInputFactory::class )]
#[UsesClass( ContainerRegistry::class )]
#[UsesFunction( 'fundrik' )]
final class AdminWordPressCampaignPartialInputFactoryTest extends FundrikTestCase {

	private ContainerInterface&MockInterface $container;

	private AdminWordPressCampaignPartialInputFactory $factory;

	protected function setUp(): void {

		parent::setUp();

		$this->container = Mockery::mock( ContainerInterface::class );
		ContainerRegistry::set( $this->container );

		$this->factory = new AdminWordPressCampaignPartialInputFactory();
	}

	#[Test]
	public function from_array_creates_input_correctly(): void {

		$data = [
			'id' => '22',
			'title' => 'Partial Campaign',
			'slug' => 'partial-campaign',
			'meta' => [
				'is_open' => false,
				'has_target' => true,
				'target_amount' => '2500',
			],
		];

		$expected = new AdminWordPressCampaignPartialInput(
			id: 22,
			title: 'Partial Campaign',
			slug: 'partial-campaign',
			is_open: false,
			has_target: true,
			target_amount: 2_500,
		);

		$this->container
			->shouldReceive( 'make' )
			->once()
			->with(
				AdminWordPressCampaignPartialInput::class,
				[
					'id' => 22,
					'title' => 'Partial Campaign',
					'slug' => 'partial-campaign',
					'is_open' => false,
					'has_target' => true,
					'target_amount' => 2_500,
				],
			)
			->andReturn( $expected );

		$input = $this->factory->from_array( $data );

		$this->assertSame( $expected, $input );
	}

	#[Test]
	public function from_array_sets_optional_fields_to_null_when_missing(): void {

		$data = [
			'id' => '99',
			'meta' => [
				'is_open' => true,
				'has_target' => false,
				'target_amount' => 0,
			],
		];

		$expected = new AdminWordPressCampaignPartialInput(
			id: 99,
			title: null,
			slug: null,
			is_open: true,
			has_target: false,
			target_amount: 0,
		);

		$this->container
			->shouldReceive( 'make' )
			->once()
			->with(
				AdminWordPressCampaignPartialInput::class,
				[
					'id' => 99,
					'title' => null,
					'slug' => null,
					'is_open' => true,
					'has_target' => false,
					'target_amount' => 0,
				],
			)
			->andReturn( $expected );

		$input = $this->factory->from_array( $data );

		$this->assertSame( $expected, $input );
	}

	#[Test]
	#[DataProvider( 'incomplete_input_provider' )]
	public function from_array_throws_exception_when_required_fields_missing( array $data, string $key, ): void {

		$this->expectException( InvalidAdminWordPressCampaignPartialInputException::class );
		$this->expectExceptionMessage(
			"Failed to build AdminWordPressCampaignPartialInput: Missing required key '{$key}'",
		);

		$this->factory->from_array( $data );
	}

	#[Test]
	#[DataProvider( 'invalid_type_provider' )]
	public function from_array_throws_exception_when_field_has_invalid_type( array $data, string $key ): void {

		$this->expectException( InvalidAdminWordPressCampaignPartialInputException::class );
		$this->expectExceptionMessageMatches(
			"/Failed to build AdminWordPressCampaignPartialInput: Invalid value type at key '{$key}'/",
		);

		$this->factory->from_array( $data );
	}

	#[Test]
	public function from_array_throws_runtime_exception_if_returned_object_invalid(): void {

		$valid_data = [
			'id' => 22,
			'title' => 'Partial Campaign',
			'slug' => 'partial-campaign',
			'meta' => [
				'is_open' => false,
				'has_target' => true,
				'target_amount' => 250,
			],
		];

		$this->container
			->shouldReceive( 'make' )
			->once()
			->with( AdminWordPressCampaignPartialInput::class, Mockery::type( 'array' ) )
			->andReturn( new stdClass() );

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage(
			'Factory returned an instance of stdClass, but Fundrik\WordPress\Campaigns\Application\Input\AdminWordPressCampaignPartialInput expected.',
		);

		$this->factory->from_array( $valid_data );
	}

	public static function incomplete_input_provider(): array {

		return [
			'missing id' => [
				[
					'meta' => [
						'is_open' => true,
						'has_target' => true,
						'target_amount' => 100,
					],
				],
				'id',
			],
			'missing meta' => [
				[
					'id' => 1,
				],
				'meta',
			],
			'missing is_open in meta' => [
				[
					'id' => 1,
					'meta' => [
						'has_target' => true,
						'target_amount' => 100,
					],
				],
				'is_open',
			],
			'missing has_target in meta' => [
				[
					'id' => 1,
					'meta' => [
						'is_open' => true,
						'target_amount' => 100,
					],
				],
				'has_target',
			],
			'missing target_amount in meta' => [
				[
					'id' => 1,
					'meta' => [
						'is_open' => true,
						'has_target' => true,
					],
				],
				'target_amount',
			],
		];
	}

	public static function invalid_type_provider(): array {

		return [
			'id as string' => [
				[
					'id' => 'not-an-int',
					'title' => 'Campaign',
					'slug' => 'campaign',
					'meta' => [
						'is_open' => true,
						'has_target' => true,
						'target_amount' => 100,
					],
				],
				'id',
			],
			'title as int' => [
				[
					'id' => 1,
					'title' => 123,
					'slug' => 'campaign',
					'meta' => [
						'is_open' => true,
						'has_target' => true,
						'target_amount' => 100,
					],
				],
				'title',
			],
			'slug as array' => [
				[
					'id' => 1,
					'title' => 'Campaign',
					'slug' => [ 'slug' ],
					'meta' => [
						'is_open' => true,
						'has_target' => true,
						'target_amount' => 100,
					],
				],
				'slug',
			],
			'is_open as string' => [
				[
					'id' => 1,
					'title' => 'Campaign',
					'slug' => 'campaign',
					'meta' => [
						'is_open' => 'open',
						'has_target' => true,
						'target_amount' => 100,
					],
				],
				'is_open',
			],
			'has_target as null' => [
				[
					'id' => 1,
					'title' => 'Campaign',
					'slug' => 'campaign',
					'meta' => [
						'is_open' => true,
						'has_target' => null,
						'target_amount' => 100,
					],
				],
				'has_target',
			],
			'target_amount as string' => [
				[
					'id' => 1,
					'title' => 'Campaign',
					'slug' => 'campaign',
					'meta' => [
						'is_open' => true,
						'has_target' => true,
						'target_amount' => 'a lot',
					],
				],
				'target_amount',
			],
		];
	}

	/*#[Test]
	#[DataProvider( 'incomplete_input_provider' )]
	public function from_array_throws_exception_when_required_fields_missing_or_invalid(
		array $data,
		string $expected_message,
	): void {

		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( $expected_message );

		$this->factory->from_array( $data );
	}

	#[Test]
	public function from_array_throws_runtime_exception_if_returned_object_invalid(): void {

		$valid_data = [
			'id' => 1,
			'title' => 'Valid Title',
			'slug' => 'valid-slug',
			'meta' => [
				'is_open' => true,
				'has_target' => false,
				'target_amount' => 100,
			],
		];

		$this->container
			->shouldReceive( 'make' )
			->once()
			->with(
				AdminWordPressCampaignPartialInput::class,
				Mockery::type( 'array' ),
			)
			->andReturn( new stdClass() );

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage(
			'Factory returned an instance of stdClass, but Fundrik\WordPress\Campaigns\Application\Input\Abstracts\AbstractAdminWordPressCampaignPartialInput expected.',
		);

		$this->factory->from_array( $valid_data );
	}

	public static function incomplete_input_provider(): array {

		return [
			'missing id' => [
				[
					'meta' => [
						'is_open' => true,
						'has_target' => false,
						'target_amount' => 100,
					],
				],
				"Missing required key 'id' (expected entity ID)",
			],
			'invalid title type' => [
				[
					'id' => 1,
					'title' => [ 'not a string' ],
					'slug' => 'partial-campaign',
					'meta' => [
						'is_open' => true,
						'has_target' => true,
						'target_amount' => 100,
					],
				],
				"Invalid value at key 'title' (expected string)",
			],
			'invalid slug type' => [
				[
					'id' => 1,
					'title' => 'Valid title',
					'slug' => 123, // not a string.
					'meta' => [
						'is_open' => true,
						'has_target' => true,
						'target_amount' => 100,
					],
				],
				"Invalid value at key 'slug' (expected string)",
			],
			'missing is_open' => [
				[
					'id' => 1,
					'meta' => [
						'has_target' => true,
						'target_amount' => 100,
					],
				],
				"Missing required key 'is_open' (expected bool)",
			],
			'missing has_target' => [
				[
					'id' => 1,
					'title' => 'Partial Campaign',
					'slug' => 'partial-campaign',
					'meta' => [
						'is_open' => true,
						'target_amount' => 100,
					],
				],
				"Missing required key 'has_target' (expected bool)",
			],
			'missing target_amount' => [
				[
					'id' => 1,
					'title' => 'Partial Campaign',
					'slug' => 'partial-campaign',
					'meta' => [
						'is_open' => true,
						'has_target' => true,
					],
				],
				"Missing required key 'target_amount' (expected int)",
			],
		];
	}*/
}
