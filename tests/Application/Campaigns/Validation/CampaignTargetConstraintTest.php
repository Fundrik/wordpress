<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Application\Campaigns\Validation;

use Fundrik\WordPress\Application\Campaigns\Validation\CampaignTargetConstraint;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Validator\Constraint;

#[CoversClass( CampaignTargetConstraint::class )]
final class CampaignTargetConstraintTest extends FundrikTestCase {

	private CampaignTargetConstraint $constraint;

	protected function setUp(): void {

		parent::setUp();

		$this->constraint = new CampaignTargetConstraint();
	}

	#[Test]
	public function it_extends_symfony_constraint(): void {

		$this->assertInstanceOf( Constraint::class, $this->constraint );
	}

	#[Test]
	public function it_has_default_error_messages(): void {

		$this->assertSame(
			'Target amount must be greater than zero when targeting is enabled.',
			$this->constraint->enabled_invalid,
		);

		$this->assertSame(
			'Target amount must be zero when targeting is disabled.',
			$this->constraint->disabled_invalid,
		);
	}

	#[Test]
	public function it_returns_class_constraint_target(): void {

		$this->assertSame( Constraint::CLASS_CONSTRAINT, $this->constraint->getTargets() );
	}
}
