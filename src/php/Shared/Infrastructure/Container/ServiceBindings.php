<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Shared\Infrastructure\Container;

// phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse
use Closure;
use Fundrik\WordPress\Campaigns\Application\Interfaces\WordPressCampaignServiceInterface;
use Fundrik\WordPress\Campaigns\Application\WordPressCampaignService;
use Fundrik\WordPress\Campaigns\Infrastructure\Interfaces\WordPressCampaignRepositoryInterface;
use Fundrik\WordPress\Campaigns\Infrastructure\WpdbWordPressCampaignRepository;
use Fundrik\WordPress\Shared\Infrastructure\Interfaces\QueryExecutorInterface;
use Fundrik\WordPress\Shared\Infrastructure\Migrations\Interfaces\MigrationReferenceFactoryInterface;
use Fundrik\WordPress\Shared\Infrastructure\Migrations\MigrationReferenceFactory;
use Fundrik\WordPress\Shared\Infrastructure\WordPress\Interfaces\WordPressPlatformInterface;
use Fundrik\WordPress\Shared\Infrastructure\WordPress\WordPressPlatform;
use Fundrik\WordPress\Shared\Infrastructure\WordPress\WpdbQueryExecutor;
use Illuminate\Contracts\Events\Dispatcher as DispatcherInterface;
use Illuminate\Events\Dispatcher;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use wpdb;

/**
 * The service binding provider for the Fundrik container.
 *
 * This class supplies a list of service bindings that map abstract types
 * (interfaces or class names) to their concrete implementations or factories.
 *
 * These bindings are registered into the container at application bootstrap.
 *
 * @since 1.0.0
 */
class ServiceBindings {

	/**
	 * Returns the list of service bindings for the container.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, string|Closure> The array of [abstract => concrete] bindings.
	 */
	public function get_bindings(): array {

		/**
		 * Filters the container bindings before registration.
		 *
		 * Allows themes or plugins to modify or extend the default set of
		 * bindings before they are registered in the container.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, string|Closure> $bindings The array of container bindings.
		 */
		return apply_filters(
			'fundrik_container_bindings',
			[
				DispatcherInterface::class => Dispatcher::class,

				ValidatorInterface::class => static fn (): ValidatorInterface => Validation::createValidatorBuilder()
					->enableAttributeMapping()
					->getValidator(),

				// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
				wpdb::class => static fn () => $GLOBALS['wpdb'],

				QueryExecutorInterface::class => WpdbQueryExecutor::class,

				WordPressCampaignRepositoryInterface::class => WpdbWordPressCampaignRepository::class,
				WordPressCampaignServiceInterface::class => WordPressCampaignService::class,

				MigrationReferenceFactoryInterface::class => MigrationReferenceFactory::class,

				WordPressPlatformInterface::class => WordPressPlatform::class,
			],
		);
	}
}
