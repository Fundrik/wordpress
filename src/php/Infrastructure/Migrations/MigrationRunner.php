<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Migrations;

use Fundrik\Core\Support\TypeCaster;
use Fundrik\WordPress\Infrastructure\Container\ContainerInterface;
use Fundrik\WordPress\Infrastructure\DatabaseInterface;

/**
 * Applies versioned database migrations in order.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class MigrationRunner implements MigrationRunnerInterface {

	private const OPTION_KEY = 'fundrik_db_version';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param ContainerInterface $container Resolves migration class instances.
	 * @param DatabaseInterface $database Provides access to the WordPress database.
	 * @param MigrationVersionReader $version_reader Extracts version information from migration classes.
	 * @param MigrationRegistry $registry Provides the list of migration classes and target DB version.
	 */
	public function __construct(
		private ContainerInterface $container,
		private DatabaseInterface $database,
		private MigrationVersionReader $version_reader,
		private MigrationRegistry $registry,
	) {}

	/**
	 * Applies all pending migrations with versions newer than the last applied.
	 *
	 * @since 1.0.0
	 */
	public function migrate(): void {

		$current_db_version = $this->get_current_db_version();

		if ( version_compare( MigrationRegistry::DB_VERSION, $current_db_version, '<=' ) ) {
			return;
		}

		$charset_collate = $this->database->get_charset_collate();

		foreach ( $this->get_sorted_classes() as $class ) {

			$version = $this->version_reader->get_version( $class );

			if ( version_compare( $version, $current_db_version, '<=' ) ) {
				continue;
			}

			$migration = $this->container->get( $class );
			$migration->apply( $charset_collate );
			$this->update_current_db_version( $version );
		}
	}

	/**
	 * Returns the list of migration class names sorted by version.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string> The sorted list of migration classes.
	 *
	 * @phpstan-return array<class-string<AbstractMigration>>
	 */
	private function get_sorted_classes(): array {

		$classes = $this->registry->get_migration_classes();

		usort(
			$classes,
			fn ( string $a, string $b ) => version_compare(
				$this->version_reader->get_version( $a ),
				$this->version_reader->get_version( $b ),
			),
		);

		return $classes;
	}

	/**
	 * Returns the current database schema version.
	 *
	 * @since 1.0.0
	 *
	 * @return string The stored DB version.
	 */
	private function get_current_db_version(): string {

		return TypeCaster::to_string( get_option( self::OPTION_KEY, '0000_00_00_00' ) );
	}

	/**
	 * Marks the given migration version as the current database schema version.
	 *
	 * @since 1.0.0
	 *
	 * @param string $version The version to store as current.
	 *
	 * @return bool True on success, false on failure.
	 */
	private function update_current_db_version( string $version ): bool {

		return update_option( self::OPTION_KEY, $version, false );
	}
}
