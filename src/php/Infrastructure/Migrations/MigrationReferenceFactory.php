<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Migrations;

use Fundrik\WordPress\Infrastructure\Migrations\Interfaces\MigrationReferenceFactoryInterface;

/**
 * Factory for creating migration references from PHP files in the filesystem.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class MigrationReferenceFactory implements MigrationReferenceFactoryInterface {

	/**
	 * Constructor.
	 *
	 * @param MigrationValidator $validator Validates migration file structure and extracts metadata.
	 */
	public function __construct(
		private MigrationValidator $validator,
	) {}

	/**
	 * Creates references for all valid migration files in the given directory.
	 *
	 * @since 1.0.0
	 *
	 * @param string $migrations_directory Absolute path to the directory containing migration files.
	 *
	 * @return MigrationReference[] Sorted array of migration references.
	 */
	public function create_all( string $migrations_directory ): array {

		$files      = glob( $migrations_directory . '/*.php' );
		$references = [];

		foreach ( $files as $filepath ) {
			$references[] = $this->create_from_file( $filepath );
		}

		usort(
			$references,
			static fn ( MigrationReference $a, MigrationReference $b ): int => version_compare( $a->version, $b->version )
		);

		return $references;
	}

	/**
	 * Creates a migration reference from a single file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $filepath Absolute path to the migration file.
	 *
	 * @return MigrationReference The resulting reference object.
	 */
	public function create_from_file( string $filepath ): MigrationReference {

		$validation_result = $this->validator->validate_by_filepath( $filepath );

		return new MigrationReference(
			version: $validation_result->version,
			class_name: $validation_result->class_name,
		);
	}
}
