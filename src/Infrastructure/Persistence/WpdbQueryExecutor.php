<?php
/**
 * A wrapper around wpdb.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Persistence;

use Fundrik\WordPress\Infrastructure\Persistence\Interfaces\QueryExecutorInterface;
use wpdb;

/**
 * Executes safe SQL queries using WordPress's wpdb instance.
 *
 * @since 1.0.0
 */
final readonly class WpdbQueryExecutor implements QueryExecutorInterface {

	/**
	 * WpdbQueryExecutor constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param wpdb $db The WordPress database access object.
	 */
	public function __construct( private wpdb $db ) {}

	/**
	 * Retrieves a row from the given table by its ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string     $table The name of the table.
	 * @param int|string $id    The value of the primary key (integer or UUID).
	 *
	 * @return array<string,mixed>|null The result as an associative array, or null if not found.
	 */
	public function get_by_id( string $table, int|string $id ): ?array {

		$placeholder = is_int( $id ) ? '%d' : '%s';

		$sql   = "SELECT * FROM %i WHERE id = {$placeholder} LIMIT 1";
		$query = $this->db->prepare( $sql, $table, $id );

		return $this->db->get_row( $query, ARRAY_A );
	}

	/**
	 * Retrieves all rows from the given table.
	 *
	 * @since 1.0.0
	 *
	 * @param string $table The name of the table.
	 *
	 * @return array<int,array<string,mixed>> An array of rows as associative arrays.
	 */
	public function get_all( string $table ): array {

		$sql   = 'SELECT * FROM %i';
		$query = $this->db->prepare( $sql, $table );

		return $this->db->get_results( $query, ARRAY_A );
	}

	/**
	 * Checks if a record exists in the table by its ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string     $table The name of the table.
	 * @param int|string $id    The value of the primary key (integer or UUID).
	 *
	 * @return bool True if the record exists, false otherwise.
	 */
	public function exists( string $table, int|string $id ): bool {

		$placeholder = is_int( $id ) ? '%d' : '%s';

		$sql   = "SELECT id FROM %i WHERE id = {$placeholder} LIMIT 1";
		$query = $this->db->prepare( $sql, $table, $id );

		return (bool) $this->db->get_var( $query );
	}

	/**
	 * Inserts a new row into the given table.
	 *
	 * @since 1.0.0
	 *
	 * @param string $table The name of the table.
	 * @param array  $data  An associative array of column names and their values.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function insert( string $table, array $data ): bool {

		return (bool) $this->db->insert(
			$table,
			$data,
		);
	}

	/**
	 * Updates a row in the given table.
	 *
	 * @since 1.0.0
	 *
	 * @param string     $table The name of the table.
	 * @param array      $data  An associative array of column names and their new values.
	 * @param int|string $id The value of the primary key (integer or UUID).
	 *
	 * @return bool True on success, false on failure.
	 */
	public function update( string $table, array $data, int|string $id ): bool {

		$result = $this->db->update(
			$table,
			$data,
			[ 'id' => $id ]
		);

		return false === $result ? false : true;
	}
}
