<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Application\Campaigns\Input;

use Fundrik\WordPress\Application\Campaigns\Input\Abstracts\AbstractBaseAdminWordPressCampaignInput;
use Fundrik\WordPress\Application\Campaigns\Validation\CampaignTargetConstraint;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Symfony\Component\Validator\Constraints\Positive;

#[CoversClass( AbstractBaseAdminWordPressCampaignInput::class )]
#[UsesClass( CampaignTargetConstraint::class )]
final class AbstractBaseAdminWordPressCampaignInputTest extends FundrikTestCase {

	#[Test]
	public function id_property_has_positive_constraint(): void {

		$this->assert_has_attribute_instance_of(
			AbstractBaseAdminWordPressCampaignInput::class,
			'id',
			Positive::class,
			[ 'message' => 'ID must be a positive' ],
		);
	}

	#[Test]
	public function class_has_campaign_target_constraint_attribute(): void {

		$this->assert_сlass_has_attribute(
			AbstractBaseAdminWordPressCampaignInput::class,
			CampaignTargetConstraint::class,
		);
	}

	// @todo Uncomment this test after migrating to PHP 8.3+
	// Anonymous readonly classes will be supported starting with PHP 8.3.
	// #[Test]
	// public function constructor_assigns_all_values_correctly(): void {
	//
	// $stub = new readonly class(123, true, true, 1_500) extends AbstractBaseAdminWordPressCampaignInput {};
	//
	// $this->assertSame(123, $stub->id);
	// $this->assertTrue($stub->is_open);
	// $this->assertTrue($stub->has_target);
	// $this->assertSame(1_500, $stub->target_amount);
	// }
}
