<?php
/**
 * DependencyProvider class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure;

use Fundrik\WordPress\Application\Campaigns\Interfaces\WordPressCampaignRepositoryInterface;
use Fundrik\WordPress\Application\Campaigns\Interfaces\WordPressCampaignServiceInterface;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignService;
use Fundrik\WordPress\Infrastructure\Persistence\Interfaces\QueryExecutorInterface;
use Fundrik\WordPress\Infrastructure\Persistence\WpdbQueryExecutor;
use Fundrik\WordPress\Infrastructure\Campaigns\Persistence\WpdbWordPressCampaignRepository;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces\WordPressCampaignPostMapperInterface;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces\WordPressCampaignSyncListenerInterface;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignPostMapper;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignPostType;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignSyncListener;
use wpdb;

/**
 * Provides bindings for various dependencies used throughout the application.
 *
 * @since 1.0.0
 */
class DependencyProvider {

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
	 * @return array<string, string|array<string, string>> The array of bindings for dependencies.
	 */
	public function get_bindings( string $category = '' ): array {

		/**
		 * Filters the container bindings.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, string|array<string, string>> $bindings The bindings array.
		 */
		$bindings = apply_filters(
			'fundrik_container_bindings',
			[
				'core'       => [],
				'wordpress'  => [
					wpdb::class                   => fn() => $GLOBALS['wpdb'],

					QueryExecutorInterface::class => WpdbQueryExecutor::class,

					WordPressCampaignRepositoryInterface::class => WpdbWordPressCampaignRepository::class,
					WordPressCampaignServiceInterface::class => WordPressCampaignService::class,
					WordPressCampaignPostMapperInterface::class => WordPressCampaignPostMapper::class,
				],
				'post_types' => [
					WordPressCampaignPostType::class,
				],
				'listeners'  => [
					WordPressCampaignSyncListenerInterface::class => WordPressCampaignSyncListener::class,
				],
			]
		);

		if ( $category ) {
			return $bindings[ $category ] ?? [];
		}

		return $bindings;
	}
}
