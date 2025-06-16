<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Campaigns\Platform;

use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignPostType;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( WordPressCampaignPostType::class )]
final class WordPressCampaignPostTypeTest extends FundrikTestCase {

	private WordPressCampaignPostType $post_type;

	protected function setUp(): void {

		parent::setUp();

		$this->post_type = new WordPressCampaignPostType();
	}

	#[Test]
	public function it_has_correct_constants(): void {

		self::assertSame( 'is_open', WordPressCampaignPostType::META_IS_OPEN );
		self::assertSame( 'has_target', WordPressCampaignPostType::META_HAS_TARGET );
		self::assertSame( 'target_amount', WordPressCampaignPostType::META_TARGET_AMOUNT );

		self::assertSame( 'fundrik/campaign-settings', WordPressCampaignPostType::CAMPAIGN_SETTINGS_BLOCK );
	}

	#[Test]
	public function it_returns_correct_and_localized_labels(): void {

		$labels = $this->post_type->get_labels();

		$expected_keys = [
			'name',
			'singular_name',
			'menu_name',
			'name_admin_bar',
			'add_new',
			'add_new_item',
			'new_item',
			'edit_item',
			'view_item',
			'all_items',
			'search_items',
			'parent_item_colon',
			'not_found',
			'not_found_in_trash',
			'featured_image',
			'set_featured_image',
			'remove_featured_image',
			'use_featured_image',
			'archives',
			'insert_into_item',
			'uploaded_to_this_item',
			'items_list',
			'items_list_navigation',
			'filter_items_list',
		];

		foreach ( $expected_keys as $key ) {
			self::assertArrayHasKey( $key, $labels );
		}

		self::assertSame( 'Campaigns', $labels['name'] );
		self::assertSame( 'Campaign', $labels['singular_name'] );
		self::assertSame( 'Campaigns', $labels['menu_name'] );
		self::assertSame( 'Campaign', $labels['name_admin_bar'] );
	}

	#[Test]
	public function it_returns_correct_post_type(): void {

		self::assertSame( 'fundrik_campaign', $this->post_type->get_type() );
	}

	#[Test]
	public function it_returns_correct_rewrite_slug(): void {

		self::assertSame( 'campaigns', $this->post_type->get_slug() );
	}

	#[Test]
	public function it_returns_correct_meta_fields(): void {

		$meta_fields = $this->post_type->get_meta_fields();

		$meta_is_open       = WordPressCampaignPostType::META_IS_OPEN;
		$meta_has_target    = WordPressCampaignPostType::META_HAS_TARGET;
		$meta_target_amount = WordPressCampaignPostType::META_TARGET_AMOUNT;

		self::assertIsArray( $meta_fields );
		self::assertArrayHasKey( $meta_is_open, $meta_fields );
		self::assertArrayHasKey( $meta_has_target, $meta_fields );
		self::assertArrayHasKey( $meta_target_amount, $meta_fields );

		self::assertSame(
			[
				'type'    => 'boolean',
				'default' => true,
			],
			$meta_fields[ $meta_is_open ]
		);

		self::assertSame(
			[ 'type' => 'boolean' ],
			$meta_fields[ $meta_has_target ]
		);

		self::assertSame(
			[ 'type' => 'string' ],
			$meta_fields[ $meta_target_amount ]
		);
	}

	#[Test]
	public function it_returns_correct_template_blocks(): void {

		$template_blocks = $this->post_type->get_template_blocks();

		self::assertIsArray( $template_blocks );
		self::assertCount( 1, $template_blocks );
		self::assertSame(
			[ WordPressCampaignPostType::CAMPAIGN_SETTINGS_BLOCK ],
			$template_blocks[0]
		);
	}

	#[Test]
	public function it_returns_specific_blocks(): void {

		$blocks = $this->post_type->get_specific_blocks();

		self::assertIsArray( $blocks );
		self::assertContains( WordPressCampaignPostType::CAMPAIGN_SETTINGS_BLOCK, $blocks );
		self::assertCount( 1, $blocks );
	}
}
