<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress\Listeners;

use Fundrik\WordPress\Infrastructure\WordPress\Events\WordPressInitEvent;

/**
 * Registers all custom blocks.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class RegisterBlocksListener {

	/**
	 * Handler.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressInitEvent $event Represents the 'init' WordPress action as a Fundrik event.
	 */
	public function handle( WordPressInitEvent $event ): void {

		wp_register_block_types_from_metadata_collection(
			$event->context->plugin->get_blocks_path(),
			$event->context->plugin->get_blocks_manifest_path(),
		);
	}
}
