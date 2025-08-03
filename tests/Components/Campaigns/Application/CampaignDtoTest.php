<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Components\Campaigns\Application;

use Fundrik\WordPress\Components\Campaigns\Application\CampaignDto;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( CampaignDto::class )]
final class CampaignDtoTest extends FundrikTestCase {

	#[Test]
	public function dto_holds_all_expected_values(): void {

		$dto = $this->make_campaign_dto();

		$this->assertSame( 1, $dto->id );
		$this->assertSame( 'Test Campaign', $dto->title );
		$this->assertSame( 'test-campaign', $dto->slug );
		$this->assertTrue( $dto->is_active );
		$this->assertTrue( $dto->is_open );
		$this->assertTrue( $dto->has_target );
		$this->assertSame( 100, $dto->target_amount );
	}
}
