<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Campaigns\Application\Validation;

use Brain\Monkey;
use Fundrik\WordPress\Campaigns\Application\Input\AdminWordPressCampaignInput;
use Fundrik\WordPress\Campaigns\Application\Input\AdminWordPressCampaignInputFactory;
use Fundrik\WordPress\Campaigns\Application\Input\AdminWordPressCampaignPartialInput;
use Fundrik\WordPress\Campaigns\Application\Input\AdminWordPressCampaignPartialInputFactory;
use Fundrik\WordPress\Campaigns\Application\Validation\CampaignTargetConstraint;
use Fundrik\WordPress\Campaigns\Application\Validation\CampaignTargetConstraintValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use stdClass;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

#[CoversClass( CampaignTargetConstraintValidator::class )]
#[UsesClass( AdminWordPressCampaignInput::class )]
#[UsesClass( AdminWordPressCampaignInputFactory::class )]
#[UsesClass( AdminWordPressCampaignPartialInput::class )]
#[UsesClass( AdminWordPressCampaignPartialInputFactory::class )]
#[UsesClass( CampaignTargetConstraint::class )]
final class CampaignTargetConstraintValidatorTest extends ConstraintValidatorTestCase {

	private CampaignTargetConstraint $campaign_constraint;

	protected function setUp(): void {

		parent::setUp();

		Monkey\setUp();

		Monkey\Functions\stubEscapeFunctions();
		Monkey\Functions\stubTranslationFunctions();

		$this->campaign_constraint = new CampaignTargetConstraint();
	}

	protected function tearDown(): void {

		Monkey\tearDown();

		parent::tearDown();
	}

	protected function createValidator(): CampaignTargetConstraintValidator {

		return new CampaignTargetConstraintValidator();
	}

	#[Test]
	public function valid_with_target(): void {

		$input = $this->create_campaign_input( has_target: true, target_amount: 500 );

		$this->validator->validate( $input, $this->campaign_constraint );

		$this->assertNoViolation();
	}

	#[Test]
	public function valid_without_target(): void {

		$input = $this->create_campaign_input( has_target: false, target_amount: 0 );

		$this->validator->validate( $input, $this->campaign_constraint );

		$this->assertNoViolation();
	}

	#[Test]
	public function invalid_with_target_and_zero_amount(): void {

		$input = $this->create_campaign_input( has_target: true, target_amount: 0 );

		$this->validator->validate( $input, $this->campaign_constraint );

		$this
			->buildViolation( 'Target amount must be greater than zero when targeting is enabled.' )
			->atPath( 'property.path.target_amount' )
			->assertRaised();
	}

	#[Test]
	public function invalid_without_target_and_nonzero_amount(): void {

		$input = $this->create_campaign_input( has_target: false, target_amount: 100 );

		$this->validator->validate( $input, $this->campaign_constraint );

		$this
			->buildViolation( 'Target amount must be zero when targeting is disabled.' )
			->atPath( 'property.path.target_amount' )
			->assertRaised();
	}

	#[Test]
	public function valid_partial_input_does_not_raise_violation(): void {

		$input = new AdminWordPressCampaignPartialInput( id: 42, is_open: true, has_target: false, target_amount: 0 );

		$this->validator->validate( $input, $this->campaign_constraint );

		$this->assertNoViolation();
	}

	#[Test]
	public function throws_exception_for_invalid_input_type(): void {

		$invalid_input = new stdClass();

		$this->expectException( UnexpectedValueException::class );
		$this->expectExceptionMessage( stdClass::class );

		$this->validator->validate( $invalid_input, $this->campaign_constraint );
	}

	#[Test]
	public function throws_exception_for_invalid_constraint_type_with_valid_input(): void {

		$input = $this->create_campaign_input( has_target: true, target_amount: 100 );

		// phpcs:ignore SlevomatCodingStandard.Classes.EmptyLinesAroundClassBraces.MultipleEmptyLinesAfterOpeningBrace, SlevomatCodingStandard.Classes.EmptyLinesAroundClassBraces.IncorrectEmptyLinesBeforeClosingBrace
		$invalid_constraint = new class() extends Constraint {};

		$this->expectException( UnexpectedValueException::class );
		$this->expectExceptionMessage( $invalid_constraint::class );

		$this->validator->validate( $input, $invalid_constraint );
	}

	private function create_campaign_input(
		int $id = 42,
		string $title = 'Test Campaign',
		string $slug = 'test-campaign',
		bool $is_enabled = true,
		bool $is_open = true,
		bool $has_target = false,
		int $target_amount = 0,
	): AdminWordPressCampaignInput {

		return new AdminWordPressCampaignInput(
			id: $id,
			title: $title,
			slug: $slug,
			is_enabled: $is_enabled,
			is_open: $is_open,
			has_target: $has_target,
			target_amount: $target_amount,
		);
	}
}
