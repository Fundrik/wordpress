<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Persistence\Interfaces;

/**
 * Interface for executing safe SQL queries using WordPress's wpdb instance.
 *
 * @since 1.0.0
 */
interface QueryExecutorInterface {

	/**
	 * Retrieves a row from the given table by its ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $table The name of the table.
	 * @param int|string $id The value of the primary key (integer or UUID).
	 *
	 * @return array<string, scalar|null>|null The result as an associative array, or null if not found.
	 */
	public function get_by_id( string $table, int|string $id ): ?array;

	/**
	 * Retrieves all rows from the given table.
	 *
	 * @since 1.0.0
	 *
	 * @param string $table The name of the table.
	 *
	 * @return list<array<string,scalar|null>> An array of rows as associative arrays.
	 */
	public function get_all( string $table ): array;

	/**
	 * Checks if a record exists in the table by its ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $table The name of the table.
	 * @param int|string $id The value of the primary key (integer or UUID).
	 *
	 * @return bool True if the record exists, false otherwise.
	 */
	public function exists( string $table, int|string $id ): bool;

	/**
	 * Checks if a record exists in the table by a specific column and value.
	 *
	 * @since 1.0.0
	 *
	 * @param string $table The name of the table.
	 * @param string $column The column name.
	 * @param int|float|string|bool|null $value The value to check.
	 *
	 * @return bool True if the record exists, false otherwise.
	 */
	public function exists_by_column( string $table, string $column, int|float|string|bool|null $value ): bool;

	/**
	 * Inserts a new row into the given table.
	 *
	 * @since 1.0.0
	 *
	 * @param string $table The name of the table.
	 * @param array<string,scalar|null> $data An associative array of column names and their values.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function insert( string $table, array $data ): bool;

	/**
	 * Updates a row in the given table.
	 *
	 * @since 1.0.0
	 *
	 * @param string $table The name of the table.
	 * @param array<string,scalar|null> $data An associative array of column names and their new values.
	 * @param int|string $id The value of the primary key (integer or UUID).
	 *
	 * @return bool True on success, false on failure.
	 */
	public function update( string $table, array $data, int|string $id ): bool;

	/**
	 * Deletes a row from the given table by its ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $table The name of the table.
	 * @param int|string $id The value of the primary key (integer or UUID).
	 *
	 * @return bool True on success, false on failure.
	 */
	public function delete( string $table, int|string $id ): bool;

	/**
	 * Executes a raw SQL query.
	 *
	 * @since 1.0.0
	 *
	 * @param string $sql The SQL query string.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function query( string $sql ): bool;
}
