<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Campaigns\Application;

use Fundrik\Core\Domain\Campaigns\Campaign;
use Fundrik\Core\Domain\Campaigns\CampaignTarget;
use Fundrik\Core\Domain\Campaigns\CampaignTitle;
use Fundrik\Core\Domain\EntityId;
use Fundrik\WordPress\Campaigns\Application\Exceptions\InvalidWordPressCampaignDtoException;
use Fundrik\WordPress\Campaigns\Application\Input\AdminWordPressCampaignInput;
use Fundrik\WordPress\Campaigns\Application\WordPressCampaignDto;
use Fundrik\WordPress\Campaigns\Application\WordPressCampaignDtoFactory;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaign;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaignSlug;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( WordPressCampaignDtoFactory::class )]
#[UsesClass( WordPressCampaign::class )]
#[UsesClass( WordPressCampaignSlug::class )]
#[UsesClass( AdminWordPressCampaignInput::class )]
#[UsesClass( WordPressCampaignDto::class )]
final class WordPressCampaignDtoFactoryTest extends FundrikTestCase {

	private WordPressCampaignDtoFactory $dto_factory;

	protected function setUp(): void {

		parent::setUp();

		$this->dto_factory = new WordPressCampaignDtoFactory();
	}

	#[Test]
	public function creates_dto_from_array(): void {

		$data = [
			'id' => 123,
			'title' => 'Array Campaign',
			'slug' => 'array-campaign',
			'is_enabled' => true,
			'is_open' => true,
			'has_target' => true,
			'target_amount' => 1_500,
		];

		$dto = $this->dto_factory->from_array( $data );

		$this->assertInstanceOf( WordPressCampaignDto::class, $dto );
		$this->assertSame( 123, $dto->id );
		$this->assertSame( 'Array Campaign', $dto->title );
		$this->assertSame( 'array-campaign', $dto->slug );
		$this->assertTrue( $dto->is_enabled );
		$this->assertTrue( $dto->is_open );
		$this->assertTrue( $dto->has_target );
		$this->assertSame( 1_500, $dto->target_amount );
	}

	#[Test]
	#[DataProvider( 'incomplete_input_provider' )]
	public function from_array_throws_exception_when_required_fields_missing( array $data, string $key, ): void {

		$this->expectException( InvalidWordPressCampaignDtoException::class );
		$this->expectExceptionMessage( "Failed to build WordPressCampaignDto: Missing required key '{$key}'" );

		$this->dto_factory->from_array( $data );
	}

	#[Test]
	#[DataProvider( 'invalid_type_provider' )]
	public function from_array_throws_exception_when_field_has_invalid_type( array $data, string $key ): void {

		$this->expectException( InvalidWordPressCampaignDtoException::class );
		$this->expectExceptionMessageMatches(
			"/Failed to build WordPressCampaignDto: Invalid value type at key '{$key}'/",
		);

		$this->dto_factory->from_array( $data );
	}

	#[Test]
	public function creates_dto_from_campaign(): void {

		$campaign = new WordPressCampaign(
			new Campaign(
				id: EntityId::create( 456 ),
				title: CampaignTitle::create( 'Domain Campaign' ),
				is_enabled: false,
				is_open: true,
				target: CampaignTarget::create( is_enabled: false, amount: 0 ),
			),
			slug: WordPressCampaignSlug::create( 'domain-campaign' ),
		);

		$dto = $this->dto_factory->from_campaign( $campaign );

		$this->assertInstanceOf( WordPressCampaignDto::class, $dto );
		$this->assertSame( 456, $dto->id );
		$this->assertSame( 'Domain Campaign', $dto->title );
		$this->assertSame( 'domain-campaign', $dto->slug );
		$this->assertFalse( $dto->is_enabled );
		$this->assertTrue( $dto->is_open );
		$this->assertFalse( $dto->has_target );
		$this->assertSame( 0, $dto->target_amount );
	}

	#[Test]
	public function creates_dto_from_input(): void {

		$input = new AdminWordPressCampaignInput(
			id: 789,
			title: 'Input Campaign',
			slug: 'input-campaign',
			is_enabled: true,
			is_open: false,
			has_target: true,
			target_amount: 3_000,
		);

		$dto = $this->dto_factory->from_input( $input );

		$this->assertInstanceOf( WordPressCampaignDto::class, $dto );
		$this->assertSame( 789, $dto->id );
		$this->assertSame( 'Input Campaign', $dto->title );
		$this->assertSame( 'input-campaign', $dto->slug );
		$this->assertTrue( $dto->is_enabled );
		$this->assertFalse( $dto->is_open );
		$this->assertTrue( $dto->has_target );
		$this->assertSame( 3_000, $dto->target_amount );
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
}
