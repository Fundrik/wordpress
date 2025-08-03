<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress\HookMappers\Mappers;

use Fundrik\Core\Support\TypeCaster;
use Fundrik\WordPress\Infrastructure\EventDispatcher\EventDispatcherInterface;
use Fundrik\WordPress\Infrastructure\Helpers\LoggerFormatter;
use Fundrik\WordPress\Infrastructure\WordPress\Events\WordPressAllowedBlockTypesAllFilterEvent;
use Fundrik\WordPress\Infrastructure\WordPress\HookMappers\HookToEventMapperInterface;
use Fundrik\WordPress\Infrastructure\WordPress\HookMappers\Mappers\Exceptions\InvalidMapperArgumentException;
use Fundrik\WordPress\Infrastructure\WordPress\WordPressContext\WordPressContextFactory;
use Fundrik\WordPress\Infrastructure\WordPress\WordPressContext\WordPressContextInterface;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use WP_Block_Editor_Context;

/**
 * Maps the 'allowed_block_types_all' WordPress filter to a Fundrik event.
 *
 * Validates and normalizes the filter input before dispatching an internal event.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class AllowedBlockTypesAllMapper implements HookToEventMapperInterface {

	private const HOOK_NAME = 'allowed_block_types_all';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressContextFactory $context_factory Creates WordPressContext instances on demand.
	 * @param EventDispatcherInterface $dispatcher Dispatches the mapped event.
	 * @param LoggerInterface $logger Logs validation errors and mapping-related warnings.
	 */
	public function __construct(
		private WordPressContextFactory $context_factory,
		private EventDispatcherInterface $dispatcher,
		private LoggerInterface $logger,
	) {}

	/**
	 * Registers the 'allowed_block_types_all' WordPress filter and maps it to the internal event.
	 *
	 * Validates the hook arguments and dispatches an event if they are valid; otherwise, skips processing.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {

		$context = $this->context_factory->make();

		add_filter(
			self::HOOK_NAME,
			fn ( $allowed, $editor_context ) => $this->handle_hook( $allowed, $editor_context, $context ),
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
	 * @param WordPressContextInterface $context The WordPress-specific plugin context.
	 *
	 * @return mixed The modified list of allowed blocks or the original value if validation fails.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	private function handle_hook( mixed $allowed, mixed $editor_context, WordPressContextInterface $context ): mixed {

		try {
			$valid_allowed = $this->validate_allowed( $allowed );
			$valid_context = $this->validate_editor_context( $editor_context );
		} catch ( InvalidMapperArgumentException $e ) {
			$this->logger->warning(
				$e->getMessage(),
				LoggerFormatter::hook_mapper_context( hook: self::HOOK_NAME, mapper: self::class ),
			);
			return $allowed;
		}

		$event = new WordPressAllowedBlockTypesAllFilterEvent( $valid_allowed, $valid_context, $context );
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
				throw InvalidMapperArgumentException::create( argument: 'allowed', hook: self::HOOK_NAME );
			}
		}

		if ( $allowed !== true && $allowed !== false ) {
			throw InvalidMapperArgumentException::create( argument: 'allowed', hook: self::HOOK_NAME );
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
			throw InvalidMapperArgumentException::create( argument: 'editor_context', hook: self::HOOK_NAME );
		}

		return $editor_context;
	}
}
