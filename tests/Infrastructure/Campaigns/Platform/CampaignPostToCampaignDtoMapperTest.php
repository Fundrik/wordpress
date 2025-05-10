<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Campaigns\Platform;

use Brain\Monkey\Functions;
use Fundrik\Core\Application\Campaigns\CampaignDtoFactory;
use Fundrik\Core\Domain\Campaigns\CampaignDto;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\CampaignPostToCampaignDtoMapper;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( CampaignPostToCampaignDtoMapper::class )]
class CampaignPostToCampaignDtoMapperTest extends FundrikTestCase {

	private CampaignPostToCampaignDtoMapper $mapper;
	private CampaignDtoFactory $dto_factory;

	protected function setUp(): void {

		parent::setUp();

		$this->dto_factory = new CampaignDtoFactory();

		$this->mapper = new CampaignPostToCampaignDtoMapper( $this->dto_factory );
	}

	#[Test]
	public function from_wp_post_creates_campaign_dto_correctly(): void {

		$wp_post = Mockery::mock( 'WP_Post' );

		$wp_post->ID         = 1;
		$wp_post->post_title = 'Test Campaign';
		$wp_post->post_name  = 'test-campaign';

		Functions\when( 'get_post_meta' )->alias(
			function ( $post_id, $key ) {

				$meta_data = [
					'is_open'          => '0',
					'has_target'       => '1',
					'target_amount'    => '1000',
					'collected_amount' => '500',
				];

				return $meta_data[ $key ] ?? '';
			}
		);

		$result = $this->mapper->from_wp_post( $wp_post );

		$this->assertInstanceOf( CampaignDto::class, $result );
		$this->assertEquals( 1, $result->id );
		$this->assertEquals( 'Test Campaign', $result->title );
		$this->assertEquals( 'test-campaign', $result->slug );
		$this->assertFalse( $result->is_open );
		$this->assertTrue( $result->has_target );
		$this->assertEquals( 1000, $result->target_amount );
		$this->assertEquals( 500, $result->collected_amount );
	}
}
