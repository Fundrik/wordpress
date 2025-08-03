<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Components\Campaigns\Domain;

use Fundrik\WordPress\Components\Campaigns\Domain\CampaignSlug;
use Fundrik\WordPress\Components\Campaigns\Domain\Exceptions\InvalidCampaignSlugException;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( CampaignSlug::class )]
final class CampaignSlugTest extends FundrikTestCase {

	#[Test]
	public function creates_with_valid_slug(): void {

		$slug = CampaignSlug::create( 'save-the-planet' );

		$this->assertSame( 'save-the-planet', $slug->value );
	}

	#[Test]
	public function trims_slug_before_storing(): void {

		$slug = CampaignSlug::create( "\n\t  help-kids-today \t\n" );

		$this->assertSame( 'help-kids-today', $slug->value );
	}

	#[Test]
	public function throws_when_slug_is_empty(): void {

		$this->expectException( InvalidCampaignSlugException::class );
		$this->expectExceptionMessage( 'Campaign slug cannot be empty or whitespace.' );

		CampaignSlug::create( '' );
	}

	#[Test]
	public function throws_when_slug_is_only_whitespace(): void {

		$this->expectException( InvalidCampaignSlugException::class );
		$this->expectExceptionMessage( 'Campaign slug cannot be empty or whitespace.' );

		CampaignSlug::create( "   \n\t   " );
	}

	#[Test]
	public function accepts_unicode_and_symbols(): void {

		$slug = CampaignSlug::create( 'поддержать-лес-🌲' );

		$this->assertSame( 'поддержать-лес-🌲', $slug->value );
	}
}
