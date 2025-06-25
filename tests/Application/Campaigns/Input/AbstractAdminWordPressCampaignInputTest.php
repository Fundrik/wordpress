<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Application\Campaigns\Input;

use Fundrik\WordPress\Application\Campaigns\Input\Abstracts\AbstractAdminWordPressCampaignInput;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignInput;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Symfony\Component\Validator\Constraints\NotBlank;

#[CoversClass( AdminWordPressCampaignInput::class )]
#[UsesClass( AbstractAdminWordPressCampaignInput::class )]
final class AbstractAdminWordPressCampaignInputTest extends FundrikTestCase {

	#[Test]
	public function title_property_has_not_blank_constraint(): void {

		$this->assert_has_attribute_instance_of(
			AbstractAdminWordPressCampaignInput::class,
			'title',
			NotBlank::class,
			[ 'message' => 'Title must not be blank' ],
		);
	}

	#[Test]
	public function slug_property_has_not_blank_constraint(): void {

		$this->assert_has_attribute_instance_of(
			AbstractAdminWordPressCampaignInput::class,
			'slug',
			NotBlank::class,
			[ 'message' => 'Slug must not be blank' ],
		);
	}

	// phpcs:disable
	// @todo Uncomment this test after migrating to PHP 8.3+
	// Anonymous readonly classes will be supported starting with PHP 8.3.
	// #[Test]
	// public function constructor_assigns_all_values_correctly(): void {
	//
	// $stub = new readonly class(123, 'Test campaign', 'test-campaign', false, true, true, 1_500) extends AbstractAdminWordPressCampaignInput {};
	//
	// $this->assertSame(123, $stub->id);
	// $this->assertSame('Test campaign', $stub->title);
	// $this->assertSame('test-campaign', $stub->slug);
	// $this->assertFalse($stub->is_enabled);
	// $this->assertTrue($stub->is_open);
	// $this->assertTrue($stub->has_target);
	// $this->assertSame(1_500, $stub->target_amount);
	// }
	// phpcs:enable
}
