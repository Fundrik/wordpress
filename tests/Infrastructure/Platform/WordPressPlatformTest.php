<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Platform;

use Brain\Monkey\Functions;
use Closure;
use Fundrik\Core\Infrastructure\Internal\Container;
use Fundrik\Core\Infrastructure\Internal\ContainerManager;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignService;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaignFactory;
use Fundrik\WordPress\Infrastructure\Campaigns\Persistence\WpdbWordPressCampaignRepository;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignPostMapper;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignSyncListener;
use Fundrik\WordPress\Infrastructure\DependencyProvider;
use Fundrik\WordPress\Infrastructure\Persistence\WpdbQueryExecutor;
use Fundrik\WordPress\Infrastructure\Platform\Interfaces\ListenerInterface;
use Fundrik\WordPress\Infrastructure\Platform\Interfaces\PostTypeInterface;
use Fundrik\WordPress\Infrastructure\Platform\WordPressPlatform;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( WordPressPlatform::class )]
#[UsesClass( WordPressCampaignService::class )]
#[UsesClass( WordPressCampaignFactory::class )]
#[UsesClass( WpdbWordPressCampaignRepository::class )]
#[UsesClass( WordPressCampaignPostMapper::class )]
#[UsesClass( WordPressCampaignSyncListener::class )]
#[UsesClass( DependencyProvider::class )]
#[UsesClass( WpdbQueryExecutor::class )]
class WordPressPlatformTest extends FundrikTestCase {

	private WordPressPlatform $platform_mocked;
	private WordPressPlatform $platform_real;
	private DependencyProvider&MockInterface $dependency_provider;

	protected function setUp(): void {

		parent::setUp();

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['wpdb'] = Mockery::mock( 'wpdb' );

		$this->dependency_provider = Mockery::mock( DependencyProvider::class );

		$this->platform_mocked = new WordPressPlatform( $this->dependency_provider );
		$this->platform_real   = new WordPressPlatform( new DependencyProvider() );
	}

	#[Test]
	public function init_registers_init_hooks(): void {

		$this->platform_real->init();

		self::assertNotFalse(
			has_action(
				'init',
				$this->platform_real->register_post_types( ... )
			)
		);
	}

	#[Test]
	public function register_post_types_registers_post_types(): void {

		$container = ContainerManager::get_fresh();

		$post_type_mock_1 = Mockery::mock( PostTypeInterface::class );
		$post_type_mock_1->shouldReceive( 'get_type' )->andReturn( 'type_1' );
		$post_type_mock_1->shouldReceive( 'get_labels' )->andReturn( [ 'name' => 'Type 1' ] );
		$post_type_mock_1->shouldReceive( 'get_rewrite_slug' )->andReturn( 'type-1-slug' );

		$post_type_mock_2 = Mockery::mock( PostTypeInterface::class );
		$post_type_mock_2->shouldReceive( 'get_type' )->andReturn( 'type_2' );
		$post_type_mock_2->shouldReceive( 'get_labels' )->andReturn( [ 'name' => 'Type 2' ] );
		$post_type_mock_2->shouldReceive( 'get_rewrite_slug' )->andReturn( 'type-2-slug' );

		$container->singleton( 'PostType1', fn() => $post_type_mock_1 );
		$container->singleton( 'PostType2', fn() => $post_type_mock_2 );

		$this->dependency_provider
			->shouldReceive( 'get_bindings' )
			->with( 'post_types' )
			->andReturn( [ 'PostType1', 'PostType2' ] );

		Functions\expect( 'register_post_type' )
			->twice()
			->andReturnUsing(
				function ( string $type, array $args ) {
					$this->assertStringContainsString( 'type_', $type );
					$this->assertArrayHasKey( 'labels', $args );
				}
			);

		$this->platform_mocked->register_post_types();

		ContainerManager::reset();
	}

	#[Test]
	public function register_bindings_registers_dependencies(): void {

		$container = ContainerManager::get_fresh();

		$this->platform_real->init();

		$bindings = ( new DependencyProvider() )->get_bindings();

		foreach ( $bindings as $abstract => $concrete ) {

			if ( is_array( $concrete ) ) {

				foreach ( $concrete as $a => $c ) {
					$this->assertSingletonBinding( $container, $a, $c );
				}

				continue;
			}

			$this->assertSingletonBinding( $container, $abstract, $concrete );
		}

		ContainerManager::reset();
	}

	#[Test]
	public function register_listeners_registers_all_listeners(): void {

		// WordPressPlatform::init() calls register_bindings() first,
		// which internally calls get_bindings() with no arguments.
		// Even though this call is not directly relevant to this test,
		// we still need to mock it to avoid unexpected behavior.
		$this->dependency_provider
			->shouldReceive( 'get_bindings' )
			->withNoArgs()
			->andReturn( [] );

		$container = ContainerManager::get_fresh();

		$listener_mock_1 = Mockery::mock( ListenerInterface::class );
		$listener_mock_1->shouldReceive( 'register' )->once();

		$listener_mock_2 = Mockery::mock( ListenerInterface::class );
		$listener_mock_2->shouldReceive( 'register' )->once();

		$container->singleton( 'Listener1', fn() => $listener_mock_1 );
		$container->singleton( 'Listener2', fn() => $listener_mock_2 );

		$this->dependency_provider
			->shouldReceive( 'get_bindings' )
			->with( 'listeners' )
			->andReturn( [ 'Listener1', 'Listener2' ] );

		$this->platform_mocked->init();

		ContainerManager::reset();
	}

	private function assertSingletonBinding(
		Container $container,
		string|int $abstract_name,
		string|Closure $concrete,
	): void {

		if ( is_callable( $concrete ) ) {
			$concrete = $concrete()::class;
		}

		if ( is_int( $abstract_name ) ) {

			$instance1 = $container->get( $concrete );
			$this->assertInstanceOf( $concrete, $instance1 );
		} else {

			$instance1 = $container->get( $abstract_name );
			$this->assertInstanceOf( $concrete, $instance1 );

			$instance2 = $container->get( $abstract_name );
			$this->assertSame( $instance1, $instance2 );
		}
	}
}
