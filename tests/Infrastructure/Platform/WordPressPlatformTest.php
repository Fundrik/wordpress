<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Platform;

use Brain\Monkey\Functions;
use Fundrik\Core\Domain\Campaigns\Interfaces\QueryExecutorInterface;
use Fundrik\Core\Infrastructure\Internal\ContainerManager;
use Fundrik\WordPress\Infrastructure\Campaigns\Persistence\WpdbQueryExecutor;
use Fundrik\WordPress\Infrastructure\Platform\WordpressPlatform;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( WordpressPlatform::class )]
#[UsesClass( WpdbQueryExecutor::class )]
class WordpressPlatformTest extends FundrikTestCase {

	private WordpressPlatform $platform;

	protected function setUp(): void {

		parent::setUp();

		$this->platform = new WordpressPlatform();
	}

	#[Test]
	public function init_calls_register_post_types(): void {

		$this->platform->init();

		self::assertNotFalse(
			has_action(
				'init',
				$this->platform->register_post_types( ... )
			)
		);
	}

	#[Test]
	public function setup_container_registers_dependencies(): void {

		$container = ContainerManager::get_fresh();

		$wpdb = Mockery::mock( 'wpdb' );
		$container->singleton( 'wpdb', fn () => $wpdb );

		$this->platform->init();

		$query_executor = $container->get( QueryExecutorInterface::class );

		$this->assertInstanceOf( WpdbQueryExecutor::class, $query_executor );

		$second_instance = $container->get( QueryExecutorInterface::class );

		$this->assertEquals( $query_executor, $second_instance );

		ContainerManager::reset();
	}

	#[Test]
	public function it_registers_post_types(): void {

		Functions\when( '__' )->returnArg();

		Functions\expect( 'register_post_type' )
			->once()
			->with(
				'fundrik_campaign',
				Mockery::on(
					static function ( $args ): bool {
						return is_array( $args )
						&& isset( $args['labels']['name'] )
						&& 'Campaigns' === $args['labels']['name'];
					}
				)
			);

		$this->platform->register_post_types();
	}
}
