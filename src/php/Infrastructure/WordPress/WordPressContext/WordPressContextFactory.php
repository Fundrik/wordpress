<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress\WordPressContext;

/**
 * Creates WordPressContext instances on demand.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class WordPressContextFactory {

	/**
	 * Builds a fresh WordPressContext instance.
	 *
	 * @since 1.0.0
	 *
	 * @return WordPressContext The current WordPress execution context.
	 */
	public function make(): WordPressContext {

		return new WordPressContext();
	}
}
