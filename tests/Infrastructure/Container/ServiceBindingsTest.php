<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Container;

use Fundrik\WordPress\Application;
use Fundrik\WordPress\Components\Campaigns\Application\CampaignAssembler;
use Fundrik\WordPress\Components\Campaigns\Application\CampaignDtoFactory;
use Fundrik\WordPress\Components\Campaigns\Application\CampaignService;
use Fundrik\WordPress\Components\Campaigns\Application\Ports\In\CampaignServicePortInterface;
use Fundrik\WordPress\Infrastructure\Container\ContainerInterface;
use Fundrik\WordPress\Infrastructure\Container\ServiceBindings;
use Fundrik\WordPress\Infrastructure\DatabaseInterface;
use Fundrik\WordPress\Infrastructure\EventDispatcher\EventDispatcher;
use Fundrik\WordPress\Infrastructure\EventDispatcher\EventDispatcherInterface;
use Fundrik\WordPress\Infrastructure\EventDispatcher\EventListenerRegistrar;
use Fundrik\WordPress\Infrastructure\EventDispatcher\EventListenerRegistrarInterface;
use Fundrik\WordPress\Infrastructure\Integration\HookBridges\HookBridgeRegistrar;
use Fundrik\WordPress\Infrastructure\Integration\HookBridges\HookBridgeRegistrarInterface;
use Fundrik\WordPress\Infrastructure\Integration\HookBridges\HookBridgeRegistry;
use Fundrik\WordPress\Infrastructure\Integration\PostTypes\Attributes\PostTypeBlockTemplateReader;
use Fundrik\WordPress\Infrastructure\Integration\PostTypes\Attributes\PostTypeIdReader;
use Fundrik\WordPress\Infrastructure\Integration\PostTypes\Attributes\PostTypeMetaFieldReader;
use Fundrik\WordPress\Infrastructure\Integration\PostTypes\Attributes\PostTypeSlugReader;
use Fundrik\WordPress\Infrastructure\Integration\PostTypes\Attributes\PostTypeSpecificBlockReader;
use Fundrik\WordPress\Infrastructure\Integration\WordPressContext\WordPressContext;
use Fundrik\WordPress\Infrastructure\Integration\WordPressContext\WordPressContextFactory;
use Fundrik\WordPress\Infrastructure\Integration\WordPressContext\WordPressContextInterface;
use Fundrik\WordPress\Infrastructure\Integration\WordPressOptionsStorage;
use Fundrik\WordPress\Infrastructure\Integration\WpdbDatabase;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationRegistry;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationRunner;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationRunnerInterface;
use Fundrik\WordPress\Infrastructure\StorageInterface;
use Fundrik\WordPress\Tests\MockeryTestCase;
use Illuminate\Contracts\Events\Dispatcher as LaravelEventsDispatcherInterface;
use Illuminate\Events\Dispatcher as LaravelEventsDispatcher;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( ServiceBindings::class )]
final class ServiceBindingsTest extends MockeryTestCase {

	#[Test]
	#[DataProvider( 'interface_bindings' )]
	public function it_maps_interface_to_expected_implementation( string $interface, string $implementation ): void {

		$bindings = ( new ServiceBindings() )->get_bindings();

		$this->assertArrayHasKey( $interface, $bindings );
		$this->assertSame( $implementation, $bindings[ $interface ] );
	}

	public static function interface_bindings(): iterable {

		yield [ CampaignServicePortInterface::class, CampaignService::class ];
		yield [ LaravelEventsDispatcherInterface::class, LaravelEventsDispatcher::class ];
		yield [ EventDispatcherInterface::class, EventDispatcher::class ];
		yield [ EventListenerRegistrarInterface::class, EventListenerRegistrar::class ];
		yield [ HookBridgeRegistrarInterface::class, HookBridgeRegistrar::class ];
		yield [ MigrationRunnerInterface::class, MigrationRunner::class ];
		yield [ DatabaseInterface::class, WpdbDatabase::class ];
		yield [ StorageInterface::class, WordPressOptionsStorage::class ];
		yield [ WordPressContextInterface::class, WordPressContext::class ];
	}

	#[Test]
	#[DataProvider( 'singleton_bindings' )]
	public function it_includes_singletons_as_self_binding( string $class ): void {

		$bindings = ( new ServiceBindings() )->get_bindings();

		$this->assertContains( $class, $bindings );
	}

	public static function singleton_bindings(): iterable {

		yield [ Application::class ];
		yield [ CampaignAssembler::class ];
		yield [ CampaignDtoFactory::class ];
		yield [ HookBridgeRegistry::class ];
		yield [ MigrationRegistry::class ];
		yield [ PostTypeBlockTemplateReader::class ];
		yield [ PostTypeIdReader::class ];
		yield [ PostTypeMetaFieldReader::class ];
		yield [ PostTypeSlugReader::class ];
		yield [ PostTypeSpecificBlockReader::class ];
		yield [ WordPressContextFactory::class ];
	}

	#[Test]
	public function all_bindings_are_covered_by_tests(): void {

		$bindings = ( new ServiceBindings() )->get_bindings();

		$expected = array_merge(
			array_map(
				static fn ( array $entry ) => $entry[0],
				iterator_to_array( self::interface_bindings() ),
			),
			array_map(
				static fn ( array $entry ) => $entry[0],
				iterator_to_array( self::singleton_bindings() ),
			),
		);

		foreach ( $bindings as $abstract => $concrete ) {

			$class = is_int( $abstract ) ? $concrete : $abstract;

			$this->assertContains( $class, $expected, "Binding for `$class` is present but not covered by tests." );
		}
	}

	#[Test]
	public function it_registers_all_bindings_into_the_container(): void {

		$bindings = ( new ServiceBindings() )->get_bindings();
		$expected_calls = [];

		foreach ( $bindings as $abstract => $concrete ) {

			if ( is_int( $abstract ) ) {
				$abstract = $concrete;
			}

			$expected_calls[] = [ $abstract, $concrete ];
		}

		$container = Mockery::mock( ContainerInterface::class );

		foreach ( $bindings as $abstract => $concrete ) {

			if ( is_int( $abstract ) ) {
				$abstract = $concrete;
			}

			$container->shouldReceive( 'singleton' )
				->once()
				->with( $abstract, $concrete );
		}

		( new ServiceBindings() )->register_bindings_into_container( $container );
	}

	#[Test]
	public function get_bindings_returns_expected_array(): void {

		$bindings = ( new ServiceBindings() )->get_bindings();

		$this->assertIsArray( $bindings );
		$this->assertNotEmpty( $bindings );

		foreach ( $bindings as $abstract => $concrete ) {

			if ( ! is_int( $abstract ) ) {
				$this->assertIsString( $abstract );
			}

			$this->assertIsString( $concrete );
			$this->assertTrue( class_exists( $concrete ), "$concrete does not exist" );
		}
	}
}
