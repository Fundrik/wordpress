<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Integration\HookBridges\Bridges;

use Fundrik\Core\Support\TypeCaster;
use Fundrik\WordPress\Infrastructure\EventDispatcher\EventDispatcherInterface;
use Fundrik\WordPress\Infrastructure\Helpers\LoggerFormatter;
use Fundrik\WordPress\Infrastructure\Integration\Events\FilterAllowedBlockTypesEvent;
use Fundrik\WordPress\Infrastructure\Integration\HookBridges\HookToEventBridgeInterface;
use Fundrik\WordPress\Infrastructure\Integration\HookBridges\InvalidBridgeArgumentException;
use Fundrik\WordPress\Infrastructure\Integration\WordPressContext\WordPressContextFactory;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use WP_Block_Editor_Context;

/**
 * Bridges the WordPress 'allowed_block_types_all' filter to internal integration events.
 *
 * Validates the filter input before dispatching an internal event.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class AllowedBlockTypesAllFilterBridge implements HookToEventBridgeInterface {

	private const HOOK_NAME = 'allowed_block_types_all';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressContextFactory $context_factory Creates WordPressContext instances on demand.
	 * @param EventDispatcherInterface $dispatcher Dispatches the bridged events.
	 * @param LoggerInterface $logger Logs validation errors and bridging-related warnings.
	 */
	public function __construct(
		private WordPressContextFactory $context_factory,
		private EventDispatcherInterface $dispatcher,
		private LoggerInterface $logger,
	) {}

	/**
	 * Registers the 'allowed_block_types_all' WordPress filter and bridge it to the internal events.
	 *
	 * Validates the hook arguments and dispatches an event if they are valid; otherwise, skips processing.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {

		add_filter(
			self::HOOK_NAME,
			$this->handle( ... ),
			10,
			2,
		);
	}

	// phpcs:disable SlevomatCodingStandard.Files.LineLength.LineTooLong
	/**
	 * Handles the 'allowed_block_types_all' filter logic.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $allowed The list of allowed block type slugs, or a boolean to allow or disallow all.
	 * @param mixed $editor_context The current block editor context.
	 *
	 * @return mixed The modified list of allowed blocks or the original value if validation fails.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	public function handle( mixed $allowed, mixed $editor_context ): mixed {

		try {
			$valid_allowed = $this->validate_allowed( $allowed );
			$valid_context = $this->validate_editor_context( $editor_context );
		} catch ( InvalidBridgeArgumentException $e ) {
			$this->logger->warning(
				$e->getMessage(),
				LoggerFormatter::hook_bridge_context( hook: self::HOOK_NAME, bridge: self::class ),
			);
			return $allowed;
		}

		$event = new FilterAllowedBlockTypesEvent( $valid_allowed, $valid_context, $this->context_factory->create() );
		$this->dispatcher->dispatch( $event );

		return $event->allowed;
	}
	// phpcs:enable

	/**
	 * Validates and normalizes the 'allowed' argument.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $allowed The original value passed by WordPress.
	 *
	 * @return array<string>|bool The validated and normalized allowed block types.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	private function validate_allowed( mixed $allowed ): array|bool {

		if ( is_array( $allowed ) ) {

			try {
				return array_map( TypeCaster::to_string( ... ), $allowed );
			} catch ( InvalidArgumentException ) {
				throw InvalidBridgeArgumentException::create( argument: 'allowed', hook: self::HOOK_NAME );
			}
		}

		if ( $allowed !== true && $allowed !== false ) {
			throw InvalidBridgeArgumentException::create( argument: 'allowed', hook: self::HOOK_NAME );
		}

		return $allowed;
	}

	/**
	 * Validates the 'editor_context' argument.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $editor_context The context passed by WordPress.
	 *
	 * @return WP_Block_Editor_Context The validated context.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	private function validate_editor_context( mixed $editor_context ): WP_Block_Editor_Context {

		if ( ! $editor_context instanceof WP_Block_Editor_Context ) {
			throw InvalidBridgeArgumentException::create( argument: 'editor_context', hook: self::HOOK_NAME );
		}

		return $editor_context;
	}
}
