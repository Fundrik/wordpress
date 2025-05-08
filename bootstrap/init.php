<?php
/**
 * Initializes the Fundrik plugin environment.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

use Fundrik\Core\Infrastructure\Platform\Interfaces\PlatformInterface;
use Fundrik\WordPress\Infrastructure\Platform\WordpressPlatform;

$fundrik_container = fundrik();

$fundrik_container->singleton(
	PlatformInterface::class,
	WordpressPlatform::class
);

$fundrik_container->make( PlatformInterface::class )->init();
