<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Migrations;

use Fundrik\WordPress\Infrastructure\Migrations\Files\Abstracts\AbstractMigration;
use Fundrik\WordPress\Support\Nspace;
use RuntimeException;

/**
 * Validates migration files by their filepath.
 *
 * Extracts migration version and fully qualified class name,
 * ensures the class exists and extends the AbstractMigration base class.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class MigrationValidator {

	/**
	 * Validates a migration file by its path.
	 *
	 * Parses the filename to extract the migration version and class name,
	 * then resolves the fully qualified class name and validates it.
	 *
	 * @since 1.0.0
	 *
	 * @param string $filepath Full path to the migration PHP file.
	 *
	 * @return MigrationValidationResult The validated migration info.
	 */
	public function validate_by_filepath( string $filepath ): MigrationValidationResult {

		[ $version, $class_name ] = $this->parse_basename( basename( $filepath ) );

		$full_class_name = $this->resolve_full_class_name( $filepath, $class_name );

		return new MigrationValidationResult( $version, $full_class_name );
	}

	/**
	 * Parses the basename of a migration file to extract version and class name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $basename Filename with extension.
	 *
	 * @return array{0:string,1:string} Array containing version and class name.
	 */
	private function parse_basename( string $basename ): array {

		$basename_without_extension = pathinfo( $basename, PATHINFO_FILENAME );

		$parts = explode( '_', $basename_without_extension, 5 );

		if ( count( $parts ) < 5 ) {
			// @todo Escaping
			throw new RuntimeException(
				sprintf(
					"Invalid migration file name format: expected 'YYYY_MM_DD_XX_name', got '%s'",
					$basename_without_extension,
				),
			);
		}

		$version = implode( '_', array_slice( $parts, 0, 4 ) );
		$class_name = str_replace( '_', '', ucwords( array_pop( $parts ), '_' ) );

		return [
			$version,
			$class_name,
		];
	}

	/**
	 * Resolves the fully qualified class name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $filepath Full path to the migration PHP file.
	 * @param string $class_name Class name parsed from filename.
	 *
	 * @return string Fully qualified migration class name.
	 */
	private function resolve_full_class_name( string $filepath, string $class_name ): string {

		require_once $filepath;

		$full_class_name = Nspace::resolve_class_name_by_path(
			dirname( $filepath ) . "/{$class_name}.php",
		);

		if ( $full_class_name === null || ! class_exists( $full_class_name ) ) {
			// @todo Escaping
			throw new RuntimeException( "Migration class '{$class_name}' does not exist in file '{$filepath}'" );
		}

		if ( ! is_subclass_of( $full_class_name, AbstractMigration::class ) ) {
			// @todo Escaping
			throw new RuntimeException( "Migration class '{$class_name}' must extend AbstractMigration" );
		}

		return $full_class_name;
	}
}
