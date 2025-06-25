<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Persistence;

use Fundrik\Core\Support\TypeCaster;
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
	 * @param string $table The name of the table.
	 * @param int|string $id The value of the primary key (integer or UUID).
	 *
	 * @return array<string, scalar|null>|null The result as an associative array, or null if not found.
	 */
	public function get_by_id( string $table, int|string $id ): ?array {

		$placeholder = is_int( $id ) ? '%d' : '%s';

		$sql = "SELECT * FROM %i WHERE id = {$placeholder} LIMIT 1";
		$query = $this->db->prepare( $sql, $table, $id );

		// phpcs:ignore Generic.Commenting.DocComment.MissingShort, SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
		/** @var array<string, mixed>|null $row */
		$row = $this->db->get_row( $query, ARRAY_A );

		if ( $row === null ) {
			return null;
		}

		$result = [];

		foreach ( $row as $key => $value ) {

			$result[ $key ] = TypeCaster::to_scalar_or_null( $value );
		}

		return $result;
	}

	/**
	 * Retrieves all rows from the given table.
	 *
	 * @since 1.0.0
	 *
	 * @param string $table The name of the table.
	 *
	 * @return list<array<string, scalar|null>> An array of rows as associative arrays.
	 */
	public function get_all( string $table ): array {

		$sql = 'SELECT * FROM %i';
		$query = $this->db->prepare( $sql, $table );

		// phpcs:ignore Generic.Commenting.DocComment.MissingShort, SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
		/** @var list<array<string, mixed>>|null $results */
		$results = $this->db->get_results( $query, ARRAY_A );

		if ( ! is_array( $results ) ) {
			return [];
		}

		$casted_results = [];

		foreach ( $results as $index => $row ) {

			$casted_row = [];

			foreach ( $row as $key => $value ) {
				$casted_row[ $key ] = TypeCaster::to_scalar_or_null( $value );
			}

			$casted_results[ $index ] = $casted_row;
		}

		return array_values( $casted_results );
	}

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
	public function exists( string $table, int|string $id ): bool {

		$placeholder = is_int( $id ) ? '%d' : '%s';

		$sql = "SELECT 1 FROM %i WHERE id = {$placeholder} LIMIT 1";
		$query = $this->db->prepare( $sql, $table, $id );

		return TypeCaster::to_bool( $this->db->get_var( $query ) );
	}

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
	public function exists_by_column( string $table, string $column, int|float|string|bool|null $value ): bool {

		$placeholder = is_int( $value ) ? '%d' : '%s';

		$sql = "SELECT 1 FROM %i WHERE %i = {$placeholder} LIMIT 1";
		$query = $this->db->prepare( $sql, $table, $column, $value );

		return TypeCaster::to_bool( $this->db->get_var( $query ) );
	}

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
	public function insert( string $table, array $data ): bool {

		return TypeCaster::to_bool(
			$this->db->insert(
				$table,
				$data,
			),
		);
	}

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
	public function update( string $table, array $data, int|string $id ): bool {

		$result = $this->db->update(
			$table,
			$data,
			[ 'id' => $id ],
		);

		return $result !== false;
	}

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
	public function delete( string $table, int|string $id ): bool {

		return TypeCaster::to_bool(
			$this->db->delete(
				$table,
				[ 'id' => $id ],
			),
		);
	}

	/**
	 * Executes a raw SQL query.
	 *
	 * @since 1.0.0
	 *
	 * @param string $sql The SQL query string.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function query( string $sql ): bool {

		$result = $this->db->query( $sql );

		return $result !== false;
	}
}
