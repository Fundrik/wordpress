<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Migrations\Files\Abstracts;

use Fundrik\WordPress\Infrastructure\Persistence\Interfaces\QueryExecutorInterface;

/**
 * Abstract base class for database migrations.
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
	 * @param QueryExecutorInterface $query_executor Query executor for running SQL queries.
	 */
	public function __construct(
		protected QueryExecutorInterface $query_executor,
	) {}

	/**
	 * Applies the migration schema changes.
	 *
	 * @since 1.0.0
	 *
	 * @param string $charset_collate Charset and collation to apply to tables, if needed.
	 */
	abstract public function apply( string $charset_collate ): void;
}
