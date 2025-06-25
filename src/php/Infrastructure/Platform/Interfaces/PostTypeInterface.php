<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Platform\Interfaces;

/**
 * Interface for providing constants and configuration for the custom post type.
 *
 * @since 1.0.0
 */
interface PostTypeInterface {

	/**
	 * Returns the custom post type identifier for the post type.
	 *
	 * @since 1.0.0
	 *
	 * @return string The custom post type identifier.
	 */
	public function get_type(): string;

	/**
	 * Returns labels for the custom post type.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, string> An associative array where the keys are label names
	 *                               and the values are the corresponding localized label strings
	 *                               for the custom post type.
	 */
	public function get_labels(): array;

	/**
	 * Returns the slug used for the custom post type.
	 *
	 * @since 1.0.0
	 *
	 * @return string The slug for the custom post type.
	 */
	public function get_slug(): string;

	/**
	 * Returns an array of meta fields associated with the custom post type.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, array{type: string, default?: mixed}> An associative array where keys are meta field names,
	 *                                                             and values are the corresponding data types.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	public function get_meta_fields(): array;

	/**
	 * Returns the block-based template used to render the custom post type in the editor.
	 *
	 * @since 1.0.0
	 *
	 * @return array<int, array<string>> A nested array of block names representing
	 *                                   the template layout for the custom post type.
	 */
	public function get_template_blocks(): array;

	/**
	 * Returns a list of block names that are specifically allowed for the custom post type.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string> List of block names allowed for the custom post type.
	 */
	public function get_specific_blocks(): array;
}
