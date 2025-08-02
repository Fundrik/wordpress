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
use Fundrik\WordPress\Infrastructure\Migrations\MigrationRegistry;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationRunner;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationRunnerInterface;
use Fundrik\WordPress\Infrastructure\WordPress\HookMappers\HookMapperRegistrar;
use Fundrik\WordPress\Infrastructure\WordPress\HookMappers\HookMapperRegistrarInterface;
use Fundrik\WordPress\Infrastructure\WordPress\HookMappers\HookMapperRegistry;
use Fundrik\WordPress\Infrastructure\WordPress\PostTypes\Attributes\PostTypeBlockTemplateReader;
use Fundrik\WordPress\Infrastructure\WordPress\PostTypes\Attributes\PostTypeIdReader;
use Fundrik\WordPress\Infrastructure\WordPress\PostTypes\Attributes\PostTypeMetaFieldReader;
use Fundrik\WordPress\Infrastructure\WordPress\PostTypes\Attributes\PostTypeSlugReader;
use Fundrik\WordPress\Infrastructure\WordPress\PostTypes\Attributes\PostTypeSpecificBlockReader;
use Fundrik\WordPress\Infrastructure\WordPress\WordPressContext\WordPressContextFactory;
use Fundrik\WordPress\Infrastructure\WordPress\WpdbDatabase;
use Illuminate\Contracts\Events\Dispatcher as IlluminateEventsDispatcherInterface;
use Illuminate\Events\Dispatcher as IlluminateEventsDispatcher;

/**
 * Provides default service bindings for the WordPress container.
 *
 * @since 1.0.0
 *
 * @internal
 */
class ServiceBindings {

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
			IlluminateEventsDispatcherInterface::class => IlluminateEventsDispatcher::class,
			EventDispatcherInterface::class => EventDispatcher::class,
			EventListenerRegistrarInterface::class => EventListenerRegistrar::class,
			HookMapperRegistrarInterface::class => HookMapperRegistrar::class,
			HookMapperRegistry::class,

			// Migrations.
			MigrationRegistry::class,
			MigrationRunnerInterface::class => MigrationRunner::class,

			// Database.
			DatabaseInterface::class => WpdbDatabase::class,

			// Post type attribute readers.
			PostTypeBlockTemplateReader::class,
			PostTypeIdReader::class,
			PostTypeMetaFieldReader::class,
			PostTypeSlugReader::class,
			PostTypeSpecificBlockReader::class,

			// Context.
			WordPressContextFactory::class,
		];
	}
}
