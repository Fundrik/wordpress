<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Validation\Interfaces;

use Symfony\Component\Validator\Exception\ValidationFailedException;

/**
 * Interface to transform validation exceptions into structured or human-readable formats.
 *
 * @since 1.0.0
 */
interface ValidationErrorTransformerInterface {

	/**
	 * Converts a ValidationFailedException into a grouped array of messages per field.
	 *
	 * @since 1.0.0
	 *
	 * @param ValidationFailedException $exception The exception thrown during validation.
	 *
	 * @return array<string, string[]> Associative array of field names to their respective error messages.
	 */
	public function to_array( ValidationFailedException $exception ): array;

	/**
	 * Converts a ValidationFailedException into a human-readable string of all messages.
	 *
	 * @since 1.0.0
	 *
	 * @param ValidationFailedException $exception The exception thrown during validation.
	 *
	 * @return string Combined error messages separated by newlines.
	 */
	public function to_string( ValidationFailedException $exception ): string;
}
