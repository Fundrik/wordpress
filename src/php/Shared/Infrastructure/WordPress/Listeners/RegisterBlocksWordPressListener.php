<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Shared\Infrastructure\WordPress\Listeners;

use Fundrik\WordPress\Shared\Infrastructure\WordPress\Events\InitWordPressEvent;

/**
 * Registers all custom blocks.
 *
 * @since 1.0.0
 */
final readonly class RegisterBlocksWordPressListener {

	/**
	 * Handler.
	 *
	 * @since 1.0.0
	 *
	 * @param InitWordPressEvent $event The 'init' WordPress action with the WordPress-specific plugin context.
	 */
	public function handle( InitWordPressEvent $event ): void {

		wp_register_block_types_from_metadata_collection(
			$event->context->plugin->get_blocks_path(),
			$event->context->plugin->get_blocks_manifest_path(),
		);
	}
}
