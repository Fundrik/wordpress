<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Platform;

use Brain\Monkey\Functions;
use Closure;
use Fundrik\Core\Infrastructure\Internal\Container;
use Fundrik\Core\Infrastructure\Internal\ContainerManager;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignInputFactory;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignService;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaignFactory;
use Fundrik\WordPress\Infrastructure\Campaigns\Persistence\WpdbWordPressCampaignRepository;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignPostMapper;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignPostType;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignSyncListener;
use Fundrik\WordPress\Infrastructure\DependencyProvider;
use Fundrik\WordPress\Infrastructure\Persistence\WpdbQueryExecutor;
use Fundrik\WordPress\Infrastructure\Platform\AllowedBlockTypesFilter;
use Fundrik\WordPress\Infrastructure\Platform\Interfaces\ListenerInterface;
use Fundrik\WordPress\Infrastructure\Platform\Interfaces\PostTypeInterface;
use Fundrik\WordPress\Infrastructure\Platform\WordPressPlatform;
use Fundrik\WordPress\Support\Path;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use stdClass;

#[CoversClass( WordPressPlatform::class )]
#[UsesClass( WordPressCampaignService::class )]
#[UsesClass( WordPressCampaignFactory::class )]
#[UsesClass( WpdbWordPressCampaignRepository::class )]
#[UsesClass( WordPressCampaignPostMapper::class )]
#[UsesClass( WordPressCampaignSyncListener::class )]
#[UsesClass( DependencyProvider::class )]
#[UsesClass( WpdbQueryExecutor::class )]
#[UsesClass( AdminWordPressCampaignInputFactory::class )]
#[UsesClass( WordPressCampaignPostType::class )]
#[UsesClass( Path::class )]
#[UsesClass( AllowedBlockTypesFilter::class )]
class WordPressPlatformTest extends FundrikTestCase {

	private WordPressPlatform $platform;
	private DependencyProvider&MockInterface $dependency_provider;

	protected function setUp(): void {

		parent::setUp();

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['wpdb'] = Mockery::mock( 'wpdb' );

		$this->dependency_provider = Mockery::mock( DependencyProvider::class );

		$allowed_block_types_filter = new AllowedBlockTypesFilter(
			[ 'core/paragraph', 'core/image', 'core/quote' ]
		);

		$this->platform = new WordPressPlatform(
			$this->dependency_provider,
			$allowed_block_types_filter,
		);
	}

	#[Test]
	public function init_registers_init_hooks(): void {

		// WordPressPlatform::init() internally calls get_bindings() with no arguments
		// and get_bindings() with 'listeners' argument,
		// so even though this is not the focus of the current test,
		// we need to mock it to avoid unexpected calls and test failures.
		$this->dependency_provider
			->shouldReceive( 'get_bindings' )
			->once()
			->withNoArgs()
			->andReturn( [] );

		$this->dependency_provider
			->shouldReceive( 'get_bindings' )
			->once()
			->with( 'listeners' )
			->andReturn( [] );

		$this->platform->init();

		self::assertNotFalse(
			has_action(
				'init',
				$this->platform->register_post_types( ... )
			)
		);

		self::assertNotFalse(
			has_action(
				'init',
				$this->platform->register_blocks( ... )
			)
		);

		self::assertNotFalse(
			has_filter(
				'allowed_block_types_all',
				$this->platform->filter_allowed_blocks_by_post_type( ... )
			)
		);
	}

	#[Test]
	public function register_post_types_registers_post_types(): void {

		$container = ContainerManager::get_fresh();

		$post_type_mock_1 = Mockery::mock( PostTypeInterface::class );
		$post_type_mock_1->shouldReceive( 'get_type' )->twice()->andReturn( 'type_1' );
		$post_type_mock_1->shouldReceive( 'get_labels' )->once()->andReturn( [ 'name' => 'Type 1' ] );
		$post_type_mock_1->shouldReceive( 'get_slug' )->once()->andReturn( 'type-1-slug' );
		$post_type_mock_1->shouldReceive( 'get_template_blocks' )->once()->andReturn( [] );
		$post_type_mock_1->shouldReceive( 'get_meta_fields' )->once()->andReturn(
			[
				'meta_key_1' => [ 'type' => 'string' ],
			]
		);

		$post_type_mock_2 = Mockery::mock( PostTypeInterface::class );
		$post_type_mock_2->shouldReceive( 'get_type' )->twice()->andReturn( 'type_2' );
		$post_type_mock_2->shouldReceive( 'get_labels' )->once()->andReturn( [ 'name' => 'Type 2' ] );
		$post_type_mock_2->shouldReceive( 'get_slug' )->once()->andReturn( 'type-2-slug' );
		$post_type_mock_2->shouldReceive( 'get_template_blocks' )->once()->andReturn( [] );
		$post_type_mock_2->shouldReceive( 'get_meta_fields' )->once()->andReturn(
			[
				'meta_key_2' => [ 'type' => 'boolean' ],
			]
		);

		$container->singleton( 'PostType1', fn() => $post_type_mock_1 );
		$container->singleton( 'PostType2', fn() => $post_type_mock_2 );

		$this->dependency_provider
			->shouldReceive( 'get_bindings' )
			->once()
			->with( 'post_types' )
			->andReturn( [ 'PostType1', 'PostType2' ] );

		Functions\expect( 'register_post_type' )
			->twice()
			->andReturnUsing(
				function ( string $type, array $args ) {
					$this->assertStringStartsWith( 'type_', $type );
					$this->assertArrayHasKey( 'labels', $args );
					$this->assertArrayHasKey( 'rewrite', $args );
					$this->assertArrayHasKey( 'template', $args );
				}
			);

		Functions\expect( 'register_post_meta' )
			->twice()
			->andReturnUsing(
				function ( string $post_type, string $meta_key, array $args ) {
					$this->assertStringStartsWith( 'type_', $post_type );
					$this->assertStringStartsWith( 'meta_key_', $meta_key );
					$this->assertArrayHasKey( 'show_in_rest', $args );
					$this->assertArrayHasKey( 'single', $args );
				}
			);

		Functions\expect( 'wp_parse_args' )
			->twice()
			->andReturnUsing(
				function ( $args, $defaults ) {
					return array_merge( $defaults, $args );
				}
			);

		$this->platform->register_post_types();

		ContainerManager::reset();
	}

	#[Test]
	public function register_blocks_registers_all_blocks(): void {

		Functions\expect( 'wp_register_block_types_from_metadata_collection' )
			->once()
			->with(
				Path::blocks(),
				Path::blocks_manifest()
			);

		$this->platform->register_blocks();
	}

	#[Test]
	public function filter_allowed_blocks_by_post_type_returns_filtered_result(): void {

		$allowed_blocks = true;
		$post_type      = 'custom_type';

		$post            = Mockery::mock( 'WP_Post' );
		$post->post_type = $post_type;

		$editor_context       = Mockery::mock( 'WP_Block_Editor_Context' );
		$editor_context->post = $post;

		$this->dependency_provider
			->shouldReceive( 'get_bindings' )
			->once()
			->with( 'post_types' )
			->andReturn( [] );

		$result = $this->platform->filter_allowed_blocks_by_post_type(
			$allowed_blocks,
			$editor_context
		);

		$this->assertIsArray( $result );
	}

	#[Test]
	public function register_bindings_registers_dependencies(): void {

		$container = ContainerManager::get_fresh();

		$bindings = [
			'Some\Simple\Interface' => stdClass::class,
			'grouped'               => [
				'Some\Grouped\Interface1' => stdClass::class,
				'Some\Grouped\Interface2' => stdClass::class,
			],
		];

		// WordPressPlatform::init() internally calls get_bindings() with 'listeners' argument,
		// so even though this is not the focus of the current test,
		// we need to mock it to avoid unexpected calls and test failures.
		$this->dependency_provider
			->shouldReceive( 'get_bindings' )
			->once()
			->with( 'listeners' )
			->andReturn( [] );

		$this->dependency_provider
			->shouldReceive( 'get_bindings' )
			->once()
			->withNoArgs()
			->andReturn( $bindings );

		$this->platform->init();

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

		// WordPressPlatform::init() internally calls get_bindings() with no arguments,
		// so even though this is not the focus of the current test,
		// we need to mock it to avoid unexpected calls and test failures.
		$this->dependency_provider
			->shouldReceive( 'get_bindings' )
			->once()
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
			->once()
			->with( 'listeners' )
			->andReturn( [ 'Listener1', 'Listener2' ] );

		$this->platform->init();

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
