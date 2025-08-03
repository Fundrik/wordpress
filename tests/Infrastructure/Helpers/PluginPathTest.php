<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Helpers;

use Fundrik\WordPress\Infrastructure\Helpers\PluginPath;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( PluginPath::class )]
final class PluginPathTest extends FundrikTestCase {

	#[Test]
	public function blocks_path_resolves_relative_to_base(): void {

		$expected = PluginPath::BASE . 'assets/js/blocks/';
		$this->assertSame( $expected, PluginPath::Blocks->get_full_path() );
	}

	#[Test]
	public function blocks_manifest_path_resolves_relative_to_base(): void {

		$expected = PluginPath::BASE . 'assets/js/blocks/blocks-manifest.php';
		$this->assertSame( $expected, PluginPath::BlocksManifest->get_full_path() );
	}

	#[Test]
	public function appends_suffix_when_provided(): void {

		$suffix = 'my-file.js';
		$expected = PluginPath::BASE . 'assets/js/blocks/' . $suffix;

		$this->assertSame( $expected, PluginPath::Blocks->get_full_path( $suffix ) );
	}
}
