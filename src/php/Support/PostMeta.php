<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Support;

use Fundrik\Core\Support\TypeCaster;
use Fundrik\WordPress\Support\Exceptions\InvalidPostMetaValueException;
use Fundrik\WordPress\Support\Exceptions\MissingPostMetaException;
use InvalidArgumentException;

/**
 * A utility class for working with post metadata in WordPress.
 *
 * @since 1.0.0
 */
final readonly class PostMeta {

	/**
	 * Retrieves a boolean value of a post meta field for the given post ID.
	 *
	 * Returns null if the meta field does not exist.
	 * Throws an exception if the meta value exists but cannot be cast to a valid boolean.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The ID of the post.
	 * @param string $key The meta field key.
	 *
	 * @return bool|null The boolean value of the meta field, or null if not exists.
	 */
	public static function get_bool_optional( int $post_id, string $key ): ?bool {

		return self::cast_meta( $post_id, $key, TypeCaster::to_bool( ... ), 'bool', false );
	}

	/**
	 * Retrieves a integer value of a post meta field for the given post ID.
	 *
	 * Returns null if the meta field does not exist.
	 * Throws an exception if the meta value exists but cannot be cast to a valid integer.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The ID of the post.
	 * @param string $key The meta field key.
	 *
	 * @return int|null The integer value of the meta field, or null if not exists.
	 */
	public static function get_int_optional( int $post_id, string $key ): ?int {

		return self::cast_meta( $post_id, $key, TypeCaster::to_int( ... ), 'int', false );
	}

	/**
	 * Retrieves a float value of a post meta field for the given post ID.
	 *
	 * Returns null if the meta field does not exist.
	 * Throws an exception if the meta value exists but cannot be cast to a valid float.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The ID of the post.
	 * @param string $key The meta field key.
	 *
	 * @return float|null The float value of the meta field, or null if not exists.
	 */
	public static function get_float_optional( int $post_id, string $key ): ?float {

		return self::cast_meta( $post_id, $key, TypeCaster::to_float( ... ), 'float', false );
	}

	/**
	 * Retrieves a string value of a post meta field for the given post ID.
	 *
	 * Returns null if the meta field does not exist.
	 * Throws an exception if the meta value exists but cannot be cast to a valid string.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The ID of the post.
	 * @param string $key The meta field key.
	 *
	 * @return string|null The string value of the meta field, or null if not exists.
	 */
	public static function get_string_optional( int $post_id, string $key ): ?string {

		return self::cast_meta( $post_id, $key, TypeCaster::to_string( ... ), 'string', false );
	}

	/**
	 * Retrieves a boolean value of a post meta field for the given post ID.
	 *
	 * Throws an exception if the specified meta field does not exist for the post
	 * or if the meta value cannot be cast to a valid boolean.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The ID of the post.
	 * @param string $key The meta field key.
	 *
	 * @return bool The boolean value of the meta field.
	 */
	public static function get_bool_required( int $post_id, string $key ): bool {

		return self::cast_meta( $post_id, $key, TypeCaster::to_bool( ... ), 'bool' );
	}

	/**
	 * Retrieves a integer value of a post meta field for the given post ID.
	 *
	 * Throws an exception if the specified meta field does not exist for the post
	 * or if the meta value cannot be cast to a valid integer.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The ID of the post.
	 * @param string $key The meta field key.
	 *
	 * @return int The integer value of the meta field.
	 */
	public static function get_int_required( int $post_id, string $key ): int {

		return self::cast_meta( $post_id, $key, TypeCaster::to_int( ... ), 'int' );
	}

	/**
	 * Retrieves a float value of a post meta field for the given post ID.
	 *
	 * Throws an exception if the specified meta field does not exist for the post
	 * or if the meta value cannot be cast to a valid float.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The ID of the post.
	 * @param string $key The meta field key.
	 *
	 * @return float The float value of the meta field.
	 */
	public static function get_float_required( int $post_id, string $key ): float {

		return self::cast_meta( $post_id, $key, TypeCaster::to_float( ... ), 'float' );
	}

	/**
	 * Retrieves a string value of a post meta field for the given post ID.
	 *
	 * Throws an exception if the specified meta field does not exist for the post
	 * or if the meta value cannot be cast to a valid string.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The ID of the post.
	 * @param string $key The meta field key.
	 *
	 * @return string The string value of the meta field.
	 */
	public static function get_string_required( int $post_id, string $key ): string {

		return self::cast_meta( $post_id, $key, TypeCaster::to_string( ... ), 'string' );
	}

	/**
	 * Internal meta casting wrapper with exception normalization.
	 *
	 * - If the meta key is missing:
	 *   - Throws exception if $required is true.
	 *   - Returns null if $required is false.
	 *
	 * - If the meta key exists but the value is of invalid type:
	 *   - Always throws exception with detailed context.
	 *
	 * This guarantees clear distinction between missing meta key and invalid meta value.
	 *
	 * @since 1.0.0
	 *
	 * @template T
	 *
	 * @param int $post_id The ID of the post.
	 * @param string $key The meta key.
	 * @param callable $caster A function that attempts to cast the value.
	 * @param string $type_description Human-readable description of the expected type.
	 * @param bool $required Whether the meta key must exist. Default: true.
	 *
	 * @phpstan-param callable(mixed): T $caster
	 *
	 * @return mixed The casted meta value, or null if not required and key is missing.
	 *
	 * @phpstan-return ($required is true ? T : T|null)
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	private static function cast_meta(
		int $post_id,
		string $key,
		callable $caster,
		string $type_description,
		bool $required = true,
	): mixed {

		if ( ! metadata_exists( 'post', $post_id, $key ) ) {

			if ( $required ) {
				throw new MissingPostMetaException( "Missing required meta key '{$key}' for post {$post_id}" );
			}

			return null;
		}

		$value = get_post_meta( $post_id, $key, true );

		try {
			return $caster( $value );
		} catch ( InvalidArgumentException $e ) {
			throw new InvalidPostMetaValueException(
				// phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong
				"Invalid value for meta key '{$key}' on post {$post_id} (expected {$type_description}): " . $e->getMessage(),
				previous: $e,
			);
		}
	}
}
