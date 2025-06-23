<?php
// phpcs:disable SlevomatCodingStandard.Commenting.ForbiddenAnnotations.AnnotationForbidden, SlevomatCodingStandard.Commenting.DocCommentSpacing.IncorrectLinesCountBetweenDifferentAnnotationsTypes
/**
 * Fundrik
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
 * Author: Denis Yanchevskiy
 * Author URI: https://denisco.pro
 * License: GPLv2 or later
 * Text Domain: fundrik
 */

declare(strict_types=1);

use Fundrik\Core\Infrastructure\Interfaces\DependencyProviderInterface;
use Fundrik\WordPress\App;
use Fundrik\WordPress\Infrastructure\Container\Container;
use Fundrik\WordPress\Infrastructure\Container\ContainerRegistry;
use Fundrik\WordPress\Infrastructure\DependencyProvider;

defined( 'ABSPATH' ) || die;

define( 'FUNDRIK_URL', plugin_dir_url( __FILE__ ) );
define( 'FUNDRIK_PATH', plugin_dir_path( __FILE__ ) );
define( 'FUNDRIK_BASENAME', plugin_basename( __FILE__ ) );
define( 'FUNDRIK_VERSION', '1.0.0' );

/**
 * Initializes the Fundrik plugin.
 *
 * @since 1.0.0
 */
function fundrik_init(): void {

	require_once FUNDRIK_PATH . 'vendor/autoload.php';

	ContainerRegistry::set( new Container( new \Illuminate\Container\Container() ) );

	fundrik()->singleton( DependencyProviderInterface::class, DependencyProvider::class );

	fundrik()->get( App::class )->run();
}

add_action( 'plugins_loaded', fundrik_init( ... ) );

/**
 * Handles plugin activation logic.
 *
 * @since 1.0.0
 */
function fundrik_activate(): void {

	require_once FUNDRIK_PATH . 'vendor/autoload.php';

	ContainerRegistry::set( new Container( new \Illuminate\Container\Container() ) );

	fundrik()->singleton( DependencyProviderInterface::class, DependencyProvider::class );

	fundrik()->get( App::class )->activate();
}

/**
 * Register activation hook.
 *
 * WordPress requires this to be at the top level.
 *
 * @see https://developer.wordpress.org/plugins/plugin-basics/activation-deactivation-hooks/
 */
register_activation_hook(
	__FILE__,
	fundrik_activate( ... ),
);
