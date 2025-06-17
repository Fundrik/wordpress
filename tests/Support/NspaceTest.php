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
	public function resolve_class_name_by_path_returns_expected_class_name_for_valid_path(): void {

		$full_path = Path::PHP_BASE . 'Infrastructure/Migrations/FooBar.php';

		$result = Nspace::resolve_class_name_by_path( $full_path );

		$expected = Nspace::BASE . '\Infrastructure\Migrations\FooBar';

		$this->assertSame( $expected, $result );
	}

	#[Test]
	public function resolve_class_name_by_path_returns_null_for_invalid_path(): void {

		$path = '/var/www/html/other_dir/SomeClass.php';

		$result = Nspace::resolve_class_name_by_path( $path );

		$this->assertNull( $result );
	}

	#[Test]
	public function resolve_class_name_by_path_normalizes_path_with_dot_segments(): void {

		$full_path = Path::PHP_BASE . 'Infrastructure/../Domain/./ValueObject/Foo.php';

		$result = Nspace::resolve_class_name_by_path( $full_path );

		$expected = Nspace::BASE . '\Domain\ValueObject\Foo';

		$this->assertSame( $expected, $result );
	}

	#[Test]
	public function resolve_class_name_by_path_removes_extra_slashes(): void {

		$full_path = Path::PHP_BASE . 'Infrastructure//Domain///Foo.php';

		$result = Nspace::resolve_class_name_by_path( $full_path );

		$expected = Nspace::BASE . '\Infrastructure\Domain\Foo';

		$this->assertSame( $expected, $result );
	}

	#[Test]
	public function resolve_class_name_by_path_handles_tests_subpath_case_insensitively(): void {

		$full_path = Path::PHP_BASE . 'infrastructure/tests/ExampleTest.php';

		$result = Nspace::resolve_class_name_by_path( $full_path );

		$expected = Nspace::BASE . '\Infrastructure\Tests\ExampleTest';

		$this->assertSame( $expected, $result );
	}
}
