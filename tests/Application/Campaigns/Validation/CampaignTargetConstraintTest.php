<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Application\Campaigns\Validation;

use Fundrik\WordPress\Application\Campaigns\Validation\CampaignTargetConstraint;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Validator\Constraint;

#[CoversClass( CampaignTargetConstraint::class )]
class CampaignTargetConstraintTest extends FundrikTestCase {

	#[Test]
	public function it_extends_symfony_constraint(): void {

		$constraint = new CampaignTargetConstraint();

		$this->assertInstanceOf( Constraint::class, $constraint );
	}

	#[Test]
	public function it_has_default_error_messages(): void {

		$constraint = new CampaignTargetConstraint();

		$this->assertSame(
			'Target amount must be greater than zero when targeting is enabled.',
			$constraint->enabled_invalid
		);

		$this->assertSame(
			'Target amount must be zero when targeting is disabled.',
			$constraint->disabled_invalid
		);
	}

	#[Test]
	public function it_returns_class_constraint_target(): void {

		$constraint = new CampaignTargetConstraint();

		$this->assertSame( Constraint::CLASS_CONSTRAINT, $constraint->getTargets() );
	}
}
