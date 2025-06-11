<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Support;

use Brain\Monkey\Functions;
use Fundrik\WordPress\Support\PostMeta;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( PostMeta::class )]
class PostMetaTest extends FundrikTestCase {

	#[Test]
	public function get_bool_returns_true_when_value_is_true(): void {

		$post_id  = 123;
		$meta_key = 'some_key';

		Functions\expect( 'get_post_meta' )
			->with(
				$this->identicalTo( $post_id ),
				$this->identicalTo( $meta_key ),
				true
			)
			->andReturn( 'true' );

		$result = PostMeta::get_bool( $post_id, $meta_key );

		$this->assertTrue( $result );
	}

	#[Test]
	public function get_bool_returns_false_when_value_is_false(): void {

		$post_id  = 123;
		$meta_key = 'some_key';

		Functions\expect( 'get_post_meta' )
			->with(
				$this->identicalTo( $post_id ),
				$this->identicalTo( $meta_key ),
				true
			)
			->andReturn( 'off' );

		$result = PostMeta::get_bool( $post_id, $meta_key );

		$this->assertFalse( $result );
	}

	#[Test]
	public function get_bool_returns_false_when_value_is_empty(): void {

		$post_id  = 123;
		$meta_key = 'some_key';

		Functions\expect( 'get_post_meta' )
			->with(
				$this->identicalTo( $post_id ),
				$this->identicalTo( $meta_key ),
				true
			)
			->andReturn( '' );

		$result = PostMeta::get_bool( $post_id, $meta_key );

		$this->assertFalse( $result );
	}

	#[Test]
	public function get_int_returns_integer_value(): void {

		$post_id  = 123;
		$meta_key = 'some_key';

		Functions\expect( 'get_post_meta' )
			->with(
				$this->identicalTo( $post_id ),
				$this->identicalTo( $meta_key ),
				true
			)
			->andReturn( '42' );

		$result = PostMeta::get_int( $post_id, $meta_key );

		$this->assertSame( 42, $result );
	}

	#[Test]
	public function get_int_returns_zero_for_non_integer_value(): void {

		$post_id  = 123;
		$meta_key = 'some_key';

		Functions\expect( 'get_post_meta' )
			->with(
				$this->identicalTo( $post_id ),
				$this->identicalTo( $meta_key ),
				true
			)
			->andReturn( 'some string' );

		$result = PostMeta::get_int( $post_id, $meta_key );

		$this->assertSame( 0, $result );
	}

	#[Test]
	public function get_int_returns_zero_when_value_is_empty(): void {

		$post_id  = 123;
		$meta_key = 'some_key';

		Functions\expect( 'get_post_meta' )
			->with(
				$this->identicalTo( $post_id ),
				$this->identicalTo( $meta_key ),
				true
			)
			->andReturn( '' );

		$result = PostMeta::get_int( $post_id, $meta_key );

		$this->assertSame( 0, $result );
	}
}
