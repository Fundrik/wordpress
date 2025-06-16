<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Support;

use Fundrik\WordPress\Support\Path;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( Path::class )]
final class PathTest extends FundrikTestCase {

	#[Test]
	public function constants_have_expected_values(): void {

		$this->assertSame(
			FUNDRIK_PATH,
			Path::BASE
		);

		$this->assertSame(
			FUNDRIK_PATH . 'src/php/',
			Path::PHP_BASE
		);
	}

	#[Test]
	public function blocks_returns_correct_path(): void {

		$this->assertSame(
			Path::BASE . 'assets/js/blocks/',
			Path::Blocks->get_full_path()
		);
	}

	#[Test]
	public function blocks_manifest_returns_correct_path(): void {

		$this->assertSame(
			Path::BASE . 'assets/js/blocks/blocks-manifest.php',
			Path::BlocksManifest->get_full_path()
		);
	}

	#[Test]
	public function migrations_returns_correct_path(): void {

		$this->assertSame(
			Path::PHP_BASE . 'Infrastructure/Migrations/Files/',
			Path::MigrationFiles->get_full_path()
		);
	}

	#[Test]
	public function get_full_path_appends_suffix_correctly(): void {

		$suffix = 'example.php';

		$this->assertSame(
			Path::BASE . 'assets/js/blocks/' . $suffix,
			Path::Blocks->get_full_path( $suffix )
		);

		$this->assertSame(
			Path::PHP_BASE . 'Infrastructure/Migrations/Files/' . $suffix,
			Path::MigrationFiles->get_full_path( $suffix )
		);
	}
}
