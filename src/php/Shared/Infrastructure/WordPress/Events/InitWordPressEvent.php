<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Shared\Infrastructure\WordPress\Events;

use Fundrik\WordPress\Shared\Infrastructure\WordPress\WordPressContext;

/**
 * Mirrors the 'init' WordPress action.
 *
 * @since 1.0.0
 */
final readonly class InitWordPressEvent {

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
