<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Platform;

use Fundrik\WordPress\Infrastructure\Platform\AllowedBlockTypesFilter;
use Fundrik\WordPress\Infrastructure\Platform\Interfaces\PostTypeInterface;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( AllowedBlockTypesFilter::class )]
final class AllowedBlockTypesFilterTest extends FundrikTestCase {

	#[Test]
	public function returns_empty_array_when_allowed_blocks_is_false(): void {

		$filter = new AllowedBlockTypesFilter();

		$result = $filter->filter(
			false,
			'post',
			[]
		);

		$this->assertSame( [], $result );
	}

	#[Test]
	public function returns_all_blocks_when_allowed_blocks_is_true_and_no_specific_blocks(): void {

		$filter = new AllowedBlockTypesFilter(
			[ 'core/paragraph', 'core/image', 'core/quote' ]
		);

		$result = $filter->filter(
			true,
			'post',
			[]
		);

		$this->assertContains( 'core/paragraph', $result );
		$this->assertContains( 'core/image', $result );
	}

	#[Test]
	public function filters_out_blocks_not_allowed_for_current_post_type(): void {

		$filter = new AllowedBlockTypesFilter();

		$allowed_blocks = [ 'core/paragraph', 'core/image' ];

		$post_type_custom = Mockery::mock( PostTypeInterface::class );
		$post_type_custom->shouldReceive( 'get_type' )->once()->andReturn( 'custom_post' );
		$post_type_custom->shouldReceive( 'get_specific_blocks' )->once()->andReturn( [ 'core/paragraph' ] );

		$post_type_other = Mockery::mock( PostTypeInterface::class );
		$post_type_other->shouldReceive( 'get_type' )->once()->andReturn( 'other_post' );
		$post_type_other->shouldReceive( 'get_specific_blocks' )->once()->andReturn( [ 'core/image' ] );

		$result = $filter->filter(
			$allowed_blocks,
			'custom_post',
			[ $post_type_custom, $post_type_other ]
		);

		$this->assertSame( [ 'core/paragraph' ], $result );
	}

	#[Test]
	public function allows_block_if_it_is_not_restricted_to_specific_post_types(): void {

		$filter = new AllowedBlockTypesFilter();

		$post_type_mock = Mockery::mock( PostTypeInterface::class );
		$post_type_mock->shouldReceive( 'get_type' )->once()->andReturn( 'page' );
		$post_type_mock->shouldReceive( 'get_specific_blocks' )->once()->andReturn( [ 'core/paragraph' ] );

		$allowed_blocks = [ 'core/paragraph', 'core/quote' ];

		$result = $filter->filter(
			$allowed_blocks,
			'post', // current post type not allowed for core/paragraph.
			[ $post_type_mock ]
		);

		// core/paragraph is restricted to 'page' only, core/quote allowed by default (not restricted).
		$this->assertSame( [ 'core/quote' ], $result );
	}
}
