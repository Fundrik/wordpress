<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Application\Campaigns\Input;

use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignInput;
use Fundrik\WordPress\Application\Campaigns\Validation\CampaignTargetConstraint;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

#[CoversClass( AdminWordPressCampaignInput::class )]
#[UsesClass( AdminWordPressCampaignInput::class )]
final class AdminWordPressCampaignInputTest extends FundrikTestCase {

	#[Test]
	public function class_has_campaign_target_constraint_attribute(): void {

		$this->assert_class_has_attribute( AdminWordPressCampaignInput::class, CampaignTargetConstraint::class );
	}

	#[Test]
	public function id_property_has_positive_constraint(): void {

		$this->assert_has_attribute_instance_of(
			AdminWordPressCampaignInput::class,
			'id',
			Positive::class,
			[ 'message' => 'ID must be a positive integer' ],
		);
	}

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
	public function can_create_instance_and_properties_are_assigned(): void {

		$input = new AdminWordPressCampaignInput(
			id: 123,
			title: 'Test Campaign',
			slug: 'test-campaign',
			is_enabled: true,
			is_open: false,
			has_target: true,
			target_amount: 100,
		);

		$this->assertSame( 123, $input->id );
		$this->assertSame( 'Test Campaign', $input->title );
		$this->assertSame( 'test-campaign', $input->slug );
		$this->assertTrue( $input->is_enabled );
		$this->assertFalse( $input->is_open );
		$this->assertTrue( $input->has_target );
		$this->assertSame( 100, $input->target_amount );
	}
}
