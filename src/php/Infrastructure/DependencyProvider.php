<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure;

// phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse
use Closure;
use Fundrik\Core\Infrastructure\Interfaces\DependencyProviderInterface;
use Fundrik\WordPress\Application\Campaigns\Interfaces\WordPressCampaignRepositoryInterface;
use Fundrik\WordPress\Application\Campaigns\Interfaces\WordPressCampaignServiceInterface;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignService;
use Fundrik\WordPress\Application\Validation\Interfaces\ValidationErrorTransformerInterface;
use Fundrik\WordPress\Application\Validation\ValidationErrorTransformer;
use Fundrik\WordPress\Infrastructure\Campaigns\Persistence\WpdbWordPressCampaignRepository;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces\WordPressCampaignSyncListenerInterface;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignPostType;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignSyncListener;
use Fundrik\WordPress\Infrastructure\Migrations\Interfaces\MigrationReferenceFactoryInterface;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationReferenceFactory;
use Fundrik\WordPress\Infrastructure\Persistence\Interfaces\QueryExecutorInterface;
use Fundrik\WordPress\Infrastructure\Persistence\WpdbQueryExecutor;
use Fundrik\WordPress\Infrastructure\Platform\Interfaces\PlatformInterface;
use Fundrik\WordPress\Infrastructure\Platform\WordPressPlatform;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use wpdb;

/**
 * Provides bindings for various dependencies used throughout the application.
 *
 * @since 1.0.0
 */
class DependencyProvider implements DependencyProviderInterface {

	// phpcs:disable SlevomatCodingStandard.Functions.FunctionLength.FunctionLength, SlevomatCodingStandard.Files.LineLength.LineTooLong
	/**
	 * Returns all the bindings for dependencies.
	 *
	 * @param string $category (optional) The category of bindings to return.
	 *                          If provided, only the bindings for the specified category will be returned.
	 *                          If the category doesn't exist, an empty array will be returned.
	 *                          If not provided, all bindings are returned.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, array<string, string|Closure>>|array<string, Closure|string> The array of bindings for dependencies.
	 */
	public function get_bindings( string $category = '' ): array {

		/**
		 * Filters the container bindings.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, array<string, string|Closure>> $bindings The bindings array.
		 */
		$bindings = apply_filters(
			'fundrik_container_bindings',
			[
				'core' => [],
				'wordpress' => [
					// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
					wpdb::class => static fn () => $GLOBALS['wpdb'],

					QueryExecutorInterface::class => WpdbQueryExecutor::class,

					WordPressCampaignRepositoryInterface::class => WpdbWordPressCampaignRepository::class,
					WordPressCampaignServiceInterface::class => WordPressCampaignService::class,

					MigrationReferenceFactoryInterface::class => MigrationReferenceFactory::class,

					ValidationErrorTransformerInterface::class => ValidationErrorTransformer::class,
					ValidatorInterface::class => static fn (): ValidatorInterface => Validation::createValidatorBuilder()
						->enableAttributeMapping()
						->getValidator(),

					PlatformInterface::class => WordPressPlatform::class,
				],
				'post_types' => [
					WordPressCampaignPostType::class => WordPressCampaignPostType::class,
				],
				'listeners' => [
					WordPressCampaignSyncListenerInterface::class => WordPressCampaignSyncListener::class,
				],
			],
		);

		if ( $category !== '' ) {
			return $bindings[ $category ] ?? [];
		}

		return $bindings;
	}
	// phpcs:enable SlevomatCodingStandard.Functions.FunctionLength.FunctionLength
}
