<?php
/**
 * DependencyProvider class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure;

use Fundrik\Core\Domain\Campaigns\Interfaces\CampaignRepositoryInterface;
use Fundrik\WordPress\Infrastructure\Persistence\Interfaces\QueryExecutorInterface;
use Fundrik\WordPress\Infrastructure\Persistence\WpdbQueryExecutor;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\CampaignSyncListener;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces\CampaignSyncListenerInterface;
use Fundrik\WordPress\Infrastructure\Campaigns\Persistence\WpdbCampaignRepository;
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
				wpdb::class                        => fn() => $GLOBALS['wpdb'],
				QueryExecutorInterface::class      => WpdbQueryExecutor::class,
				CampaignRepositoryInterface::class => WpdbCampaignRepository::class,
				'listeners'                        => [
					CampaignSyncListenerInterface::class => CampaignSyncListener::class,
				],
			]
		);

		if ( $category ) {
			return $bindings[ $category ] ?? [];
		}

		return $bindings;
	}
}
