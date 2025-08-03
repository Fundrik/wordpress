<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\EventDispatcher;

use Fundrik\WordPress\Infrastructure\EventDispatcher\EventDispatcherInterface;
use Fundrik\WordPress\Infrastructure\EventDispatcher\EventListenerRegistrar;
use Fundrik\WordPress\Infrastructure\WordPress\Events\WordPressAllowedBlockTypesAllFilterEvent;
use Fundrik\WordPress\Infrastructure\WordPress\Events\WordPressInitEvent;
use Fundrik\WordPress\Infrastructure\WordPress\Listeners\FilterAllowedBlocksByPostTypeListener;
use Fundrik\WordPress\Infrastructure\WordPress\Listeners\RegisterBlocksListener;
use Fundrik\WordPress\Infrastructure\WordPress\Listeners\RegisterPostTypesListener;
use Fundrik\WordPress\Tests\MockeryTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( EventListenerRegistrar::class )]
final class EventListenerRegistrarTest extends MockeryTestCase {

	private EventDispatcherInterface&MockInterface $dispatcher;
	private EventListenerRegistrar $registrar;

	protected function setUp(): void {

		parent::setUp();

		$this->dispatcher = Mockery::mock( EventDispatcherInterface::class );
		$this->registrar = new EventListenerRegistrar( $this->dispatcher );
	}

	#[Test]
	public function it_registers_all_event_listeners(): void {

		$this->dispatcher
			->shouldReceive( 'listen' )
			->once()
			->with( WordPressInitEvent::class, RegisterPostTypesListener::class );

		$this->dispatcher
			->shouldReceive( 'listen' )
			->once()
			->with( WordPressInitEvent::class, RegisterBlocksListener::class );

		$this->dispatcher
			->shouldReceive( 'listen' )
			->once()
			->with( WordPressAllowedBlockTypesAllFilterEvent::class, FilterAllowedBlocksByPostTypeListener::class );

		$this->registrar->register_all();
	}
}
