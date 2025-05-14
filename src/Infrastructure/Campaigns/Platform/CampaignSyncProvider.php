<?php
/**
 * CampaignSyncProvider class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Campaigns\Platform;

use Fundrik\Core\Application\Campaigns\CampaignService;
use Fundrik\WordPress\Infrastructure\Platform\Interfaces\SyncProviderInterface;
use Fundrik\WordPress\Infrastructure\Platform\PostSyncListener;

/**
 * Provides synchronization for the campaign post type.
 *
 * @since 1.0.0
 */
class CampaignSyncProvider implements SyncProviderInterface {

	/**
	 * CampaignSyncProvider constructor.
	 *
	 * @param CampaignPostToCampaignDtoMapper $mapper The mapper responsible for converting posts to DTOs.
	 * @param CampaignService                 $service The service that handles campaign operations.
	 */
	public function __construct(
		private CampaignPostToCampaignDtoMapper $mapper,
		private CampaignService $service,
	) {}

	/**
	 * Registers the post synchronization listener for the campaign post type.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {

		$listener = fundrik()->makeWith(
			PostSyncListener::class,
			[
				'post_type' => CampaignPostType::get_type(),
				'mapper'    => $this->mapper,
				'service'   => $this->service,
			]
		);

		$listener->register();
	}
}
