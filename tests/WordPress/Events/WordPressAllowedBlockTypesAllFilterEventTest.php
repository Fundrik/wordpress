<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\WordPress\Events;

use Fundrik\WordPress\Infrastructure\WordPress\Events\WordPressAllowedBlockTypesAllFilterEvent;
use Fundrik\WordPress\Infrastructure\WordPress\WordPressContext\WordPressContextInterface;
use Fundrik\WordPress\Tests\MockeryTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use WP_Block_Editor_Context;

#[CoversClass( WordPressAllowedBlockTypesAllFilterEvent::class )]
final class WordPressAllowedBlockTypesAllFilterEventTest extends MockeryTestCase {

	private WP_Block_Editor_Context&MockInterface $editor_context;
	private WordPressContextInterface&MockInterface $context;

	protected function setUp(): void {

		parent::setUp();

		$this->editor_context = Mockery::mock( 'WP_Block_Editor_Context' );
		$this->context = Mockery::mock( WordPressContextInterface::class );
	}

	#[Test]
	public function it_accepts_true_as_allowed(): void {

		$event = new WordPressAllowedBlockTypesAllFilterEvent( true, $this->editor_context, $this->context );

		$this->assertTrue( $event->allowed );
		$this->assertSame( $this->editor_context, $event->editor_context );
		$this->assertSame( $this->context, $event->context );
	}

	#[Test]
	public function it_accepts_false_as_allowed(): void {

		$event = new WordPressAllowedBlockTypesAllFilterEvent( false, $this->editor_context, $this->context );

		$this->assertFalse( $event->allowed );
	}

	#[Test]
	public function it_accepts_array_as_allowed(): void {

		$blocks = [ 'core/paragraph', 'core/image' ];

		$event = new WordPressAllowedBlockTypesAllFilterEvent( $blocks, $this->editor_context, $this->context );

		$this->assertSame( $blocks, $event->allowed );
	}

	#[Test]
	public function it_allows_modification_of_allowed_field(): void {

		$event = new WordPressAllowedBlockTypesAllFilterEvent( true, $this->editor_context, $this->context );

		$event->allowed = [ 'core/quote' ];

		$this->assertSame( [ 'core/quote' ], $event->allowed );
	}
}
