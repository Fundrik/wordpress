<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress\Listeners;

use Fundrik\WordPress\Infrastructure\Container\ContainerInterface;
use Fundrik\WordPress\Infrastructure\WordPress\Events\WordPressInitEvent;
use Fundrik\WordPress\Infrastructure\WordPress\PostTypes\Attributes\PostTypeBlockTemplateReader;
use Fundrik\WordPress\Infrastructure\WordPress\PostTypes\Attributes\PostTypeIdReader;
use Fundrik\WordPress\Infrastructure\WordPress\PostTypes\Attributes\PostTypeMetaFieldReader;
use Fundrik\WordPress\Infrastructure\WordPress\PostTypes\Attributes\PostTypeSlugReader;
use Fundrik\WordPress\Infrastructure\WordPress\PostTypes\PostTypeInterface;
use RuntimeException;

/**
 * Registers all post types declared in the plugin configuration during WordPress initialization.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class RegisterPostTypesListener {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param ContainerInterface $container Resolves post type classes via dependency injection.
	 * @param PostTypeIdReader $id_reader Extracts the post type ID from class attributes.
	 * @param PostTypeSlugReader $slug_reader Extracts the post type slug from class attributes.
	 *
	 * // phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong
	 * @param PostTypeBlockTemplateReader $template_reader Extracts the post type block editor template from class attributes.
	 * @param PostTypeMetaFieldReader $meta_reader Extracts declared post meta fields from class constants.
	 */
	public function __construct(
		private ContainerInterface $container,
		private PostTypeIdReader $id_reader,
		private PostTypeSlugReader $slug_reader,
		private PostTypeBlockTemplateReader $template_reader,
		private PostTypeMetaFieldReader $meta_reader,
	) {}

	/**
	 * Handler.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressInitEvent $event Represents the 'init' WordPress action as a Fundrik event.
	 */
	public function handle( WordPressInitEvent $event ): void {

		foreach ( $event->context->get_declared_post_types() as $post_type ) {

			$this->register_post_type( $post_type );
		}
	}

	// phpcs:disable SlevomatCodingStandard.Functions.FunctionLength.FunctionLength
	/**
	 * Resolves and registers the given post type class in WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @param string $class_name The fully-qualified class name of the post type.
	 *
	 * @phpstan-param class-string $class_name
	 *
	 * @todo Decide where post type config (public, supports...) should live.
	 */
	private function register_post_type( string $class_name ): void {

		$post_type = $this->container->get( $class_name );

		if ( ! $post_type instanceof PostTypeInterface ) {
			throw new RuntimeException( "Post type class '$class_name' must implement PostTypeInterface." );
		}

		$id = $this->id_reader->get_id( $class_name );

		/**
		 * Filters the post type labels before registration.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, string> $labels The post type labels.
		 *
		 * @return array<string, string> The filtered labels.
		 */
		$labels = apply_filters( "fundrik_{$id}_post_type_labels", $post_type->get_labels() );

		/**
		 * Filters the post type rewrite slug before registration.
		 *
		 * @since 1.0.0
		 *
		 * @param string $slug The post type slug.
		 *
		 * @return string The filtered slug.
		 */
		$slug = apply_filters( "fundrik_{$id}_post_type_slug", $this->slug_reader->get_slug( $class_name ) );

		register_post_type(
			$id,
			[
				'labels' => $labels,
				'public' => true,
				'menu_icon' => 'dashicons-heart',
				'supports' => [ 'title', 'editor', 'custom-fields' ],
				'has_archive' => true,
				'rewrite' => [ 'slug' => $slug ],
				'show_in_rest' => true,
				'template' => $this->template_reader->get_template( $class_name ),
			],
		);

		$this->register_post_meta_fields( $class_name );
	}
	// phpcs:enable

	/**
	 * Registers all post type meta fields.
	 *
	 * @since 1.0.0
	 *
	 * @param string $class_name The fully-qualified post type class name.
	 *
	 * @phpstan-param class-string $class_name
	 *
	 * @todo Add optional show_in_rest/single to PostTypeMetaField?
	 */
	private function register_post_meta_fields( string $class_name ): void {

		foreach ( $this->meta_reader->get_meta_fields( $class_name ) as $meta_key => $args ) {

			register_post_meta(
				$this->id_reader->get_id( $class_name ),
				$meta_key,
				$args + [
					'show_in_rest' => true,
					'single' => true,
				],
			);
		}
	}
}
