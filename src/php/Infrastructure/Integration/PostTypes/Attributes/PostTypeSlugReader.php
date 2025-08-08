<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Integration\PostTypes\Attributes;

use ReflectionClass;
use RuntimeException;

/**
 * Extracts the post type slug via the #[PostTypeSlug] attribute.
 *
 * Ensures that a post type declares its slug.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class PostTypeSlugReader {

	/**
	 * Returns the slug from a post type class.
	 *
	 * @since 1.0.0
	 *
	 * @param string $class_name The fully qualified class name of the post type.
	 *
	 * @phpstan-param class-string $class_name
	 *
	 * @return string The declared post type slug.
	 */
	public function get_slug( string $class_name ): string {

		$attributes = ( new ReflectionClass( $class_name ) )->getAttributes( PostTypeSlug::class );

		if ( $attributes === [] ) {
			throw new RuntimeException( "Post type class '$class_name' is missing #[PostTypeSlug] attribute." );
		}

		return $attributes[0]->newInstance()->value;
	}
}
