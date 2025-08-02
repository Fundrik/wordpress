<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress\HookMappers;

use Fundrik\WordPress\Infrastructure\Container\ContainerInterface;

/**
 * Registers all WordPress hook-to-event mappers.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class HookMapperRegistrar implements HookMapperRegistrarInterface {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param HookMapperRegistry $registry Provides the list of mapper classes.
	 * @param ContainerInterface $container Resolves mapper class instances.
	 */
	public function __construct(
		private HookMapperRegistry $registry,
		private ContainerInterface $container,
	) {}

	/**
	 * Registers all declared hook-to-event mappers.
	 *
	 * @since 1.0.0
	 */
	public function register_all(): void {

		foreach ( $this->registry->get_mapper_classes() as $class ) {
			$this->container->get( $class )->register();
		}
	}
}
