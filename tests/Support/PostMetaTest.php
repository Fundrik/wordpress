<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Support;

use Brain\Monkey\Functions;
use Fundrik\WordPress\Support\Exceptions\InvalidPostMetaValueException;
use Fundrik\WordPress\Support\Exceptions\MissingPostMetaException;
use Fundrik\WordPress\Support\PostMeta;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( PostMeta::class )]
final class PostMetaTest extends FundrikTestCase {

	private int $post_id = 123;
	private string $meta_key = 'some_key';

	// --- BOOL ---

	#[Test]
	public function get_bool_optional_returns_bool_when_value_valid(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( true );
		Functions\expect( 'get_post_meta' )->with( $this->post_id, $this->meta_key, true )->andReturn( '1' );
		$result = PostMeta::get_bool_optional( $this->post_id, $this->meta_key );
		$this->assertTrue( $result );
	}

	#[Test]
	public function get_bool_optional_returns_null_when_meta_missing(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( false );
		$result = PostMeta::get_bool_optional( $this->post_id, $this->meta_key );
		$this->assertNull( $result );
	}

	#[Test]
	public function get_bool_optional_throws_when_value_invalid(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( true );
		Functions\expect( 'get_post_meta' )->with( $this->post_id, $this->meta_key, true )->andReturn( 'invalid_bool' );
		$this->expectException( InvalidPostMetaValueException::class );
		PostMeta::get_bool_optional( $this->post_id, $this->meta_key );
	}

	#[Test]
	public function get_bool_required_returns_bool_when_value_valid(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( true );
		Functions\expect( 'get_post_meta' )->with( $this->post_id, $this->meta_key, true )->andReturn( '0' );
		$result = PostMeta::get_bool_required( $this->post_id, $this->meta_key );
		$this->assertFalse( $result );
	}

	#[Test]
	public function get_bool_required_throws_when_meta_missing(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( false );
		$this->expectException( MissingPostMetaException::class );
		PostMeta::get_bool_required( $this->post_id, $this->meta_key );
	}

	#[Test]
	public function get_bool_required_throws_when_value_invalid(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( true );
		Functions\expect( 'get_post_meta' )->with( $this->post_id, $this->meta_key, true )->andReturn( 'invalid_bool' );
		$this->expectException( InvalidPostMetaValueException::class );
		PostMeta::get_bool_required( $this->post_id, $this->meta_key );
	}

	// --- INT ---

	#[Test]
	public function get_int_optional_returns_int_when_value_valid(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( true );
		Functions\expect( 'get_post_meta' )->with( $this->post_id, $this->meta_key, true )->andReturn( '42' );
		$result = PostMeta::get_int_optional( $this->post_id, $this->meta_key );
		$this->assertSame( 42, $result );
	}

	#[Test]
	public function get_int_optional_returns_null_when_meta_missing(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( false );
		$result = PostMeta::get_int_optional( $this->post_id, $this->meta_key );
		$this->assertNull( $result );
	}

	#[Test]
	public function get_int_optional_throws_when_value_invalid(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( true );
		Functions\expect( 'get_post_meta' )->with( $this->post_id, $this->meta_key, true )->andReturn( 'invalid_int' );
		$this->expectException( InvalidPostMetaValueException::class );
		PostMeta::get_int_optional( $this->post_id, $this->meta_key );
	}

	#[Test]
	public function get_int_required_returns_int_when_value_valid(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( true );
		Functions\expect( 'get_post_meta' )->with( $this->post_id, $this->meta_key, true )->andReturn( '100' );
		$result = PostMeta::get_int_required( $this->post_id, $this->meta_key );
		$this->assertSame( 100, $result );
	}

	#[Test]
	public function get_int_required_throws_when_meta_missing(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( false );
		$this->expectException( MissingPostMetaException::class );
		PostMeta::get_int_required( $this->post_id, $this->meta_key );
	}

	#[Test]
	public function get_int_required_throws_when_value_invalid(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( true );
		Functions\expect( 'get_post_meta' )->with( $this->post_id, $this->meta_key, true )->andReturn( 'invalid_int' );
		$this->expectException( InvalidPostMetaValueException::class );
		PostMeta::get_int_required( $this->post_id, $this->meta_key );
	}

	// --- FLOAT ---

	#[Test]
	public function get_float_optional_returns_float_when_value_valid(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( true );
		Functions\expect( 'get_post_meta' )->with( $this->post_id, $this->meta_key, true )->andReturn( '3.14' );
		$result = PostMeta::get_float_optional( $this->post_id, $this->meta_key );
		$this->assertSame( 3.14, $result );
	}

	#[Test]
	public function get_float_optional_returns_null_when_meta_missing(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( false );
		$result = PostMeta::get_float_optional( $this->post_id, $this->meta_key );
		$this->assertNull( $result );
	}

	#[Test]
	public function get_float_optional_throws_when_value_invalid(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( true );
		Functions\expect( 'get_post_meta' )->with( $this->post_id, $this->meta_key, true )->andReturn( 'invalid_float' );
		$this->expectException( InvalidPostMetaValueException::class );
		PostMeta::get_float_optional( $this->post_id, $this->meta_key );
	}

	#[Test]
	public function get_float_required_returns_float_when_value_valid(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( true );
		Functions\expect( 'get_post_meta' )->with( $this->post_id, $this->meta_key, true )->andReturn( '2.718' );
		$result = PostMeta::get_float_required( $this->post_id, $this->meta_key );
		$this->assertSame( 2.718, $result );
	}

	#[Test]
	public function get_float_required_throws_when_meta_missing(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( false );
		$this->expectException( MissingPostMetaException::class );
		PostMeta::get_float_required( $this->post_id, $this->meta_key );
	}

	#[Test]
	public function get_float_required_throws_when_value_invalid(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( true );
		Functions\expect( 'get_post_meta' )->with( $this->post_id, $this->meta_key, true )->andReturn( 'invalid_float' );
		$this->expectException( InvalidPostMetaValueException::class );
		PostMeta::get_float_required( $this->post_id, $this->meta_key );
	}

	// --- STRING ---

	#[Test]
	public function get_string_optional_returns_string_when_value_valid(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( true );
		Functions\expect( 'get_post_meta' )->with( $this->post_id, $this->meta_key, true )->andReturn( 'hello world' );
		$result = PostMeta::get_string_optional( $this->post_id, $this->meta_key );
		$this->assertSame( 'hello world', $result );
	}

	#[Test]
	public function get_string_optional_returns_null_when_meta_missing(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( false );
		$result = PostMeta::get_string_optional( $this->post_id, $this->meta_key );
		$this->assertNull( $result );
	}

	#[Test]
	public function get_string_optional_throws_when_value_invalid(): void {
		// Для строки invalid - смоделируем выброс InvalidArgumentException в кастере
		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( true );
		Functions\expect( 'get_post_meta' )->with( $this->post_id, $this->meta_key, true )->andReturn( [] );
		$this->expectException( InvalidPostMetaValueException::class );
		PostMeta::get_string_optional( $this->post_id, $this->meta_key );
	}

	#[Test]
	public function get_string_required_returns_string_when_value_valid(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( true );
		Functions\expect( 'get_post_meta' )->with( $this->post_id, $this->meta_key, true )->andReturn(
			'required string',
		);
		$result = PostMeta::get_string_required( $this->post_id, $this->meta_key );
		$this->assertSame( 'required string', $result );
	}

	#[Test]
	public function get_string_required_throws_when_meta_missing(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( false );
		$this->expectException( MissingPostMetaException::class );
		PostMeta::get_string_required( $this->post_id, $this->meta_key );
	}

	#[Test]
	public function get_string_required_throws_when_value_invalid(): void {

		Functions\expect( 'metadata_exists' )->with( 'post', $this->post_id, $this->meta_key )->andReturn( true );
		Functions\expect( 'get_post_meta' )->with( $this->post_id, $this->meta_key, true )->andReturn( [] );
		$this->expectException( InvalidPostMetaValueException::class );
		PostMeta::get_string_required( $this->post_id, $this->meta_key );
	}
}
