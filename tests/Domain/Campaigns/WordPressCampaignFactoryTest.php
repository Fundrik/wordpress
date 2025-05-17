<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Domain\Campaigns;

use Fundrik\Core\Application\Campaigns\CampaignDtoFactory;
use Fundrik\Core\Domain\Campaigns\CampaignFactory;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignDto;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaign;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaignFactory;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass( WordPressCampaignFactory::class )]
#[UsesClass( WordPressCampaign::class )]
class WordPressCampaignFactoryTest extends TestCase {

	private WordPressCampaignFactory $factory;

	protected function setUp(): void {

		parent::setUp();

		$this->factory = new WordPressCampaignFactory(
			new CampaignFactory(),
			new CampaignDtoFactory(),
		);
	}

	#[Test]
	public function creates_campaign_with_int_id(): void {

		$id = 1;

		$campaign = $this->factory->create(
			new WordPressCampaignDto(
				id: $id,
				title: 'Test Campaign',
				slug: 'test-campaign',
				is_enabled: true,
				is_open: false,
				has_target: true,
				target_amount: 1000,
			)
		);

		$this->assertInstanceOf( WordPressCampaign::class, $campaign );

		$this->assertSame( $id, $campaign->get_id() );
		$this->assertSame( 'Test Campaign', $campaign->get_title() );
		$this->assertSame( 'test-campaign', $campaign->get_slug() );
		$this->assertSame( true, $campaign->is_enabled() );
		$this->assertSame( false, $campaign->is_open() );
		$this->assertSame( true, $campaign->has_target() );
		$this->assertSame( 1000, $campaign->get_target_amount() );
	}

	#[Test]
	public function creates_campaign_with_uuid_id(): void {

		$uuid = '0196934d-e117-71aa-ab63-cff172292bd2';

		$campaign = $this->factory->create(
			new WordPressCampaignDto(
				id: $uuid,
				title: 'UUID Campaign',
				slug: 'uuid-campaign',
				is_enabled: true,
				is_open: true,
				has_target: false,
				target_amount: 0,
			)
		);

		$this->assertInstanceOf( WordPressCampaign::class, $campaign );

		$this->assertSame( $uuid, $campaign->get_id() );
	}

	#[Test]
	public function throws_when_campaign_target_is_invalid(): void {

		$this->expectException( InvalidArgumentException::class );

		$this->factory->create(
			new WordPressCampaignDto(
				id: 1,
				title: 'Invalid Campaign',
				slug: 'invalid-campaign',
				is_enabled: true,
				is_open: true,
				has_target: true,
				target_amount: 0,
			)
		);
	}

	#[Test]
	public function throws_when_entity_id_is_invalid(): void {

		$this->expectException( InvalidArgumentException::class );

		$this->factory->create(
			new WordPressCampaignDto(
				id: -1,
				title: 'Invalid Campaign',
				slug: 'invalid-campaign',
				is_enabled: true,
				is_open: true,
				has_target: false,
				target_amount: 0,
			)
		);
	}
}
