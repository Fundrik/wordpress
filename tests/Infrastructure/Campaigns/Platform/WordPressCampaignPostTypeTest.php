<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Campaigns\Platform;

use Brain\Monkey\Functions;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignPostType;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( WordPressCampaignPostType::class )]
class WordPressCampaignPostTypeTest extends FundrikTestCase {

	#[Test]
	public function it_has_correct_constants(): void {

		self::assertSame( 'is_open', WordPressCampaignPostType::META_IS_OPEN );
		self::assertSame( 'has_target', WordPressCampaignPostType::META_HAS_TARGET );
		self::assertSame( 'target_amount', WordPressCampaignPostType::META_TARGET_AMOUNT );
		self::assertSame( 'collected_amount', WordPressCampaignPostType::META_COLLECTED_AMOUNT );
	}

	#[Test]
	public function it_returns_correct_and_localized_labels(): void {

		Functions\when( '__' )->returnArg();

		$labels = WordPressCampaignPostType::get_labels();

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

		self::assertSame( 'fundrik_campaign', WordPressCampaignPostType::get_type() );
	}

	#[Test]
	public function it_returns_correct_rewrite_slug(): void {

		self::assertSame( 'campaigns', WordPressCampaignPostType::get_rewrite_slug() );
	}
}
