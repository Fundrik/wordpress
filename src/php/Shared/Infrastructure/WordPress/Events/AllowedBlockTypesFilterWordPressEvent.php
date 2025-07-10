<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Shared\Infrastructure\WordPress\Events;

use Fundrik\WordPress\Shared\Infrastructure\WordPress\WordPressContext;
use WP_Block_Editor_Context;

/**
 * Mirrors the 'allowed_block_types_all' WordPress filter.
 *
 * @since 1.0.0
 */
final class AllowedBlockTypesFilterWordPressEvent {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param bool|array<string> $allowed The list of allowed block type slugs, or a boolean to allow or disallow all.
	 * @param WP_Block_Editor_Context $editor_context The current block editor context.
	 * @param WordPressContext $context The WordPress-specific plugin context.
	 */
	public function __construct(
		public bool|array $allowed,
		public readonly WP_Block_Editor_Context $editor_context,
		public readonly WordPressContext $context,
	) {}
}
