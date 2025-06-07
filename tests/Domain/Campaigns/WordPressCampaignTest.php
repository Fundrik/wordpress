<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Domain\Campaigns;

use Fundrik\Core\Domain\Campaigns\Campaign;
use Fundrik\Core\Domain\Campaigns\CampaignTarget;
use Fundrik\Core\Domain\Campaigns\CampaignTitle;
use Fundrik\Core\Domain\EntityId;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaign;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaignSlug;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( WordPressCampaign::class )]
#[UsesClass( WordPressCampaignSlug::class )]
final class WordPressCampaignTest extends FundrikTestCase {

	#[Test]
	public function campaign_returns_all_expected_values(): void {

		$id = 42;

		$campaign = new WordPressCampaign(
			new Campaign(
				id: EntityId::create( $id ),
				title: CampaignTitle::create( 'Test Campaign' ),
				is_enabled: true,
				is_open: false,
				target: CampaignTarget::create( true, 1000 ),
			),
			slug: WordPressCampaignSlug::create( 'test-campaign' ),
		);

		$this->assertSame( $id, $campaign->get_id() );
		$this->assertSame( 'Test Campaign', $campaign->get_title() );
		$this->assertSame( 'test-campaign', $campaign->get_slug() );
		$this->assertTrue( $campaign->is_enabled() );
		$this->assertFalse( $campaign->is_open() );
		$this->assertTrue( $campaign->has_target() );
		$this->assertSame( 1000, $campaign->get_target_amount() );
	}
}
