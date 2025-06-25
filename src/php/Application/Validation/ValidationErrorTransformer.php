<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Validation;

use Fundrik\Core\Support\TypeCaster;
use Fundrik\WordPress\Application\Validation\Interfaces\ValidationErrorTransformerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

/**
 * Transforms validation exceptions into structured formats for easier handling and display.
 *
 * @since 1.0.0
 */
final readonly class ValidationErrorTransformer implements ValidationErrorTransformerInterface {

	/**
	 * Converts a ValidationFailedException into a grouped array of messages per field.
	 *
	 * @since 1.0.0
	 *
	 * @param ValidationFailedException $exception The exception thrown during validation.
	 *
	 * @return array<string, array<string>> Associative array of field names to their respective error messages.
	 */
	public function to_array( ValidationFailedException $exception ): array {

		$violations = $exception->getViolations();
		$errors = [];

		foreach ( $violations as $violation ) {
			$property_path = $violation->getPropertyPath();
			$message = TypeCaster::to_string( $violation->getMessage() );

			$errors[ $property_path ][] = $message;
		}

		return $errors;
	}

	/**
	 * Converts a ValidationFailedException into a human-readable string of all messages.
	 *
	 * @since 1.0.0
	 *
	 * @param ValidationFailedException $exception The exception thrown during validation.
	 *
	 * @return string Combined error messages separated by newlines.
	 */
	public function to_string( ValidationFailedException $exception ): string {

		$errors = $this->to_array( $exception );
		$lines = [];

		foreach ( $errors as $messages ) {

			foreach ( $messages as $message ) {
				$lines[] = $message;
			}
		}

		return implode( "\n", $lines );
	}
}
