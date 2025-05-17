<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Application\Campaigns;

use Fundrik\Core\Domain\Campaigns\Campaign;
use Fundrik\Core\Domain\Campaigns\CampaignTarget;
use Fundrik\Core\Domain\EntityId;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignDto;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignDtoFactory;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaign;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass( WordPressCampaignDtoFactory::class )]
#[UsesClass( WordPressCampaign::class )]
class WordPressCampaignDtoFactoryTest extends TestCase {

	#[Test]
	public function creates_dto_from_array(): void {

		$data = [
			'id'            => 123,
			'title'         => 'Array Campaign',
			'slug'          => 'array-campaign',
			'is_enabled'    => true,
			'is_open'       => true,
			'has_target'    => true,
			'target_amount' => 1500,
		];

		$dto = ( new WordPressCampaignDtoFactory() )->from_array( $data );

		$this->assertInstanceOf( WordPressCampaignDto::class, $dto );
		$this->assertSame( 123, $dto->id );
		$this->assertSame( 'Array Campaign', $dto->title );
		$this->assertSame( 'array-campaign', $dto->slug );
		$this->assertTrue( $dto->is_enabled );
		$this->assertTrue( $dto->is_open );
		$this->assertTrue( $dto->has_target );
		$this->assertSame( 1500, $dto->target_amount );
	}

	#[Test]
	public function creates_dto_from_campaign(): void {

		$campaign = new WordPressCampaign(
			new Campaign(
				id: EntityId::create( 456 ),
				title: 'Domain Campaign',
				is_enabled: false,
				is_open: true,
				target: new CampaignTarget( is_enabled: false, amount: 0 ),
			),
			slug: 'domain-campaign',
		);

		$dto = ( new WordPressCampaignDtoFactory() )->from_campaign( $campaign );

		$this->assertInstanceOf( WordPressCampaignDto::class, $dto );
		$this->assertSame( 456, $dto->id );
		$this->assertSame( 'Domain Campaign', $dto->title );
		$this->assertSame( 'domain-campaign', $dto->slug );
		$this->assertFalse( $dto->is_enabled );
		$this->assertTrue( $dto->is_open );
		$this->assertFalse( $dto->has_target );
		$this->assertSame( 0, $dto->target_amount );
	}
}
