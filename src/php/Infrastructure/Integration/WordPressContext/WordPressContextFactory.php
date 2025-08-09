<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Integration\WordPressContext;

use Fundrik\WordPress\Infrastructure\Container\ContainerInterface;

/**
 * Creates WordPressContext instances on demand.
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class WordPressContextFactory {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param ContainerInterface $container Resolves the WordPressContextInterface from the DI container.
	 */
	public function __construct(
		private ContainerInterface $container,
	) {}

	/**
	 * Creates a fresh WordPressContext instance.
	 *
	 * @since 1.0.0
	 *
	 * @return WordPressContextInterface The current WordPress execution context.
	 */
	public function create(): WordPressContextInterface {

		return $this->container->get( WordPressContextInterface::class );
	}
}
