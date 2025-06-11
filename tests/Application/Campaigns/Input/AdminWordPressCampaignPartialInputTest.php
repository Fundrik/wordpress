<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Application\Campaigns\Input;

use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignPartialInput;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Validator\Constraints\NotBlank;

#[CoversClass( AdminWordPressCampaignPartialInput::class )]
final class AdminWordPressCampaignPartialInputTest extends FundrikTestCase {

	#[Test]
	public function title_property_has_not_blank_constraint_allowing_null(): void {

		$this->assert_has_attribute_instance_of(
			AdminWordPressCampaignPartialInput::class,
			'title',
			NotBlank::class,
			[
				'allowNull' => true,
				'message'   => 'Title must not be blank',
			]
		);
	}

	#[Test]
	public function slug_property_has_not_blank_constraint_allowing_null(): void {

		$this->assert_has_attribute_instance_of(
			AdminWordPressCampaignPartialInput::class,
			'slug',
			NotBlank::class,
			[
				'allowNull' => true,
				'message'   => 'Slug must not be blank',
			]
		);
	}

	#[Test]
	public function constructor_assigns_all_values_correctly(): void {

		$input = new AdminWordPressCampaignPartialInput(
			id: 42,
			is_open: true,
			has_target: true,
			target_amount: 1000,
			title: 'New title',
			slug: 'new-slug',
		);

		$this->assertSame( 42, $input->id );
		$this->assertTrue( $input->is_open );
		$this->assertTrue( $input->has_target );
		$this->assertSame( 1000, $input->target_amount );
		$this->assertSame( 'New title', $input->title );
		$this->assertSame( 'new-slug', $input->slug );
	}

	#[Test]
	public function constructor_allows_null_title_and_slug(): void {

		$input = new AdminWordPressCampaignPartialInput(
			id: 7,
			is_open: false,
			has_target: false,
			target_amount: 0,
		);

		$this->assertSame( 7, $input->id );
		$this->assertFalse( $input->is_open );
		$this->assertFalse( $input->has_target );
		$this->assertSame( 0, $input->target_amount );
		$this->assertNull( $input->title );
		$this->assertNull( $input->slug );
	}
}
