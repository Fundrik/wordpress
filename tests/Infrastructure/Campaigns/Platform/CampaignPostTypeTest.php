<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Campaigns\Platform;

use Brain\Monkey\Functions;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\CampaignPostType;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( CampaignPostType::class )]
class CampaignPostTypeTest extends FundrikTestCase {

	#[Test]
	public function it_has_correct_constants(): void {

		self::assertEquals( 'fundrik_campaign', CampaignPostType::TYPE );
		self::assertEquals( 'is_open', CampaignPostType::META_IS_OPEN );
		self::assertEquals( 'has_target', CampaignPostType::META_HAS_TARGET );
		self::assertEquals( 'target_amount', CampaignPostType::META_TARGET_AMOUNT );
		self::assertEquals( 'collected_amount', CampaignPostType::META_COLLECTED_AMOUNT );
		self::assertEquals( 'campaigns', CampaignPostType::REWRITE_SLUG );
	}

	#[Test]
	public function it_returns_correct_and_localized_labels(): void {

		Functions\when( '__' )->returnArg();

		$labels = CampaignPostType::get_labels();

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

		self::assertEquals( 'Campaigns', $labels['name'] );
		self::assertEquals( 'Campaign', $labels['singular_name'] );
		self::assertEquals( 'Campaigns', $labels['menu_name'] );
		self::assertEquals( 'Campaign', $labels['name_admin_bar'] );
	}
}
