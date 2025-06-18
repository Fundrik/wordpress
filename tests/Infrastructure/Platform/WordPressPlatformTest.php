<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Platform;

use Brain\Monkey\Functions;
use Fundrik\Core\Infrastructure\Interfaces\ContainerInterface;
use Fundrik\WordPress\Infrastructure\Container\ContainerRegistry;
use Fundrik\WordPress\Infrastructure\DependencyProvider;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationManager;
use Fundrik\WordPress\Infrastructure\Migrations\Interfaces\MigrationReferenceFactoryInterface;
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
use PHPUnit\Framework\Attributes\UsesFunction;
use RuntimeException;
use stdClass;

#[CoversClass( WordPressPlatform::class )]
#[UsesClass( ContainerRegistry::class )]
#[UsesClass( MigrationManager::class )]
#[UsesClass( AllowedBlockTypesFilter::class )]
#[UsesClass( Path::class )]
#[UsesFunction( 'fundrik' )]
final class WordPressPlatformTest extends FundrikTestCase {

	private WordPressPlatform $platform;
	private DependencyProvider&MockInterface $dependency_provider;
	private ContainerInterface&MockInterface $container;

	protected function setUp(): void {
		parent::setUp();

		$this->dependency_provider = Mockery::mock( DependencyProvider::class );
		$this->container           = Mockery::mock( ContainerInterface::class );

		ContainerRegistry::set( $this->container );

		$allowed_block_types_filter = new AllowedBlockTypesFilter(
			[ 'core/paragraph', 'core/image', 'core/quote' ]
		);

		$this->platform = new WordPressPlatform(
			$this->dependency_provider,
			$allowed_block_types_filter,
			new MigrationManager(
				Mockery::mock( 'wpdb' ),
				Mockery::mock( MigrationReferenceFactoryInterface::class )
			)
		);
	}

	#[Test]
	public function init_registers_init_hooks(): void {

		$this->dependency_provider
			->shouldReceive( 'get_bindings' )
			->once()
			->with( 'listeners' )
			->andReturn( [] );

		$this->platform->init();

		self::assertNotFalse(
			has_action( 'init', $this->platform->register_post_types( ... ) )
		);

		self::assertNotFalse(
			has_action( 'init', $this->platform->register_blocks( ... ) )
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

		$this->dependency_provider
			->shouldReceive( 'get_bindings' )
			->once()
			->with( 'post_types' )
			->andReturn( [ 'PostType1', 'PostType2' ] );

		$this->container
			->shouldReceive( 'get' )
			->with( 'PostType1' )
			->andReturn( $post_type_mock_1 );
		$this->container
			->shouldReceive( 'get' )
			->with( 'PostType2' )
			->andReturn( $post_type_mock_2 );

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
	}

	#[Test]
	public function it_throws_if_get_post_types_fails(): void {

		$this->dependency_provider
			->shouldReceive( 'get_bindings' )
			->once()
			->with( 'post_types' )
			->andReturn( [ stdClass::class ] );

		$this->container
			->shouldReceive( 'get' )
			->with( $this->identicalTo( stdClass::class ) )
			->andReturn( new stdClass() );

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'Expected instance of PostTypeInterface, got stdClass for class stdClass' );

		$this->platform->register_post_types();
	}

	#[Test]
	public function on_activate_calls_migration_manager_migrate(): void {

		Functions\when( 'get_option' )->justReturn( '0000_00_00_00' );
		Functions\when( 'update_option' )->justReturn( true );

		$wpdb_mock = Mockery::mock( 'wpdb' );
		$wpdb_mock
			->shouldReceive( 'get_charset_collate' )
			->andReturn( 'charset_collate' );

		$reference_factory_mock = Mockery::mock( MigrationReferenceFactoryInterface::class );
		$reference_factory_mock
			->shouldReceive( 'create_all' )
			->andReturn( [] );

		$migration_manager = new MigrationManager(
			$wpdb_mock,
			$reference_factory_mock,
		);

		$platform = new WordPressPlatform(
			$this->dependency_provider,
			new AllowedBlockTypesFilter( [] ),
			$migration_manager
		);

		$platform->on_activate();

		$this->addToAssertionCount( 1 );
	}

	#[Test]
	public function register_blocks_registers_all_blocks(): void {

		Functions\expect( 'wp_register_block_types_from_metadata_collection' )
			->once()
			->with(
				$this->identicalTo( Path::Blocks->get_full_path() ),
				$this->identicalTo( Path::BlocksManifest->get_full_path() )
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
	public function register_listeners_registers_all_listeners(): void {

		$listener_mock_1 = Mockery::mock( ListenerInterface::class );
		$listener_mock_1->shouldReceive( 'register' )->once();

		$listener_mock_2 = Mockery::mock( ListenerInterface::class );
		$listener_mock_2->shouldReceive( 'register' )->once();

		$this->dependency_provider
			->shouldReceive( 'get_bindings' )
			->once()
			->with( 'listeners' )
			->andReturn( [ 'Listener1', 'Listener2' ] );

		$this->container
			->shouldReceive( 'get' )
			->with( 'Listener1' )
			->andReturn( $listener_mock_1 );
		$this->container
			->shouldReceive( 'get' )
			->with( 'Listener2' )
			->andReturn( $listener_mock_2 );

		$this->platform->init();
	}

	#[Test]
	public function it_throws_if_get_listeners_fails(): void {

		$this->dependency_provider
			->shouldReceive( 'get_bindings' )
			->once()
			->with( 'listeners' )
			->andReturn( [ stdClass::class ] );

		$this->container
			->shouldReceive( 'get' )
			->with( $this->identicalTo( stdClass::class ) )
			->andReturn( new stdClass() );

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'Expected instance of ListenerInterface, got stdClass for class stdClass' );

		$this->platform->init();
	}
}
