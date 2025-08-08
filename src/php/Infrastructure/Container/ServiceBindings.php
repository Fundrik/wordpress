<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Container;

use Fundrik\WordPress\Application;
use Fundrik\WordPress\Components\Campaigns\Application\CampaignAssembler;
use Fundrik\WordPress\Components\Campaigns\Application\CampaignDtoFactory;
use Fundrik\WordPress\Components\Campaigns\Application\CampaignService;
use Fundrik\WordPress\Components\Campaigns\Application\Ports\In\CampaignServicePortInterface;
use Fundrik\WordPress\Infrastructure\DatabaseInterface;
use Fundrik\WordPress\Infrastructure\EventDispatcher\EventDispatcher;
use Fundrik\WordPress\Infrastructure\EventDispatcher\EventDispatcherInterface;
use Fundrik\WordPress\Infrastructure\EventDispatcher\EventListenerRegistrar;
use Fundrik\WordPress\Infrastructure\EventDispatcher\EventListenerRegistrarInterface;
use Fundrik\WordPress\Infrastructure\Integration\HookMappers\HookMapperRegistrar;
use Fundrik\WordPress\Infrastructure\Integration\HookMappers\HookMapperRegistrarInterface;
use Fundrik\WordPress\Infrastructure\Integration\HookMappers\HookMapperRegistry;
use Fundrik\WordPress\Infrastructure\Integration\PostTypes\Attributes\PostTypeBlockTemplateReader;
use Fundrik\WordPress\Infrastructure\Integration\PostTypes\Attributes\PostTypeIdReader;
use Fundrik\WordPress\Infrastructure\Integration\PostTypes\Attributes\PostTypeMetaFieldReader;
use Fundrik\WordPress\Infrastructure\Integration\PostTypes\Attributes\PostTypeSlugReader;
use Fundrik\WordPress\Infrastructure\Integration\PostTypes\Attributes\PostTypeSpecificBlockReader;
use Fundrik\WordPress\Infrastructure\Integration\WordPressContext\WordPressContext;
use Fundrik\WordPress\Infrastructure\Integration\WordPressContext\WordPressContextFactory;
use Fundrik\WordPress\Infrastructure\Integration\WordPressContext\WordPressContextInterface;
use Fundrik\WordPress\Infrastructure\Integration\WordPressOptionsStorage;
use Fundrik\WordPress\Infrastructure\Integration\WpdbDatabase;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationRegistry;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationRunner;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationRunnerInterface;
use Fundrik\WordPress\Infrastructure\StorageInterface;
use Illuminate\Contracts\Events\Dispatcher as LaravelEventsDispatcherInterface;
use Illuminate\Events\Dispatcher as LaravelEventsDispatcher;

/**
 * Provides default service bindings for the WordPress container.
 *
 * @since 1.0.0
 *
 * @internal
 */
class ServiceBindings {

	// phpcs:disable SlevomatCodingStandard.Functions.FunctionLength.FunctionLength
	/**
	 * Returns the list of abstract-to-concrete bindings for the container.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string|int, string> The array of [abstract => concrete] bindings.
	 *
	 * @phpstan-return array<class-string|int, class-string>
	 */
	public function get_bindings(): array {

		// phpcs:disable SlevomatCodingStandard.Arrays.DisallowPartiallyKeyed.DisallowedPartiallyKeyed
		return [

			Application::class,

			// Campaigns Application.
			CampaignAssembler::class,
			CampaignDtoFactory::class,
			CampaignServicePortInterface::class => CampaignService::class,

			// Events Dispatcher.
			LaravelEventsDispatcherInterface::class => LaravelEventsDispatcher::class,
			EventDispatcherInterface::class => EventDispatcher::class,
			EventListenerRegistrarInterface::class => EventListenerRegistrar::class,
			HookMapperRegistrarInterface::class => HookMapperRegistrar::class,
			HookMapperRegistry::class,

			// Migrations.
			MigrationRegistry::class,
			MigrationRunnerInterface::class => MigrationRunner::class,

			// Storage.
			DatabaseInterface::class => WpdbDatabase::class,
			StorageInterface::class => WordPressOptionsStorage::class,

			// Post type attribute readers.
			PostTypeBlockTemplateReader::class,
			PostTypeIdReader::class,
			PostTypeMetaFieldReader::class,
			PostTypeSlugReader::class,
			PostTypeSpecificBlockReader::class,

			// Context.
			WordPressContextFactory::class,
			// @todo Register as bind instead of singleton
			WordPressContextInterface::class => WordPressContext::class,
		];
	}
	// phpcs:enable

	/**
	 * Registers all default service bindings into the given container.
	 *
	 * @since 1.0.0
	 *
	 * @param ContainerInterface $container Receives the service bindings for resolution at runtime.
	 */
	public function register_bindings_into_container( ContainerInterface $container ): void {

		foreach ( $this->get_bindings() as $abstract => $concrete ) {

			if ( is_int( $abstract ) ) {
				$abstract = $concrete;
			}

			$container->singleton( $abstract, $concrete );
		}
	}
}
