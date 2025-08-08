<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Integration\HookMappers\Mappers;

use Fundrik\WordPress\Infrastructure\EventDispatcher\EventDispatcherInterface;
use Fundrik\WordPress\Infrastructure\Integration\Events\WordPressInitEvent;
use Fundrik\WordPress\Infrastructure\Integration\HookMappers\HookToEventMapperInterface;
use Fundrik\WordPress\Infrastructure\Integration\WordPressContext\WordPressContextFactory;

/**
 * Maps the 'init' WordPress action to a Fundrik event.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class InitMapper implements HookToEventMapperInterface {

	private const HOOK_NAME = 'init';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressContextFactory $context_factory Creates WordPressContext instances on demand.
	 * @param EventDispatcherInterface $dispatcher Dispatches the WordPressInitEvent.
	 */
	public function __construct(
		private WordPressContextFactory $context_factory,
		private EventDispatcherInterface $dispatcher,
	) {}

	/**
	 * Registers the 'init' WordPress action and maps it to the internal event.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {

		$context = $this->context_factory->make();

		add_action(
			self::HOOK_NAME,
			fn () => $this->dispatcher->dispatch(
				new WordPressInitEvent( $context ),
			),
		);
	}
}
