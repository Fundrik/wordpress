<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Platform;

use Brain\Monkey\Functions;
use Closure;
use Fundrik\Core\Infrastructure\Internal\Container;
use Fundrik\Core\Infrastructure\Internal\ContainerManager;
use Fundrik\WordPress\Infrastructure\Campaigns\Persistence\WpdbCampaignRepository;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\CampaignPostToCampaignDtoMapper;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\CampaignPostType;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\CampaignSyncProvider;
use Fundrik\WordPress\Infrastructure\DependencyProvider;
use Fundrik\WordPress\Infrastructure\Persistence\WpdbQueryExecutor;
use Fundrik\WordPress\Infrastructure\Platform\PostSyncListener;
use Fundrik\WordPress\Infrastructure\Platform\WordPressPlatform;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( WordPressPlatform::class )]
#[UsesClass( WpdbCampaignRepository::class )]
#[UsesClass( CampaignPostToCampaignDtoMapper::class )]
#[UsesClass( CampaignPostType::class )]
#[UsesClass( CampaignSyncProvider::class )]
#[UsesClass( DependencyProvider::class )]
#[UsesClass( WpdbQueryExecutor::class )]
#[UsesClass( PostSyncListener::class )]
class WordPressPlatformTest extends FundrikTestCase {

	private WordPressPlatform $platform;

	protected function setUp(): void {

		parent::setUp();

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['wpdb'] = Mockery::mock( 'wpdb' );

		$this->platform = new WordPressPlatform( new DependencyProvider() );
	}

	#[Test]
	public function init_registers_init_hooks(): void {

		$this->platform->init();

		self::assertNotFalse(
			has_action(
				'init',
				$this->platform->register_post_types( ... )
			)
		);
	}

	#[Test]
	public function register_bindings_registers_dependencies(): void {

		$container = ContainerManager::get_fresh();

		$this->platform->init();

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
	public function register_post_types_registers_post_types(): void {

		Functions\when( '__' )->returnArg();

		Functions\expect( 'register_post_type' )
			->once()
			->with(
				CampaignPostType::get_type(),
				[
					'labels'       => CampaignPostType::get_labels(),
					'public'       => true,
					'menu_icon'    => 'dashicons-heart',
					'supports'     => [ 'title', 'editor' ],
					'has_archive'  => true,
					'rewrite'      => [ 'slug' => CampaignPostType::get_rewrite_slug() ],
					'show_in_rest' => true,
				]
			);

		$this->platform->register_post_types();
	}

	private function assertSingletonBinding(
		Container $container,
		string $abstract_name,
		string|Closure $concrete
	): void {

		if ( is_callable( $concrete ) ) {
			$concrete = $concrete()::class;
		}

		$instance1 = $container->get( $abstract_name );
		$this->assertInstanceOf( $concrete, $instance1 );

		$instance2 = $container->get( $abstract_name );
		$this->assertSame( $instance1, $instance2 );
	}
}
