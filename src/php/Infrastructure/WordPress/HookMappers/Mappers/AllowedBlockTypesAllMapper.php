<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress\HookMappers\Mappers;

use Fundrik\Core\Support\TypeCaster;
use Fundrik\WordPress\Infrastructure\EventDispatcher\EventDispatcherInterface;
use Fundrik\WordPress\Infrastructure\WordPress\Events\WordPressAllowedBlockTypesFilterEvent;
use Fundrik\WordPress\Infrastructure\WordPress\HookMappers\HookToEventMapperInterface;
use Fundrik\WordPress\Infrastructure\WordPress\WordPressContext\WordPressContextFactory;
use InvalidArgumentException;
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

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressContextFactory $context_factory Creates WordPressContext instances on demand.
	 * @param EventDispatcherInterface $dispatcher Dispatches the mapped event.
	 */
	public function __construct(
		private WordPressContextFactory $context_factory,
		private EventDispatcherInterface $dispatcher,
	) {}

	/**
	 * Registers the WordPress hook and maps it to the internal event.
	 *
	 * Skips event dispatching if input is invalid or cannot be normalized.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {

		$context = $this->context_factory->make();

		add_filter(
			'allowed_block_types_all',
			function ( $allowed, $editor_context ) use ( $context ) {

				try {
					$valid_allowed = $this->validate_allowed( $allowed );
					$valid_context = $this->validate_editor_context( $editor_context );
				} catch ( InvalidArgumentException $e ) {
					fundrik_log( $e->getMessage() );
					return $allowed;
				}

				$event = new WordPressAllowedBlockTypesFilterEvent( $valid_allowed, $valid_context, $context );
				$this->dispatcher->dispatch( $event );

				return $event->allowed;
			},
			10,
			2,
		);
	}

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
				throw new InvalidArgumentException( "Invalid \$allowed argument in 'allowed_block_types_all' filter." );
			}
		}

		if ( $allowed !== true && $allowed !== false ) {
			throw new InvalidArgumentException( "Invalid \$allowed argument in 'allowed_block_types_all' filter." );
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
			// phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong
			throw new InvalidArgumentException( "Invalid \$editor_context argument in 'allowed_block_types_all' filter." );
		}

		return $editor_context;
	}
}
