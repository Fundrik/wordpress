<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Domain\Campaigns;

use Fundrik\WordPress\Domain\Campaigns\WordPressCampaignSlug;
use Fundrik\WordPress\Tests\FundrikTestCase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( WordPressCampaignSlug::class )]
final class WordPressCampaignSlugTest extends FundrikTestCase {

	#[Test]
	public function creates_slug_with_valid_value(): void {

		$slug = WordPressCampaignSlug::create( 'valid-slug' );

		$this->assertSame( 'valid-slug', $slug->value );
	}

	#[Test]
	public function trims_slug_value(): void {

		$slug = WordPressCampaignSlug::create( '  trimmed-slug  ' );

		$this->assertSame( 'trimmed-slug', $slug->value );
	}

	#[Test]
	public function throws_when_slug_is_empty_string(): void {

		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Campaign slug cannot be empty or whitespace.' );

		WordPressCampaignSlug::create( '' );
	}

	#[Test]
	public function throws_when_slug_is_only_spaces(): void {

		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Campaign slug cannot be empty or whitespace.' );

		WordPressCampaignSlug::create( '   ' );
	}

	#[Test]
	public function throws_when_slug_is_only_tabs_and_newlines(): void {

		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Campaign slug cannot be empty or whitespace.' );

		WordPressCampaignSlug::create( "\t\n" );
	}
}
