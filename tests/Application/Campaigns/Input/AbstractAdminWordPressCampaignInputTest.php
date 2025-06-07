<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Application\Campaigns\Input;

use Fundrik\WordPress\Application\Campaigns\Input\AbstractAdminWordPressCampaignInput;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Validator\Constraints\NotBlank;

#[CoversClass( AbstractAdminWordPressCampaignInput::class )]
final class AbstractAdminWordPressCampaignInputTest extends FundrikTestCase {

	#[Test]
	public function id_property_has_not_blank_constraint(): void {

		$this->assertPropertyHasConstraint(
			AbstractAdminWordPressCampaignInput::class,
			'id',
			NotBlank::class,
			[ 'message' => 'ID must not be blank' ]
		);
	}


	// @todo Uncomment this test after migrating to PHP 8.3+
	// Anonymous readonly classes will be supported starting with PHP 8.3.
	// #[Test]
	// public function constructor_assigns_all_values_correctly(): void {
	//
	// $stub = new readonly class(123) extends AbstractAdminWordPressCampaignInput {};
	//
	// $this->assertSame(123, $stub->id);
	// }
}
