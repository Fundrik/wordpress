<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Campaigns\Platform;

use Fundrik\Core\Application\Campaigns\CampaignService;
use Fundrik\Core\Domain\Campaigns\Interfaces\CampaignRepositoryInterface;
use Fundrik\Core\Infrastructure\Internal\ContainerManager;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\CampaignPostToCampaignDtoMapper;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\CampaignPostType;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\CampaignSyncProvider;
use Fundrik\WordPress\Infrastructure\Persistence\Interfaces\QueryExecutorInterface;
use Fundrik\WordPress\Infrastructure\Platform\PostSyncListener;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use wpdb;

#[CoversClass( CampaignSyncProvider::class )]
#[UsesClass( CampaignPostToCampaignDtoMapper::class )]
#[UsesClass( CampaignPostType::class )]
#[UsesClass( PostSyncListener::class )]
class CampaignSyncProviderTest extends FundrikTestCase {

	#[Test]
	public function register_creates_and_registers_listener(): void {

		$fundrik_container = ContainerManager::get_fresh();

		$fundrik_container->bind( wpdb::class, fn() => Mockery::mock( 'wpdb' ) );
		$fundrik_container->bind( QueryExecutorInterface::class, fn() => Mockery::mock( QueryExecutorInterface::class ) );
		$fundrik_container->bind( CampaignRepositoryInterface::class, fn() => Mockery::mock( CampaignRepositoryInterface::class ) );

		$fundrik_container->singleton(
			PostSyncListener::class,
			fn() => new PostSyncListener(
				CampaignPostType::get_type(),
				$fundrik_container->make( CampaignPostToCampaignDtoMapper::class ),
				$fundrik_container->make( CampaignService::class )
			)
		);

		$provider = new CampaignSyncProvider(
			$fundrik_container->make( CampaignPostToCampaignDtoMapper::class ),
			$fundrik_container->make( CampaignService::class ),
		);

		$provider->register();

		self::assertNotFalse(
			has_action(
				'wp_after_insert_post',
				( $fundrik_container->get( PostSyncListener::class ) )->sync( ... )
			)
		);

		ContainerManager::reset();
	}
}
