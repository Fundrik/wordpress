<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Application\Campaigns\Input;

use Fundrik\WordPress\Application\Campaigns\Input\Abstracts\AbstractAdminWordPressCampaignPartialInput;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Validator\Constraints\NotBlank;

#[CoversClass( AbstractAdminWordPressCampaignPartialInput::class )]
final class AbstractAdminWordPressCampaignPartialInputTest extends FundrikTestCase {

	#[Test]
	public function title_property_has_not_blank_constraint_allowing_null(): void {

		$this->assert_has_attribute_instance_of(
			AbstractAdminWordPressCampaignPartialInput::class,
			'title',
			NotBlank::class,
			[
				'allowNull' => true,
				'message' => 'Title must not be blank',
			],
		);
	}

	#[Test]
	public function slug_property_has_not_blank_constraint_allowing_null(): void {

		$this->assert_has_attribute_instance_of(
			AbstractAdminWordPressCampaignPartialInput::class,
			'slug',
			NotBlank::class,
			[
				'allowNull' => true,
				'message' => 'Slug must not be blank',
			],
		);
	}

	// phpcs:disable
	// @todo Uncomment this test after migrating to PHP 8.3+
	// Anonymous readonly classes will be supported starting with PHP 8.3.
	// #[Test]
	// public function constructor_assigns_all_values_correctly(): void {
	//
	// $stub = new readonly class(42, 'New title', 'new-slug', true, true, 1_000) extends AbstractAdminWordPressCampaignInput {};
	//
	// $this->assertSame(42, $stub->id);
	// $this->assertSame('New title', $stub->title);
	// $this->assertSame('new-slug', $stub->slug);
	// $this->assertTrue($stub->is_open);
	// $this->assertTrue($stub->has_target);
	// $this->assertSame(1_000, $stub->target_amount);
	// }
	// phpcs:enable

	// phpcs:disable
	// @todo Uncomment this test after migrating to PHP 8.3+
	// Anonymous readonly classes will be supported starting with PHP 8.3.
	// #[Test]
	// public function constructor_allows_null_title_and_slug(): void {
	//
	// $stub = new readonly class(
	//     7,
	//     title: null,
	//     slug: null,
	//     is_open: false,
	//     has_target: false,
	//     target_amount: 0
	// ) extends AbstractAdminWordPressCampaignPartialInput {};
	//
	// $this->assertSame(7, $stub->id);
	// $this->assertFalse($stub->is_open);
	// $this->assertFalse($stub->has_target);
	// $this->assertSame(0, $stub->target_amount);
	// $this->assertNull($stub->title);
	// $this->assertNull($stub->slug);
	// }
	// phpcs:enable
}
