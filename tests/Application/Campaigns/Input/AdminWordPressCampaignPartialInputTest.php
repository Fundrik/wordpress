<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Application\Campaigns\Input;

use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignPartialInput;
use Fundrik\WordPress\Application\Campaigns\Validation\CampaignTargetConstraint;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

#[CoversClass( AdminWordPressCampaignPartialInput::class )]
final class AdminWordPressCampaignPartialInputTest extends FundrikTestCase {

	#[Test]
	public function class_has_campaign_target_constraint_attribute(): void {

		$this->assert_class_has_attribute( AdminWordPressCampaignPartialInput::class, CampaignTargetConstraint::class );
	}

	#[Test]
	public function id_property_has_positive_constraint(): void {

		$this->assert_has_attribute_instance_of(
			AdminWordPressCampaignPartialInput::class,
			'id',
			Positive::class,
			[
				'message' => 'ID must be a positive integer',
			],
		);
	}

	#[Test]
	public function title_property_has_not_blank_constraint_allowing_null(): void {

		$this->assert_has_attribute_instance_of(
			AdminWordPressCampaignPartialInput::class,
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
			AdminWordPressCampaignPartialInput::class,
			'slug',
			NotBlank::class,
			[
				'allowNull' => true,
				'message' => 'Slug must not be blank',
			],
		);
	}

	#[Test]
	public function can_create_instance_and_properties_are_assigned(): void {

		$input = new AdminWordPressCampaignPartialInput(
			id: 42,
			is_open: true,
			has_target: false,
			target_amount: 500,
			title: 'Partial Title',
			slug: 'partial-slug',
		);

		$this->assertSame( 42, $input->id );
		$this->assertTrue( $input->is_open );
		$this->assertFalse( $input->has_target );
		$this->assertSame( 500, $input->target_amount );
		$this->assertSame( 'Partial Title', $input->title );
		$this->assertSame( 'partial-slug', $input->slug );
	}

	#[Test]
	public function can_create_instance_with_null_optional_properties(): void {

		$input = new AdminWordPressCampaignPartialInput(
			id: 42,
			is_open: false,
			has_target: true,
			target_amount: 0,
			title: null,
			slug: null,
		);

		$this->assertSame( 42, $input->id );
		$this->assertFalse( $input->is_open );
		$this->assertTrue( $input->has_target );
		$this->assertSame( 0, $input->target_amount );
		$this->assertNull( $input->title );
		$this->assertNull( $input->slug );
	}
}
