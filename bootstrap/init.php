<?php
/**
 * Initializes the Fundrik plugin environment.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

use Fundrik\Core\Application\Platform\Interfaces\PlatformInterface;
use Fundrik\WordPress\Infrastructure\Platform\WordPressPlatform;

$fundrik_container = fundrik();

$fundrik_container->singleton(
	PlatformInterface::class,
	WordPressPlatform::class
);

$fundrik_container->make( PlatformInterface::class )->init();
