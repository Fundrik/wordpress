<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Campaigns\Platform;

use Brain\Monkey\Functions;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignDtoFactory;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignPostMapper;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignPostType;
use Fundrik\WordPress\Support\PostMeta;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( WordPressCampaignPostMapper::class )]
#[UsesClass( WordPressCampaignDtoFactory::class )]
#[UsesClass( PostMeta::class )]
final class WordPressCampaignPostMapperTest extends FundrikTestCase {

	private WordPressCampaignPostMapper $mapper;

	protected function setUp(): void {

		parent::setUp();

		$this->mapper = new WordPressCampaignPostMapper(
			Mockery::mock( WordPressCampaignPostType::class )
		);
	}

	#[Test]
	public function to_array_from_post_extracts_expected_data(): void {

		$id = 10;

		$wp_post              = Mockery::mock( 'WP_Post' );
		$wp_post->ID          = $id;
		$wp_post->post_title  = 'Test Campaign';
		$wp_post->post_name   = 'test-campaign';
		$wp_post->post_status = 'publish';

		Functions\when( 'get_post_meta' )->alias(
			function ( $post_id, $key ) {
				$meta = [
					'is_open'       => '1',
					'has_target'    => '0',
					'target_amount' => '500',
				];

				return $meta[ $key ] ?? '';
			}
		);

		$result = $this->mapper->to_array_from_post( $wp_post );

		$this->assertSame(
			[
				'id'            => $id,
				'title'         => 'Test Campaign',
				'slug'          => 'test-campaign',
				'is_enabled'    => true,
				'is_open'       => true,
				'has_target'    => false,
				'target_amount' => 500,
			],
			$result
		);
	}

	#[Test]
	public function to_array_from_post_handles_missing_meta(): void {

		$id = 20;

		$wp_post              = Mockery::mock( 'WP_Post' );
		$wp_post->ID          = $id;
		$wp_post->post_title  = 'Campaign Without Meta';
		$wp_post->post_name   = 'campaign-no-meta';
		$wp_post->post_status = 'publish';

		Functions\when( 'get_post_meta' )->justReturn( '' );

		$result = $this->mapper->to_array_from_post( $wp_post );

		$this->assertSame(
			[
				'id'            => $id,
				'title'         => 'Campaign Without Meta',
				'slug'          => 'campaign-no-meta',
				'is_enabled'    => true,
				'is_open'       => false,
				'has_target'    => false,
				'target_amount' => 0,
			],
			$result
		);
	}

	#[Test]
	public function to_array_from_post_handles_draft_post(): void {

		$id = 30;

		$wp_post              = Mockery::mock( 'WP_Post' );
		$wp_post->ID          = $id;
		$wp_post->post_title  = 'Draft Campaign';
		$wp_post->post_name   = 'draft-campaign';
		$wp_post->post_status = 'draft';

		Functions\when( 'get_post_meta' )->alias(
			function ( $post_id, $key ) {
				$meta = [
					'is_open'       => '1',
					'has_target'    => '1',
					'target_amount' => '2000',
				];

				return $meta[ $key ] ?? '';
			}
		);

		$result = $this->mapper->to_array_from_post( $wp_post );

		$this->assertSame(
			[
				'id'            => $id,
				'title'         => 'Draft Campaign',
				'slug'          => 'draft-campaign',
				'is_enabled'    => false,
				'is_open'       => true,
				'has_target'    => true,
				'target_amount' => 2000,
			],
			$result
		);
	}
}
