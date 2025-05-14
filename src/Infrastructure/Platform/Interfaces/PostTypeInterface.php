<?php
/**
 * Interface for providing constants and configuration for the custom post type.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Platform\Interfaces;

interface PostTypeInterface {

	/**
	 * Returns the custom post type identifier for the post type.
	 *
	 * @since 1.0.0
	 *
	 * @return string The custom post type identifier.
	 */
	public static function get_type(): string;

	/**
	 * Returns labels for the custom post type.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, string> An associative array where the keys are label names
	 *                               and the values are the corresponding localized label strings
	 *                               for the custom post type.
	 */
	public static function get_labels(): array;

	/**
	 * Returns the rewrite slug used for the custom post type.
	 *
	 * @since 1.0.0
	 *
	 * @return string The rewrite slug for the custom post type.
	 */
	public static function get_rewrite_slug(): string;
}
