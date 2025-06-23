<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

define( 'FUNDRIK_MAIN_FILE', realpath( __DIR__ . '/../fundrik.php' ) );

define( 'FUNDRIK_PATH', realpath( dirname( FUNDRIK_MAIN_FILE ) ) . '/' );

if ( ! class_exists( 'WP_Error' ) ) {

	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound, SlevomatCodingStandard.Files.TypeNameMatchesFileName.NoMatchBetweenTypeNameAndFileName
	final class WP_Error {

		// phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
		private $code;

		// phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
		public function __construct( $code = '' ) {

			$this->code = $code;
		}

		// phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingAnyTypeHint
		public function get_error_code() {

			return $this->code;
		}
	}
}
