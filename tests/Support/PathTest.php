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

		$this->assertSame(
			FUNDRIK_PATH . '/assets/js/blocks/',
			Path::Blocks->get()
		);
	}

	#[Test]
	public function blocks_manifest_returns_correct_path(): void {

		$this->assertSame(
			FUNDRIK_PATH . '/assets/js/blocks/blocks-manifest.php',
			Path::BlocksManifest->get()
		);
	}

	#[Test]
	public function migrations_returns_correct_path(): void {

		$this->assertSame(
			FUNDRIK_PATH . '/src/Infrastructure/Migrations/',
			Path::Migrations->get()
		);
	}
}
