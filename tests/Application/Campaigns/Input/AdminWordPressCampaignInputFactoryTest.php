<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Application\Campaigns\Input;

use Fundrik\Core\Infrastructure\Interfaces\ContainerInterface;
use Fundrik\WordPress\Application\Campaigns\Input\Abstracts\AbstractAdminWordPressCampaignInput;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignInput;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignInputFactory;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces\WordPressCampaignPostMapperInterface;
use Fundrik\WordPress\Infrastructure\Container\ContainerRegistry;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesFunction;
use WP_Post;

#[CoversClass( AdminWordPressCampaignInputFactory::class )]
#[UsesClass( AdminWordPressCampaignInput::class )]
#[UsesClass( ContainerRegistry::class )]
#[UsesFunction( 'fundrik' )]
final class AdminWordPressCampaignInputFactoryTest extends FundrikTestCase {

	private ContainerInterface&MockInterface $container;
	private WordPressCampaignPostMapperInterface&MockInterface $mapper;

	private AdminWordPressCampaignInputFactory $factory;

	protected function setUp(): void {

		parent::setUp();

		$this->container = Mockery::mock( ContainerInterface::class );

		ContainerRegistry::set( $this->container );

		$this->mapper = Mockery::mock( WordPressCampaignPostMapperInterface::class );
		$this->factory = new AdminWordPressCampaignInputFactory( $this->mapper );
	}

	#[Test]
	public function from_array_creates_input_correctly(): void {

		$expected = new AdminWordPressCampaignInput(
			id: 10,
			title: 'Sample Campaign',
			slug: 'sample-campaign',
			is_enabled: true,
			is_open: true,
			has_target: true,
			target_amount: 1_500,
		);

		$this->container
			->shouldReceive( 'make' )
			->once()
			->with(
				AbstractAdminWordPressCampaignInput::class,
				[
					'id' => 10,
					'title' => 'Sample Campaign',
					'slug' => 'sample-campaign',
					'is_enabled' => true,
					'is_open' => true,
					'has_target' => true,
					'target_amount' => 1_500,
				],
			)
			->andReturn( $expected );

		$data = [
			'id' => '10',
			'title' => 'Sample Campaign',
			'slug' => 'sample-campaign',
			'is_enabled' => '1',
			'is_open' => true,
			'has_target' => 1,
			'target_amount' => '1500',
		];

		$input = $this->factory->from_array( $data );

		$this->assertSame( $expected, $input );
	}

	#[Test]
	public function from_array_fills_in_defaults_when_keys_missing(): void {

		$expected = new AdminWordPressCampaignInput(
			id: 7,
			title: '',
			slug: '',
			is_enabled: false,
			is_open: false,
			has_target: false,
			target_amount: 0,
		);

		$this->container
			->shouldReceive( 'make' )
			->once()
			->with(
				AbstractAdminWordPressCampaignInput::class,
				[
					'id' => 7,
					'title' => '',
					'slug' => '',
					'is_enabled' => false,
					'is_open' => false,
					'has_target' => false,
					'target_amount' => 0,
				],
			)
			->andReturn( $expected );

		$data = [ 'id' => '7' ];

		$input = $this->factory->from_array( $data );

		$this->assertSame( 7, $input->id );
		$this->assertSame( '', $input->title );
		$this->assertSame( '', $input->slug );
		$this->assertFalse( $input->is_enabled );
		$this->assertFalse( $input->is_open );
		$this->assertFalse( $input->has_target );
		$this->assertSame( 0, $input->target_amount );
	}

	#[Test]
	public function from_wp_post_delegates_to_mapper_and_from_array(): void {

		$array_data = [
			'id' => 42,
			'title' => 'Post Title',
			'slug' => 'post-title',
			'is_enabled' => true,
			'is_open' => false,
			'has_target' => true,
			'target_amount' => 500,
		];

		$expected = new AdminWordPressCampaignInput(
			id: 42,
			title: 'Post Title',
			slug: 'post-title',
			is_enabled: true,
			is_open: false,
			has_target: true,
			target_amount: 500,
		);

		$this->container
			->shouldReceive( 'make' )
			->once()
			->with( AbstractAdminWordPressCampaignInput::class, $array_data )
			->andReturn( $expected );

		$post = Mockery::mock( WP_Post::class );

		$this->mapper
			->shouldReceive( 'to_array_from_post' )
			->once()
			->with( $this->identicalTo( $post ) )
			->andReturn( $array_data );

		$input = $this->factory->from_wp_post( $post );

		$this->assertInstanceOf( AdminWordPressCampaignInput::class, $input );
		$this->assertSame( 42, $input->id );
		$this->assertSame( 'Post Title', $input->title );
		$this->assertSame( 'post-title', $input->slug );
		$this->assertTrue( $input->is_enabled );
		$this->assertFalse( $input->is_open );
		$this->assertTrue( $input->has_target );
		$this->assertSame( 500, $input->target_amount );
	}
}
