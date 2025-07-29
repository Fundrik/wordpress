<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress\Events;

use Fundrik\WordPress\Infrastructure\WordPress\WordPressContext;

/**
 * Represents the 'init' WordPress action as a Fundrik event.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class WordPressInitEvent {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressContext $context The WordPress-specific plugin context.
	 */
	public function __construct(
		public WordPressContext $context,
	) {}
}
