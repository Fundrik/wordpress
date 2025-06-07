<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Support;

use Fundrik\WordPress\Support\Path;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( Path::class )]
class PathTest extends FundrikTestCase {

	#[Test]
	public function blocks_returns_correct_path(): void {

		$expected = FUNDRIK_PATH . 'assets/js/blocks/';
		$this->assertSame( $expected, Path::blocks() );
	}

	#[Test]
	public function blocks_manifest_returns_correct_path(): void {

		$expected = FUNDRIK_PATH . 'assets/js/blocks/blocks-manifest.php';
		$this->assertSame( $expected, Path::blocks_manifest() );
	}
}
