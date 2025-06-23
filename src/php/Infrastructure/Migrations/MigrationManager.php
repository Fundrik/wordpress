<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Migrations;

use Fundrik\WordPress\Infrastructure\Migrations\Files\Abstracts\AbstractMigration;
use Fundrik\WordPress\Infrastructure\Migrations\Interfaces\MigrationReferenceFactoryInterface;
use Fundrik\WordPress\Support\Path;
use RuntimeException;
use wpdb;

/**
 * Handles and applies database migrations in versioned order.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class MigrationManager {

	private const OPTION_KEY = 'fundrik_migration_version';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param wpdb $db The WordPress database access object.
	 * @param MigrationReferenceFactoryInterface $reference_factory Resolves migration classes and versions
	 *                                                              from migration files.
	 */
	public function __construct(
		private wpdb $db,
		private MigrationReferenceFactoryInterface $reference_factory,
	) {}

	/**
	 * Applies all migration files with versions newer than the last applied one.
	 *
	 * @since 1.0.0
	 */
	public function migrate(): void {

		$current_version = $this->get_current_migration_version();
		$charset_collate = $this->get_charset_collate();

		$references = $this->reference_factory->create_all( Path::MigrationFiles->get_full_path() );

		foreach ( $references as $reference ) {

			if ( version_compare( $reference->version, $current_version, '<=' ) ) {
				continue;
			}

			$migration = fundrik()->get( $reference->class_name );

			if ( ! $migration instanceof AbstractMigration ) {
				// @todo Escaping
				throw new RuntimeException(
					'Expected migration of AbstractMigration, got ' . get_debug_type(
						$migration,
					) . " for class {$reference->class_name}",
				);
			}

			$migration->apply( $charset_collate );

			$this->update_current_migration_version( $reference->version );
		}
	}

	/**
	 * Retrieves the current migration version from the WordPress options table.
	 *
	 * @since 1.0.0
	 *
	 * @return string The latest applied migration version.
	 */
	private function get_current_migration_version(): string {

		return get_option( self::OPTION_KEY, '0000_00_00_00' );
	}

	/**
	 * Updates the stored migration version in the WordPress options table.
	 *
	 * @since 1.0.0
	 *
	 * @param string $version The version to set as current.
	 *
	 * @return bool True on success, false on failure.
	 */
	private function update_current_migration_version( string $version ): bool {

		return update_option( self::OPTION_KEY, $version, false );
	}

	/**
	 * Retrieves the current charset and collation string from the WordPress database connection.
	 *
	 * Used when creating tables to ensure compatibility.
	 *
	 * @since 1.0.0
	 *
	 * @return string Charset and collation string, e.g. "DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
	 */
	private function get_charset_collate(): string {

		return $this->db->get_charset_collate();
	}
}
