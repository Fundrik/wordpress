<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

define( 'FUNDRIK_MAIN_FILE', __DIR__ . '../fundrik.php' );

define( 'FUNDRIK_PATH', dirname( FUNDRIK_MAIN_FILE ) );

if ( ! class_exists( 'WP_Error' ) ) {

	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
	class WP_Error {
		private $code;

		public function __construct( $code = '' ) {
			$this->code = $code;
		}

		public function get_error_code() {
			return $this->code;
		}
	}
}
