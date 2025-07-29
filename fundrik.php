<?php

// phpcs:disable SlevomatCodingStandard.Commenting.ForbiddenAnnotations.AnnotationForbidden, SlevomatCodingStandard.Commenting.DocCommentSpacing.IncorrectLinesCountBetweenDifferentAnnotationsTypes
/**
 * The Fundrik plugin entry point.
 *
 * @author Denis Yanchevskiy
 * @copyright 2025
 * @license GPLv2+
 *
 * @since 1.0.0
 *
 * Plugin Name: Fundrik
 * Plugin URI: https://fundrik.ru
 * Description: Fundraising solution for WordPress
 * Version: 1.0.0
 * Requires at least: 6.8
 * Requires PHP: 8.2
 * Author: Denis Yanchevskiy
 * Author URI: https://denisco.pro
 * License: GPLv2 or later
 * Text Domain: fundrik
 */
// phpcs:enable


declare(strict_types=1);

use Fundrik\WordPress\App;

defined( 'ABSPATH' ) || die;

define( 'FUNDRIK_URL', plugin_dir_url( __FILE__ ) );
define( 'FUNDRIK_PATH', plugin_dir_path( __FILE__ ) );
define( 'FUNDRIK_BASENAME', plugin_basename( __FILE__ ) );
define( 'FUNDRIK_VERSION', '1.0.0' );

require_once FUNDRIK_PATH . 'vendor/autoload.php';

/**
 * Initializes the Fundrik plugin.
 *
 * @since 1.0.0
 */
function fundrik_init(): void {

	App::bootstrap()->run();
}

add_action( 'plugins_loaded', fundrik_init( ... ) );

/**
 * Temporarily logs a message until a proper logging system is implemented.
 *
 * @since 1.0.0
 *
 * @param string $message The message to log.
 *
 * @todo Replace with proper PSR-3 compatible logger implementation.
 */
function fundrik_log( string $message ): void {

	// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	error_log( $message );
}
