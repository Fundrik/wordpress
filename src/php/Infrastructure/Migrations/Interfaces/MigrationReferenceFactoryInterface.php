<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Migrations\Interfaces;

use Fundrik\WordPress\Infrastructure\Migrations\MigrationReference;

/**
 * Interface for factory that creates migration references.
 *
 * @since 1.0.0
 *
 * @internal
 */
interface MigrationReferenceFactoryInterface {

	/**
	 * Creates references for all valid migration files in the given directory.
	 *
	 * @since 1.0.0
	 *
	 * @param string $migrations_directory Absolute path to the directory containing migration files.
	 *
	 * @return array<MigrationReference> Sorted array of migration references.
	 */
	public function create_all( string $migrations_directory ): array;

	/**
	 * Creates a migration reference from a single file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $filepath Absolute path to the migration file.
	 *
	 * @return MigrationReference The resulting reference object.
	 */
	public function create_from_file( string $filepath ): MigrationReference;
}
