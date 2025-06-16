<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Support;

use Fundrik\WordPress\Support\Nspace;
use Fundrik\WordPress\Support\Path;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( Nspace::class )]
final class NspaceTest extends FundrikTestCase {

	#[Test]
	public function base_constant_is_expected_string(): void {

		$this->assertSame( 'Fundrik\WordPress', Nspace::BASE );
	}

	#[Test]
	public function get_full_class_name_by_path_returns_full_class_name_for_valid_path(): void {

		$full_path = Path::PHP_BASE . 'Infrastructure/Migrations/FooBar.php';

		$result = Nspace::get_full_class_name_by_path( $full_path );

		$expected = Nspace::BASE . '\Infrastructure\Migrations\FooBar';

		$this->assertSame( $expected, $result );
	}

	#[Test]
	public function get_full_class_name_by_path_returns_null_for_path_without_base(): void {

		$path = '/var/www/html/other_dir/SomeClass.php';

		$result = Nspace::get_full_class_name_by_path( $path );

		$this->assertNull( $result );
	}
}
