<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Application\Campaigns\Input;

use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignPartialInput;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignPartialInputFactory;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( AdminWordPressCampaignPartialInputFactory::class )]
#[UsesClass( AdminWordPressCampaignPartialInput::class )]
final class AdminWordPressCampaignPartialInputFactoryTest extends FundrikTestCase {

	private AdminWordPressCampaignPartialInputFactory $factory;

	protected function setUp(): void {

		parent::setUp();

		$this->factory = new AdminWordPressCampaignPartialInputFactory();
	}

	#[Test]
	public function from_array_creates_input_correctly(): void {

		$data = [
			'id'            => '22',
			'title'         => 'Partial Campaign',
			'slug'          => 'partial-campaign',
			'is_enabled'    => '1',
			'is_open'       => false,
			'has_target'    => true,
			'target_amount' => '2500',
		];

		$input = $this->factory->from_array( $data );

		$this->assertInstanceOf( AdminWordPressCampaignPartialInput::class, $input );
		$this->assertSame( 22, $input->id );
		$this->assertSame( 'Partial Campaign', $input->title );
		$this->assertSame( 'partial-campaign', $input->slug );
		$this->assertTrue( $input->is_enabled );
		$this->assertFalse( $input->is_open );
		$this->assertTrue( $input->has_target );
		$this->assertSame( 2500, $input->target_amount );
	}

	#[Test]
	public function from_array_sets_missing_fields_to_null(): void {

		$data = [ 'id' => '99' ];

		$input = $this->factory->from_array( $data );

		$this->assertInstanceOf( AdminWordPressCampaignPartialInput::class, $input );
		$this->assertSame( 99, $input->id );
		$this->assertNull( $input->title );
		$this->assertNull( $input->slug );
		$this->assertNull( $input->is_enabled );
		$this->assertNull( $input->is_open );
		$this->assertNull( $input->has_target );
		$this->assertNull( $input->target_amount );
	}
}
