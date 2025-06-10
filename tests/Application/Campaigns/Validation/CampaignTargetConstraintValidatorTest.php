<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Application\Campaigns\Validation;

use Brain\Monkey;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignInput;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignInputFactory;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignPartialInput;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignPartialInputFactory;
use Fundrik\WordPress\Application\Campaigns\Validation\CampaignTargetConstraint;
use Fundrik\WordPress\Application\Campaigns\Validation\CampaignTargetConstraintValidator;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces\WordPressCampaignPostMapperInterface;
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
class CampaignTargetConstraintValidatorTest extends ConstraintValidatorTestCase {

	private AdminWordPressCampaignInputFactory $factory;
	private AdminWordPressCampaignPartialInputFactory $factory_partial;

	protected function setUp(): void {

		parent::setUp();

		Monkey\setUp();

		Monkey\Functions\stubEscapeFunctions();
		Monkey\Functions\stubTranslationFunctions();

		$mapper_mock = $this->createMock( WordPressCampaignPostMapperInterface::class );

		$this->factory         = new AdminWordPressCampaignInputFactory( $mapper_mock );
		$this->factory_partial = new AdminWordPressCampaignPartialInputFactory();
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

		$data = [
			'id'            => 42,
			'has_target'    => true,
			'target_amount' => 500,
		];

		$input = $this->factory->from_array( $data );

		$this->validator->validate( $input, new CampaignTargetConstraint() );

		$this->assertNoViolation();
	}

	#[Test]
	public function valid_without_target(): void {

		$data = [
			'id'            => 42,
			'has_target'    => false,
			'target_amount' => 0,
		];

		$input = $this->factory->from_array( $data );

		$this->validator->validate( $input, new CampaignTargetConstraint() );

		$this->assertNoViolation();
	}

	#[Test]
	public function invalid_with_target_and_zero_amount(): void {

		$data = [
			'id'            => 42,
			'has_target'    => true,
			'target_amount' => 0,
		];

		$input = $this->factory->from_array( $data );

		$this->validator->validate( $input, new CampaignTargetConstraint() );

		$this
			->buildViolation( 'Target amount must be greater than zero when targeting is enabled.' )
			->atPath( 'property.path.target_amount' )
			->assertRaised();
	}

	#[Test]
	public function invalid_without_target_and_nonzero_amount(): void {

		$data = [
			'id'            => 42,
			'has_target'    => false,
			'target_amount' => 100,
		];

		$input = $this->factory->from_array( $data );

		$this->validator->validate( $input, new CampaignTargetConstraint() );

		$this
			->buildViolation( 'Target amount must be zero when targeting is disabled.' )
			->atPath( 'property.path.target_amount' )
			->assertRaised();
	}

	#[Test]
	public function valid_partial_input_does_not_raise_violation(): void {

		$data = [
			'id'   => 42,
			'meta' => [
				'is_open'       => true,
				'has_target'    => false,
				'target_amount' => 0,
			],
		];

		$input = $this->factory_partial->from_array( $data );

		$this->validator->validate( $input, new CampaignTargetConstraint() );

		$this->assertNoViolation();
	}

	#[Test]
	public function throws_exception_for_invalid_input_type(): void {

		$invalid_input = new stdClass();
		$constraint    = new CampaignTargetConstraint();

		$this->expectException( UnexpectedValueException::class );
		$this->expectExceptionMessage( stdClass::class );

		$this->validator->validate( $invalid_input, $constraint );
	}

	#[Test]
	public function throws_exception_for_invalid_constraint_type_with_valid_input(): void {

		$data = [
			'id'            => 42,
			'has_target'    => true,
			'target_amount' => 100,
		];

		$input = $this->factory->from_array( $data );

		$invalid_constraint = new class() extends Constraint {};

		$this->expectException( UnexpectedValueException::class );
		$this->expectExceptionMessage( get_class( $invalid_constraint ) );

		$this->validator->validate( $input, $invalid_constraint );
	}
}
