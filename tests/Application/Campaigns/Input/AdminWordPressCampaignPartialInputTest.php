<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Application\Campaigns\Input;

use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignPartialInput;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

#[CoversClass( AdminWordPressCampaignPartialInput::class )]
final class AdminWordPressCampaignPartialInputTest extends FundrikTestCase {

	#[Test]
	public function title_property_has_not_blank_constraint_allowing_null(): void {

		$this->assertPropertyHasConstraint(
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
	public function target_amount_property_has_positive_or_zero_constraint(): void {

		$this->assertPropertyHasConstraint(
			AdminWordPressCampaignPartialInput::class,
			'target_amount',
			PositiveOrZero::class,
			[ 'message' => 'Target amount must be zero or positive' ]
		);
	}

	#[Test]
	public function constructor_assigns_all_values_correctly(): void {

		$input = new AdminWordPressCampaignPartialInput(
			id: 42,
			title: 'Updated Title',
			slug: 'updated-slug',
			is_enabled: true,
			is_open: false,
			has_target: true,
			target_amount: 2500,
		);

		$this->assertSame( 42, $input->id );
		$this->assertSame( 'Updated Title', $input->title );
		$this->assertSame( 'updated-slug', $input->slug );
		$this->assertTrue( $input->is_enabled );
		$this->assertFalse( $input->is_open );
		$this->assertTrue( $input->has_target );
		$this->assertSame( 2500, $input->target_amount );
	}

	#[Test]
	public function constructor_assigns_null_values_when_omitted(): void {

		$input = new AdminWordPressCampaignPartialInput( id: 7 );

		$this->assertSame( 7, $input->id );
		$this->assertNull( $input->title );
		$this->assertNull( $input->slug );
		$this->assertNull( $input->is_enabled );
		$this->assertNull( $input->is_open );
		$this->assertNull( $input->has_target );
		$this->assertNull( $input->target_amount );
	}
}
