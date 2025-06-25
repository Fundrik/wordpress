<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Application\Campaigns\Input;

use Fundrik\WordPress\Application\Campaigns\Input\Abstracts\AbstractAdminWordPressCampaignPartialInput;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignPartialInput;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( AdminWordPressCampaignPartialInput::class )]
final class AdminWordPressCampaignPartialInputTest extends FundrikTestCase {

	#[Test]
	public function class_extends_abstract_admin_input(): void {

		$this->assertTrue(
			is_subclass_of(
				AdminWordPressCampaignPartialInput::class,
				AbstractAdminWordPressCampaignPartialInput::class,
			),
			'AdminWordPressCampaignPartialInput must extend AbstractAdminWordPressCampaignPartialInput',
		);
	}
}
