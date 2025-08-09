<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Integration\HookBridges\Bridges;

use Fundrik\WordPress\Infrastructure\EventDispatcher\EventDispatcherInterface;
use Fundrik\WordPress\Infrastructure\Integration\Events\RegisterBlocksEvent;
use Fundrik\WordPress\Infrastructure\Integration\Events\RegisterPostTypesEvent;
use Fundrik\WordPress\Infrastructure\Integration\HookBridges\HookToEventBridgeInterface;
use Fundrik\WordPress\Infrastructure\Integration\WordPressContext\WordPressContextFactory;

/**
 * Bridges the WordPress 'init' action to internal integration events.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class InitActionBridge implements HookToEventBridgeInterface {

	private const HOOK_NAME = 'init';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressContextFactory $context_factory Creates WordPressContext instances on demand.
	 * @param EventDispatcherInterface $dispatcher Dispatches the bridged events.
	 */
	public function __construct(
		private WordPressContextFactory $context_factory,
		private EventDispatcherInterface $dispatcher,
	) {}

	/**
	 * Registers the 'init' WordPress action and bridge it to the internal events.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {

		add_action(
			self::HOOK_NAME,
			$this->handle( ... ),
		);
	}

	/**
	 * Handles the 'init' action logic.
	 *
	 * @since 1.0.0
	 */
	public function handle(): void {

		$this->dispatcher->dispatch(
			new RegisterPostTypesEvent( $this->context_factory->create() ),
		);

		$this->dispatcher->dispatch(
			new RegisterBlocksEvent( $this->context_factory->create() ),
		);
	}
}
