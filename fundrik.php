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

use Fundrik\WordPress\Application;
use Fundrik\WordPress\Infrastructure\Container\Container;
use Fundrik\WordPress\Infrastructure\Container\ContainerInterface;
use Fundrik\WordPress\Infrastructure\Container\ServiceBindings;
use Illuminate\Container\Container as LaravelContainer;
use Illuminate\Contracts\Container\Container as LaravelContainerInterface;
use Monolog\Handler\StreamHandler as MonologStreamHandler;
use Monolog\Level as MonologLevel;
use Monolog\Logger as MonologLogger;
use Psr\Log\LoggerInterface;

defined( 'ABSPATH' ) || die;

define( 'FUNDRIK_URL', plugin_dir_url( __FILE__ ) );
define( 'FUNDRIK_PATH', plugin_dir_path( __FILE__ ) );
define( 'FUNDRIK_BASENAME', plugin_basename( __FILE__ ) );
define( 'FUNDRIK_VERSION', '1.0.0' );

require_once FUNDRIK_PATH . 'vendor/autoload.php';

if ( ! function_exists( 'fundrik_init' ) ) {

	/**
	 * Initializes the Fundrik plugin.
	 *
	 * @since 1.0.0
	 */
	function fundrik_init(): void {

		$laravel_container = new LaravelContainer();
		$container = new Container( $laravel_container );

		$container->instance( ContainerInterface::class, $container );
		$container->instance( LaravelContainerInterface::class, $laravel_container );

		$container->singleton( ServiceBindings::class );
		$container->get( ServiceBindings::class )->register_bindings_into_container( $container );

		$container->singleton(
			LoggerInterface::class,
			static function (): LoggerInterface {

				$logger = new MonologLogger( 'fundrik' );

				$log_path = FUNDRIK_PATH . '/fundrik.log';
				$handler = new MonologStreamHandler( $log_path, MonologLevel::Debug );

				$logger->pushHandler( $handler );

				return $logger;
			},
		);

		Application::bootstrap( $container )->run();
	}
}

add_action( 'plugins_loaded', 'fundrik_init' );

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
