<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Campaigns\Application;

use Fundrik\WordPress\Campaigns\Application\WordPressCampaignDto;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( WordPressCampaignDto::class )]
final class WordPressCampaignDtoTest extends FundrikTestCase {

	#[Test]
	public function constructor_assigns_all_values_correctly(): void {

		$dto = new WordPressCampaignDto(
			id: 123,
			title: 'My WP Campaign',
			slug: 'my-wp-campaign',
			is_enabled: true,
			is_open: false,
			has_target: true,
			target_amount: 5_000,
		);

		$this->assertSame( 123, $dto->id );
		$this->assertSame( 'My WP Campaign', $dto->title );
		$this->assertSame( 'my-wp-campaign', $dto->slug );
		$this->assertTrue( $dto->is_enabled );
		$this->assertFalse( $dto->is_open );
		$this->assertTrue( $dto->has_target );
		$this->assertSame( 5_000, $dto->target_amount );
	}

	#[Test]
	public function to_array_returns_correct_structure(): void {

		$dto = new WordPressCampaignDto(
			id: 42,
			title: 'Array Test',
			slug: 'array-test',
			is_enabled: true,
			is_open: true,
			has_target: true,
			target_amount: 1_000,
		);

		$expected = [
			'id' => 42,
			'title' => 'Array Test',
			'slug' => 'array-test',
			'is_enabled' => true,
			'is_open' => true,
			'has_target' => true,
			'target_amount' => 1_000,
		];

		$this->assertSame( $expected, $dto->to_array() );
	}
}
