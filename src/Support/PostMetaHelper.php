<?php
/**
 * PostMetaHelper class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Support;

use Fundrik\Core\Support\TypeCaster;

/**
 * A utility class for working with post metadata in WordPress.
 *
 * @since 1.0.0
 */
final readonly class PostMetaHelper {

	/**
	 * Retrieves a boolean value from post metadata.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $post_id The ID of the post.
	 * @param string $key     The metadata key.
	 *
	 * @return bool The boolean value of the metadata.
	 */
	public static function get_bool( int $post_id, string $key ): bool {

		return TypeCaster::to_bool(
			get_post_meta( $post_id, $key, true )
		);
	}

	/**
	 * Retrieves an integer value from post metadata.
	 *
	 * @param int    $post_id The ID of the post.
	 * @param string $key     The metadata key.
	 *
	 * @return int The integer value of the metadata.
	 */
	public static function get_int( int $post_id, string $key ): int {

		return TypeCaster::to_int(
			get_post_meta( $post_id, $key, true )
		);
	}
}
