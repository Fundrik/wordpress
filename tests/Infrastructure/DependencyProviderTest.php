<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure;

use Brain\Monkey\Filters;
use Fundrik\WordPress\Application\Campaigns\Interfaces\WordPressCampaignRepositoryInterface;
use Fundrik\WordPress\Application\Campaigns\Interfaces\WordPressCampaignServiceInterface;
use Fundrik\WordPress\Application\Validation\Interfaces\ValidationErrorTransformerInterface;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces\WordPressCampaignSyncListenerInterface;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignPostType;
use Fundrik\WordPress\Infrastructure\DependencyProvider;
use Fundrik\WordPress\Infrastructure\Migrations\Interfaces\MigrationReferenceFactoryInterface;
use Fundrik\WordPress\Infrastructure\Persistence\Interfaces\QueryExecutorInterface;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use wpdb;

#[CoversClass( DependencyProvider::class )]
final class DependencyProviderTest extends FundrikTestCase {

	private DependencyProvider $provider;

	protected function setUp(): void {

		parent::setUp();

		$this->provider = new DependencyProvider();
	}

	#[Test]
	public function get_bindings_contains_all_required_keys(): void {

		$bindings = $this->provider->get_bindings();

		$all_keys = [];

		foreach ( $bindings as $category_bindings ) {
			$all_keys = array_merge( $all_keys, array_keys( $category_bindings ) );
		}

		$required_keys = [
			wpdb::class,
			QueryExecutorInterface::class,
			WordPressCampaignRepositoryInterface::class,
			WordPressCampaignServiceInterface::class,
			MigrationReferenceFactoryInterface::class,
			ValidationErrorTransformerInterface::class,
			ValidatorInterface::class,
			WordPressCampaignPostType::class,
			WordPressCampaignSyncListenerInterface::class,
		];

		foreach ( $required_keys as $key ) {
			$this->assertContains( $key, $all_keys, "Missing required binding: $key" );
		}
	}

	#[Test]
	public function get_bindings_returns_specific_category_if_exists(): void {

		$bindings = $this->provider->get_bindings();
		$result = $this->provider->get_bindings( 'post_types' );

		$this->assertSame( $bindings['post_types'], $result );
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
			->with( Mockery::type( 'array' ) )
			->andReturnUsing(
				static function ( array $bindings ): array {
					$bindings['custom_category']['custom_binding'] = 'CustomClass';
					return $bindings;
				},
			);

		$bindings = $this->provider->get_bindings();

		$this->assertArrayHasKey( 'custom_category', $bindings );
		$this->assertArrayHasKey( 'custom_binding', $bindings['custom_category'] );
		$this->assertSame( 'CustomClass', $bindings['custom_category']['custom_binding'] );
	}
}
