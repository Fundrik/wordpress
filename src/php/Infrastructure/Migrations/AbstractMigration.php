<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Migrations;

use Fundrik\WordPress\Infrastructure\DatabaseInterface;

/**
 * Defines a base class for applying database migrations.
 *
 * @since 1.0.0
 *
 * @internal
 */
abstract readonly class AbstractMigration {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param DatabaseInterface $database Executes SQL queries during migration.
	 */
	public function __construct(
		protected DatabaseInterface $database,
	) {}

	/**
	 * Applies the schema changes defined by the migration.
	 *
	 * @since 1.0.0
	 *
	 * @param string $charset_collate The charset and collation string for table creation.
	 */
	abstract public function apply( string $charset_collate ): void;
}
