<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Shared\Infrastructure\WordPress\Listeners;

use Fundrik\WordPress\Shared\Infrastructure\WordPress\Events\InitWordPressEvent;
use Fundrik\WordPress\Shared\Infrastructure\WordPress\Interfaces\PostTypeInterface;

/**
 * Registers all custom post types and their meta fields.
 *
 * @since 1.0.0
 */
final readonly class RegisterPostTypesWordPressListener {

	/**
	 * Handler.
	 *
	 * @since 1.0.0
	 *
	 * @param InitWordPressEvent $event The 'init' WordPress action with the WordPress-specific plugin context.
	 */
	public function handle( InitWordPressEvent $event ): void {

		foreach ( $event->context->plugin->get_post_types() as $post_type ) {

			$this->register_post_type( $post_type );

			$this->register_post_meta_fields( $post_type );
		}
	}

	/**
	 * Registers a single custom post type with WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @param PostTypeInterface $post_type The post type configuration instance.
	 */
	private function register_post_type( PostTypeInterface $post_type ): void {

		register_post_type(
			$post_type->get_type(),
			[
				'labels' => $post_type->get_labels(),
				'public' => true,
				'menu_icon' => 'dashicons-heart',
				'supports' => [ 'title', 'editor', 'custom-fields' ],
				'has_archive' => true,
				'rewrite' => [ 'slug' => $post_type->get_slug() ],
				'show_in_rest' => true,
				'template' => $post_type->get_template_blocks(),
			],
		);
	}

	/**
	 * Registers all meta fields for a given post type.
	 *
	 * @since 1.0.0
	 *
	 * @param PostTypeInterface $post_type The post type configuration instance.
	 */
	private function register_post_meta_fields( PostTypeInterface $post_type ): void {

		foreach ( $post_type->get_meta_fields() as $meta_key => $args ) {

				register_post_meta(
					$post_type->get_type(),
					$meta_key,
					$args + [
						'show_in_rest' => true,
						'single' => true,
					],
				);
		}
	}
}
