<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Domain\Campaigns;

use Fundrik\Core\Domain\Campaigns\Campaign;
use Fundrik\Core\Domain\Campaigns\CampaignTarget;
use Fundrik\Core\Domain\EntityId;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaign;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass( WordPressCampaign::class )]
final class WordPressCampaignTest extends TestCase {

	#[Test]
	public function campaign_returns_all_expected_values(): void {

		$id = 42;

		$campaign = new WordPressCampaign(
			new Campaign(
				id: EntityId::create( $id ),
				title: 'Test Campaign',
				is_enabled: true,
				is_open: false,
				target: new CampaignTarget( true, 1000 ),
			),
			slug: 'test-campaign',
		);

		$this->assertSame( $id, $campaign->get_id() );
		$this->assertSame( 'Test Campaign', $campaign->get_title() );
		$this->assertSame( 'test-campaign', $campaign->get_slug() );
		$this->assertTrue( $campaign->is_enabled() );
		$this->assertFalse( $campaign->is_open() );
		$this->assertTrue( $campaign->has_target() );
		$this->assertSame( 1000, $campaign->get_target_amount() );
	}

	#[Test]
	public function campaign_without_enabled_target(): void {

		$id = 123;

		$campaign = new WordPressCampaign(
			new Campaign(
				id: EntityId::create( $id ),
				title: 'Campaign Without Target',
				is_enabled: false,
				is_open: true,
				target: new CampaignTarget( false, 0 )
			),
			slug: 'campaign-without-target',
		);

		$this->assertFalse( $campaign->has_target() );
		$this->assertSame( 0, $campaign->get_target_amount() );
		$this->assertSame( $id, $campaign->get_id() );
	}
}
