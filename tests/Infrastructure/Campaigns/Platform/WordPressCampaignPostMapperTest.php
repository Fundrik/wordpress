<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Campaigns\Platform;

use Brain\Monkey\Functions;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignDto;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignDtoFactory;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignPostMapper;
use Fundrik\WordPress\Support\PostMetaHelper;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( WordPressCampaignPostMapper::class )]
#[UsesClass( WordPressCampaignDtoFactory::class )]
#[UsesClass( PostMetaHelper::class )]
class WordPressCampaignPostMapperTest extends FundrikTestCase {

	private WordPressCampaignPostMapper $mapper;

	protected function setUp(): void {

		parent::setUp();

		$this->mapper = new WordPressCampaignPostMapper(
			new WordPressCampaignDtoFactory()
		);
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
					'is_open'       => '0',
					'has_target'    => '1',
					'target_amount' => '1000',
				];

				return $meta_data[ $key ] ?? '';
			}
		);

		$result = $this->mapper->from_wp_post( $wp_post );

		$this->assertInstanceOf( WordPressCampaignDto::class, $result );
		$this->assertSame( $id, $result->id );
		$this->assertSame( 'Test Campaign', $result->title );
		$this->assertSame( 'test-campaign', $result->slug );
		$this->assertTrue( $result->is_enabled );
		$this->assertFalse( $result->is_open );
		$this->assertTrue( $result->has_target );
		$this->assertSame( 1000, $result->target_amount );
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

		$this->assertInstanceOf( WordPressCampaignDto::class, $result );
		$this->assertSame( $id, $result->id );
		$this->assertSame( 'Another Campaign', $result->title );
		$this->assertSame( 'another-campaign', $result->slug );
		$this->assertTrue( $result->is_enabled );
		$this->assertFalse( $result->is_open );
		$this->assertFalse( $result->has_target );
		$this->assertSame( 0, $result->target_amount );
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
					'is_open'       => 'not-a-boolean',
					'has_target'    => 'not-a-boolean',
					'target_amount' => 'invalid-int',
				];

				return $meta_data[ $key ] ?? '';
			}
		);

		$result = $this->mapper->from_wp_post( $wp_post );

		$this->assertInstanceOf( WordPressCampaignDto::class, $result );
		$this->assertSame( $id, $result->id );
		$this->assertSame( 'Faulty Campaign', $result->title );
		$this->assertSame( 'faulty-campaign', $result->slug );
		$this->assertTrue( $result->is_enabled );
		$this->assertFalse( $result->is_open );
		$this->assertFalse( $result->has_target );
		$this->assertSame( 0, $result->target_amount );
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

		$this->assertInstanceOf( WordPressCampaignDto::class, $result );
		$this->assertSame( $id, $result->id );
		$this->assertSame( 'Draft Campaign', $result->title );
		$this->assertSame( 'draft-campaign', $result->slug );
		$this->assertFalse( $result->is_enabled );
	}
}
