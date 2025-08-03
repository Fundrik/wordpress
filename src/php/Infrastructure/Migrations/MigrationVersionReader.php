<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Migrations;

use ReflectionClass;
use RuntimeException;

/**
 * Extracts the migration version via the #[MigrationVersion] attribute.
 *
 * Ensures that a migration declares its version.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class MigrationVersionReader {

	/**
	 * Returns the version from a migration class.
	 *
	 * @since 1.0.0
	 *
	 * @param string $class_name The fully qualified class name of the migration.
	 *
	 * @phpstan-param class-string $class_name
	 *
	 * @return string The declared migration version.
	 */
	public function get_version( string $class_name ): string {

		$attributes = ( new ReflectionClass( $class_name ) )->getAttributes( MigrationVersion::class );

		if ( $attributes === [] ) {
			throw new RuntimeException( "Migration class '$class_name' is missing #[MigrationVersion] attribute." );
		}

		$value = $attributes[0]->newInstance()->value;

		if ( trim( $value ) === '' ) {
			throw new RuntimeException( "Migration class '$class_name' has an empty #[MigrationVersion] value." );
		}

		if ( preg_match( '/^\d{4}_\d{2}_\d{2}_\d{2}$/', $value ) !== 1 ) {
			throw new RuntimeException(
				// phpcs:disable SlevomatCodingStandard.Files.LineLength.LineTooLong
				"Migration class '$class_name' has an invalid #[MigrationVersion] format: '$value'. Expected format is 'YYYY_MM_DD_NN'.",
			);
		}

		return $value;
	}
}
