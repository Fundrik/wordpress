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
use Fundrik\WordPress\Shared\Infrastructure\Container\Container;
use Fundrik\WordPress\Shared\Infrastructure\Container\ContainerRegistry;

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

	ContainerRegistry::set( new Container( new \Illuminate\Container\Container() ) );

	/**
	 * Fires before the Fundrik App runs.
	 *
	 * This hook is triggered after the container is initialized,
	 * but before the application is bootstrapped.
	 *
	 * @since 1.0.0
	 */
	do_action( 'fundrik_before_app_run' );

	fundrik()->get( App::class )->run();
}

add_action( 'plugins_loaded', fundrik_init( ... ) );

/**
 * Handles the plugin activation.
 *
 * @since 1.0.0
 */
function fundrik_activate(): void {

	ContainerRegistry::set( new Container( new \Illuminate\Container\Container() ) );

	/**
	 * Fires before the Fundrik App handles activation.
	 *
	 * This hook is triggered after the container and dispatcher
	 * are initialized, but before the app runs activation logic.
	 *
	 * @since 1.0.0
	 */
	do_action( 'fundrik_before_app_activate' );

	fundrik()->get( App::class )->activate();
}

/**
 * Register the activation hook.
 *
 * WordPress requires this to be a top-level call.
 *
 * @see https://developer.wordpress.org/plugins/plugin-basics/activation-deactivation-hooks/
 */
register_activation_hook( __FILE__, fundrik_activate( ... ) );
