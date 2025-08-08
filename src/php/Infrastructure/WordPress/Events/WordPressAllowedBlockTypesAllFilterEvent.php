<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress\Events;

use Fundrik\WordPress\Infrastructure\WordPress\WordPressContext\WordPressContextInterface;
use WP_Block_Editor_Context;

/**
 * Represents the 'allowed_block_types_all' WordPress filter as a Fundrik event.
 *
 * @since 1.0.0
 *
 * @internal
 */
final class WordPressAllowedBlockTypesAllFilterEvent {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param bool|array<string> $allowed The list of allowed block type slugs, or a boolean to allow or disallow all.
	 * @param WP_Block_Editor_Context $editor_context The current block editor context.
	 * @param WordPressContextInterface $context The WordPress-specific plugin context.
	 */
	public function __construct(
		public bool|array $allowed,
		public readonly WP_Block_Editor_Context $editor_context,
		public readonly WordPressContextInterface $context,
	) {}
}
