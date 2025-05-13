<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure;

use Brain\Monkey\Filters;
use Fundrik\Core\Domain\Campaigns\Interfaces\CampaignRepositoryInterface;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces\CampaignSyncListenerInterface;
use Fundrik\WordPress\Infrastructure\DependencyProvider;
use Fundrik\WordPress\Infrastructure\Persistence\Interfaces\QueryExecutorInterface;
use Fundrik\WordPress\Tests\FundrikTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use wpdb;

#[CoversClass( DependencyProvider::class )]
class DependencyProviderTest extends FundrikTestCase {

	private DependencyProvider $provider;

	protected function setUp(): void {

		parent::setUp();

		$this->provider = new DependencyProvider();
	}

	#[Test]
	public function get_bindings_returns_all_bindings(): void {

		$bindings = $this->provider->get_bindings();

		$this->assertArrayHasKey( wpdb::class, $bindings );
		$this->assertArrayHasKey( QueryExecutorInterface::class, $bindings );
		$this->assertArrayHasKey( CampaignRepositoryInterface::class, $bindings );
		$this->assertArrayHasKey( 'listeners', $bindings );
	}

	#[Test]
	public function get_bindings_returns_bindings_for_category(): void {

		$listeners = $this->provider->get_bindings( 'listeners' );

		$this->assertArrayHasKey( CampaignSyncListenerInterface::class, $listeners );
	}

	#[Test]
	public function get_bindings_can_be_modified_via_filter(): void {

		Filters\expectApplied( 'fundrik_container_bindings' )
			->once()
			->with( \Mockery::type( 'array' ) )
			->andReturnUsing(
				static function ( array $bindings ): array {
					$bindings['custom_binding'] = 'CustomClass';
					return $bindings;
				}
			);

		$bindings = $this->provider->get_bindings();

		$this->assertArrayHasKey( 'custom_binding', $bindings );
		$this->assertSame( 'CustomClass', $bindings['custom_binding'] );
	}
}
