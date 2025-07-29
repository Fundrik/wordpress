<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress\PostTypes\Attributes;

use ReflectionClass;
use RuntimeException;

/**
 * Extracts the post type ID via the #[PostTypeId] attribute.
 *
 * Ensures that a post type declares its ID.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class PostTypeIdReader {

	/**
	 * Returns the id from a post type class.
	 *
	 * @since 1.0.0
	 *
	 * @param string $class_name The fully qualified class name of the post type.
	 *
	 * @phpstan-param class-string $class_name
	 *
	 * @return string The declared post type ID.
	 */
	public function get_id( string $class_name ): string {

		$attributes = ( new ReflectionClass( $class_name ) )->getAttributes( PostTypeId::class );

		if ( $attributes === [] ) {
			throw new RuntimeException( "Post type class '$class_name' is missing #[PostTypeId] attribute." );
		}

		return $attributes[0]->newInstance()->value;
	}
}
