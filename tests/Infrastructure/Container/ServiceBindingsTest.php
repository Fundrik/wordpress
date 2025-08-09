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

	private ServiceBindings $service_bindings;

	private array $singletons;
	private array $bindings;

	protected function setUp(): void {

		parent::setUp();

		$this->service_bindings = new ServiceBindings();

		$this->singletons = $this->service_bindings->get_singletons();
		$this->bindings = $this->service_bindings->get_bindings();
	}

	// Singletons
	// ---------------------------------------------------------------------

	#[Test]
	#[DataProvider( 'interface_singletons' )]
	public function it_maps_interfaces_to_expected_singleton_implementations(
		string $interface,
		string $implementation,
	): void {

		$this->assertArrayHasKey( $interface, $this->singletons );
		$this->assertSame( $implementation, $this->singletons[ $interface ] );
	}

	#[Test]
	#[DataProvider( 'singleton_self_bindings' )]
	public function it_includes_singletons_as_self_binding( string $class ): void {

		$this->assertContains( $class, $this->singletons );
	}

	#[Test]
	public function get_singletons_returns_expected_array(): void {

		$this->assertIsArray( $this->singletons );
		$this->assertNotEmpty( $this->singletons );

		foreach ( $this->singletons as $abstract => $concrete ) {

			if ( ! is_int( $abstract ) ) {
				$this->assertIsString( $abstract );
				$this->assertTrue(
					interface_exists( $abstract ) || class_exists( $abstract ),
					"$abstract does not exist",
				);
			}

			$this->assertIsString( $concrete );
			$this->assertTrue( class_exists( $concrete ), "$concrete does not exist" );
		}
	}

	// Transients (non-singleton bindings)
	// ---------------------------------------------------------------------

	#[Test]
	#[DataProvider( 'interface_bindings' )]
	public function it_maps_interfaces_to_expected_bind_implementations( string $interface, string $implementation ): void {

		$this->assertArrayHasKey( $interface, $this->bindings );
		$this->assertSame( $implementation, $this->bindings [ $interface ] );
	}

	#[Test]
	public function get_bindings_returns_expected_array(): void {

		$this->assertIsArray( $this->bindings );
		$this->assertNotEmpty( $this->bindings );

		foreach ( $this->bindings as $abstract => $concrete ) {

			$this->assertIsString( $abstract );
			$this->assertTrue( interface_exists( $abstract ) || class_exists( $abstract ), "$abstract does not exist" );

			$this->assertIsString( $concrete );
			$this->assertTrue( class_exists( $concrete ), "$concrete does not exist" );
		}
	}

	// Coverage checks
	// ---------------------------------------------------------------------


	#[Test]
	public function all_bindings_are_covered_by_tests(): void {

		$expected_singletons = array_merge(
			array_map( static fn ( array $e ) => $e[0], iterator_to_array( self::interface_singletons() ) ),
			array_map( static fn ( array $e ) => $e[0], iterator_to_array( self::singleton_self_bindings() ) ),
		);

		$expected_bindings = array_map(
			static fn ( array $e ) => $e[0],
			iterator_to_array( self::interface_bindings() ),
		);

		foreach ( $this->singletons as $abstract => $concrete ) {

			$class = is_int( $abstract ) ? $concrete : $abstract;
			$this->assertContains(
				$class,
				$expected_singletons,
				"Singleton for `$class` is present but not covered by tests.",
			);
		}

		// phpcs:disable SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable
		foreach ( $this->bindings as $abstract => $concrete ) {

			$this->assertContains(
				$abstract,
				$expected_bindings,
				"Binding for `$abstract` is present but not covered by tests.",
			);
		}
	}

	// Registration into container
	// ---------------------------------------------------------------------

	#[Test]
	public function it_registers_all_bindings_into_the_container(): void {

		$container = Mockery::mock( ContainerInterface::class );

		foreach ( $this->singletons as $abstract => $concrete ) {

			if ( is_int( $abstract ) ) {
				$abstract = $concrete;
			}

			$container
				->shouldReceive( 'singleton' )
				->once()
				->with( $abstract, $concrete );
		}

		foreach ( $this->bindings as $abstract => $concrete ) {

			$container
				->shouldReceive( 'bind' )
				->once()
				->with( $abstract, $concrete );
		}

		$this->service_bindings->register_bindings_into_container( $container );
	}

	// Providers
	// ---------------------------------------------------------------------

	public static function interface_singletons(): iterable {

		yield [ CampaignServicePortInterface::class, CampaignService::class ];
		yield [ LaravelEventsDispatcherInterface::class, LaravelEventsDispatcher::class ];
		yield [ EventDispatcherInterface::class, EventDispatcher::class ];
		yield [ EventListenerRegistrarInterface::class, EventListenerRegistrar::class ];
		yield [ HookBridgeRegistrarInterface::class, HookBridgeRegistrar::class ];
		yield [ MigrationRunnerInterface::class, MigrationRunner::class ];
		yield [ DatabaseInterface::class, WpdbDatabase::class ];
		yield [ StorageInterface::class, WordPressOptionsStorage::class ];
	}

	public static function interface_bindings(): iterable {

		yield [ WordPressContextInterface::class, WordPressContext::class ];
	}

	public static function singleton_self_bindings(): iterable {

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
}
