<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Application\Campaigns\Input;

use Fundrik\WordPress\Application\Campaigns\Input\Abstracts\AbstractAdminWordPressCampaignInput;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignInput;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( AdminWordPressCampaignInput::class )]
#[UsesClass( AbstractAdminWordPressCampaignInput::class )]
final class AdminWordPressCampaignInputTest extends FundrikTestCase {

	#[Test]
	public function class_extends_abstract_admin_input(): void {

		$this->assertTrue(
			is_subclass_of( AdminWordPressCampaignInput::class, AbstractAdminWordPressCampaignInput::class ),
			'AdminWordPressCampaignInput must extend AbstractAdminWordPressCampaignInput',
		);
	}
}
