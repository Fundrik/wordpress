<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Campaigns\Platform;

use Brain\Monkey\Functions;
use Fundrik\Core\Application\Campaigns\CampaignDtoFactory;
use Fundrik\Core\Domain\Campaigns\CampaignDto;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\CampaignPostToCampaignDtoMapper;
use Fundrik\WordPress\Support\PostMetaHelper;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( CampaignPostToCampaignDtoMapper::class )]
#[UsesClass( PostMetaHelper::class )]
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

		$id = 1;

		$wp_post = Mockery::mock( 'WP_Post' );

		$wp_post->ID          = $id;
		$wp_post->post_title  = 'Test Campaign';
		$wp_post->post_name   = 'test-campaign';
		$wp_post->post_status = 'publish';

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
		$this->assertEquals( $id, $result->id );
		$this->assertEquals( 'Test Campaign', $result->title );
		$this->assertEquals( 'test-campaign', $result->slug );
		$this->assertTrue( $result->is_enabled );
		$this->assertFalse( $result->is_open );
		$this->assertTrue( $result->has_target );
		$this->assertEquals( 1000, $result->target_amount );
		$this->assertEquals( 500, $result->collected_amount );
	}

	#[Test]
	public function from_wp_post_handles_missing_meta_data(): void {

		$id = 2;

		$wp_post              = Mockery::mock( 'WP_Post' );
		$wp_post->ID          = $id;
		$wp_post->post_title  = 'Another Campaign';
		$wp_post->post_name   = 'another-campaign';
		$wp_post->post_status = 'publish';

		Functions\when( 'get_post_meta' )->justReturn( '' );

		$result = $this->mapper->from_wp_post( $wp_post );

		$this->assertInstanceOf( CampaignDto::class, $result );
		$this->assertEquals( $id, $result->id );
		$this->assertEquals( 'Another Campaign', $result->title );
		$this->assertEquals( 'another-campaign', $result->slug );
		$this->assertTrue( $result->is_enabled );
		$this->assertFalse( $result->is_open );
		$this->assertFalse( $result->has_target );
		$this->assertEquals( 0, $result->target_amount );
		$this->assertEquals( 0, $result->collected_amount );
	}

	#[Test]
	public function from_wp_post_handles_incorrect_meta_data(): void {

		$id = 3;

		$wp_post              = Mockery::mock( 'WP_Post' );
		$wp_post->ID          = $id;
		$wp_post->post_title  = 'Faulty Campaign';
		$wp_post->post_name   = 'faulty-campaign';
		$wp_post->post_status = 'publish';

		Functions\when( 'get_post_meta' )->alias(
			function ( $post_id, $key ) {

				$meta_data = [
					'is_open'          => 'not-a-boolean',
					'has_target'       => 'not-a-boolean',
					'target_amount'    => 'invalid-int',
					'collected_amount' => 'invalid-int',
				];

				return $meta_data[ $key ] ?? '';
			}
		);

		$result = $this->mapper->from_wp_post( $wp_post );

		$this->assertInstanceOf( CampaignDto::class, $result );
		$this->assertEquals( $id, $result->id );
		$this->assertEquals( 'Faulty Campaign', $result->title );
		$this->assertEquals( 'faulty-campaign', $result->slug );
		$this->assertTrue( $result->is_enabled );
		$this->assertFalse( $result->is_open );
		$this->assertFalse( $result->has_target );
		$this->assertEquals( 0, $result->target_amount );
		$this->assertEquals( 0, $result->collected_amount );
	}

	#[Test]
	public function from_wp_post_creates_campaign_dto_for_draft_status(): void {

		$id = 4;

		$wp_post              = Mockery::mock( 'WP_Post' );
		$wp_post->ID          = $id;
		$wp_post->post_title  = 'Draft Campaign';
		$wp_post->post_name   = 'draft-campaign';
		$wp_post->post_status = 'draft';

		Functions\when( 'get_post_meta' )->justReturn( '' );

		$result = $this->mapper->from_wp_post( $wp_post );

		$this->assertInstanceOf( CampaignDto::class, $result );
		$this->assertEquals( $id, $result->id );
		$this->assertEquals( 'Draft Campaign', $result->title );
		$this->assertEquals( 'draft-campaign', $result->slug );
		$this->assertFalse( $result->is_enabled );
	}
}
