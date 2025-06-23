<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Application\Campaigns\Input;

use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignInput;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Validator\Constraints\NotBlank;

#[CoversClass( AdminWordPressCampaignInput::class )]
final class AdminWordPressCampaignInputTest extends FundrikTestCase {

	#[Test]
	public function title_property_has_not_blank_constraint(): void {

		$this->assert_has_attribute_instance_of(
			AdminWordPressCampaignInput::class,
			'title',
			NotBlank::class,
			[ 'message' => 'Title must not be blank' ],
		);
	}

	#[Test]
	public function slug_property_has_not_blank_constraint(): void {

		$this->assert_has_attribute_instance_of(
			AdminWordPressCampaignInput::class,
			'slug',
			NotBlank::class,
			[ 'message' => 'Slug must not be blank' ],
		);
	}

	#[Test]
	public function constructor_assigns_all_values_correctly(): void {

		$input = new AdminWordPressCampaignInput(
			id: 123,
			title: 'My Campaign',
			slug: 'my-campaign',
			is_enabled: true,
			is_open: false,
			has_target: true,
			target_amount: 10_000,
		);

		$this->assertSame( 123, $input->id );
		$this->assertSame( 'My Campaign', $input->title );
		$this->assertSame( 'my-campaign', $input->slug );
		$this->assertTrue( $input->is_enabled );
		$this->assertFalse( $input->is_open );
		$this->assertTrue( $input->has_target );
		$this->assertSame( 10_000, $input->target_amount );
	}
}
