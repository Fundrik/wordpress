<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure;

use Brain\Monkey\Filters;
use Fundrik\Core\Domain\Campaigns\Interfaces\CampaignRepositoryInterface;
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
	public function get_bindings_contains_all_required_keys(): void {

		$bindings      = $this->provider->get_bindings();
		$all_keys      = $this->collect_keys_recursively( $bindings );
		$required_keys = [
			wpdb::class,
			QueryExecutorInterface::class,
			CampaignRepositoryInterface::class,
		];

		foreach ( $required_keys as $key ) {
			$this->assertContains( $key, $all_keys, "Missing required binding: $key" );
		}
	}

	#[Test]
	public function get_bindings_returns_specific_category_if_exists(): void {

		$bindings = $this->provider->get_bindings();

		$result = $this->provider->get_bindings( 'core' );

		$this->assertEquals( $bindings['core'], $result );
	}

	#[Test]
	public function get_bindings_returns_empty_array_if_category_does_not_exist(): void {

		$result = $this->provider->get_bindings( 'non_existing_category' );

		$this->assertIsArray( $result );
		$this->assertEmpty( $result );
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

	private function collect_keys_recursively( array $items ): array {

		$keys = [];

		foreach ( $items as $key => $value ) {
			$keys[] = $key;

			if ( is_array( $value ) ) {
				$keys = array_merge( $keys, $this->collect_keys_recursively( $value ) );
			}
		}

		return $keys;
	}
}
