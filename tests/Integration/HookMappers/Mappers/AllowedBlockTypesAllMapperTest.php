<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\WordPress\HookMappers\Mappers;

use Brain\Monkey\Functions;
use Fundrik\WordPress\Infrastructure\EventDispatcher\EventDispatcherInterface;
use Fundrik\WordPress\Infrastructure\Integration\Events\WordPressAllowedBlockTypesAllFilterEvent;
use Fundrik\WordPress\Infrastructure\Integration\HookMappers\Mappers\AllowedBlockTypesAllMapper;
use Fundrik\WordPress\Infrastructure\Integration\WordPressContext\WordPressContextFactory;
use Fundrik\WordPress\Infrastructure\Integration\WordPressContext\WordPressContextInterface;
use Fundrik\WordPress\Tests\WordPressTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\LoggerInterface;
use stdClass;
use WP_Block_Editor_Context;

#[CoversClass( AllowedBlockTypesAllMapper::class )]
final class AllowedBlockTypesAllMapperTest extends WordPressTestCase {

	private WordPressContextFactory&MockInterface $context_factory;
	private EventDispatcherInterface&MockInterface $dispatcher;
	private LoggerInterface&MockInterface $logger;
	private WordPressContextInterface&MockInterface $context;
	private AllowedBlockTypesAllMapper $mapper;

	protected function setUp(): void {

		parent::setUp();

		$this->context_factory = Mockery::mock( WordPressContextFactory::class );
		$this->dispatcher = Mockery::mock( EventDispatcherInterface::class );
		$this->logger = Mockery::mock( LoggerInterface::class );
		$this->context = Mockery::mock( WordPressContextInterface::class );

		$this->mapper = new AllowedBlockTypesAllMapper( $this->context_factory, $this->dispatcher, $this->logger );
	}

	#[Test]
	public function it_registers_filter_with_expected_arguments(): void {

		$this->context_factory
			->shouldReceive( 'make' )
			->once()
			->andReturn( $this->context );

		Functions\expect( 'add_filter' )
			->once()
			->withArgs(
				static fn ( string $hook_name, callable $callback, int $priority, int $accepted_args ) => $hook_name === 'allowed_block_types_all'
						&& is_callable( $callback )
						&& $priority === 10
						&& $accepted_args === 2,
			);

		$this->mapper->register();
	}

	#[Test]
	public function it_dispatches_event_when_arguments_are_valid(): void {

		$allowed = [ 'core/paragraph', 'core/heading' ];
		$editor_context = Mockery::mock( WP_Block_Editor_Context::class );

		$this->dispatcher
			->shouldReceive( 'dispatch' )
			->once()
			->with(
				Mockery::on(
					fn ( WordPressAllowedBlockTypesAllFilterEvent $event ) => $event->allowed === $allowed
							&& $event->editor_context === $editor_context
							&& $event->context === $this->context,
				),
			);

		$result = $this->invoke_private_method(
			$this->mapper,
			'handle_hook',
			[ $allowed, $editor_context, $this->context ],
		);

		$this->assertSame( $allowed, $result );
	}

	#[Test]
	public function it_logs_warning_and_returns_original_when_allowed_is_invalid(): void {

		$invalid_allowed = [ 'core/paragraph', new stdClass() ];
		$editor_context = Mockery::mock( WP_Block_Editor_Context::class );

		$this->logger
			->shouldReceive( 'warning' )
			->once()
			->with( Mockery::type( 'string' ), Mockery::type( 'array' ) );

		$result = $this->invoke_private_method(
			$this->mapper,
			'handle_hook',
			[ $invalid_allowed, $editor_context, $this->context ],
		);

		$this->assertSame( $invalid_allowed, $result );
	}

	#[Test]
	public function it_logs_warning_and_returns_original_when_editor_context_is_invalid(): void {

		$allowed = [ 'core/paragraph' ];
		$invalid_editor_context = new stdClass();

		$this->logger
			->shouldReceive( 'warning' )
			->once()
			->with( Mockery::type( 'string' ), Mockery::type( 'array' ) );

		$result = $this->invoke_private_method(
			$this->mapper,
			'handle_hook',
			[ $allowed, $invalid_editor_context, $this->context ],
		);

		$this->assertSame( $allowed, $result );
	}
}
