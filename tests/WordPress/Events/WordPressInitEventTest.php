<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\WordPress\Events;

use Fundrik\WordPress\Infrastructure\WordPress\Events\WordPressInitEvent;
use Fundrik\WordPress\Infrastructure\WordPress\WordPressContext\WordPressContextInterface;
use Fundrik\WordPress\Tests\MockeryTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( WordPressInitEvent::class )]
final class WordPressInitEventTest extends MockeryTestCase {

	private WordPressContextInterface&MockInterface $context;

	protected function setUp(): void {

		parent::setUp();

		$this->context = Mockery::mock( WordPressContextInterface::class );
	}

	#[Test]
	public function it_exposes_the_context(): void {

		$event = new WordPressInitEvent( $this->context );

		$this->assertSame( $this->context, $event->context );
	}
}
