<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Migrations;

use Fundrik\WordPress\Infrastructure\Migrations\MigrationValidationResult;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( MigrationValidationResult::class )]
final class MigrationValidationResultTest extends FundrikTestCase {

	#[Test]
	public function it_initializes_with_version_and_class_name(): void {

		$version = '2025_06_16_01';
		$class_name = 'Fundrik\WordPress\Infrastructure\Migrations\CreateFundrikCampaignsTable';

		$result = new MigrationValidationResult( $version, $class_name );

		$this->assertSame( $version, $result->version );
		$this->assertSame( $class_name, $result->class_name );
	}
}
