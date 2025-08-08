<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\WordPress\Events;

use Fundrik\WordPress\Infrastructure\WordPress\Events\WordPressDeletePost;
use Fundrik\WordPress\Infrastructure\WordPress\WordPressContext\WordPressContextInterface;
use Fundrik\WordPress\Tests\MockeryTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use WP_Post;

#[CoversClass( WordPressDeletePost::class )]
final class WordPressDeletePostTest extends MockeryTestCase {

	private WP_Post&MockInterface $post;
	private WordPressContextInterface&MockInterface $context;

	protected function setUp(): void {

		parent::setUp();

		$this->post = Mockery::mock( WP_Post::class );
		$this->context = Mockery::mock( WordPressContextInterface::class );
	}

	#[Test]
	public function it_accepts_post_id_post_and_context(): void {

		$event = new WordPressDeletePost( 42, $this->post, $this->context );

		$this->assertSame( 42, $event->post_id );
		$this->assertSame( $this->post, $event->post );
		$this->assertSame( $this->context, $event->context );
	}
}
