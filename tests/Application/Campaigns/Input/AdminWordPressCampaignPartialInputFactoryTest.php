<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Application\Campaigns\Input;

use Fundrik\Core\Infrastructure\Interfaces\ContainerInterface;
use Fundrik\WordPress\Application\Campaigns\Input\Abstracts\AbstractAdminWordPressCampaignPartialInput;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignPartialInput;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignPartialInputFactory;
use Fundrik\WordPress\Infrastructure\Container\ContainerRegistry;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesFunction;

#[CoversClass( AdminWordPressCampaignPartialInputFactory::class )]
#[UsesClass( AbstractAdminWordPressCampaignPartialInput::class )]
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
}
