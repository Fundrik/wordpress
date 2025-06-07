<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Application\Campaigns\Input;

use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignInput;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignInputFactory;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces\WordPressCampaignPostMapperInterface;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use WP_Post;

#[CoversClass( AdminWordPressCampaignInputFactory::class )]
#[UsesClass( AdminWordPressCampaignInput::class )]
final class AdminWordPressCampaignInputFactoryTest extends FundrikTestCase {

	private AdminWordPressCampaignInputFactory $factory;
	private WordPressCampaignPostMapperInterface&MockInterface $mapper;

	protected function setUp(): void {

		parent::setUp();

		$this->mapper  = Mockery::mock( WordPressCampaignPostMapperInterface::class );
		$this->factory = new AdminWordPressCampaignInputFactory( $this->mapper );
	}

	#[Test]
	public function from_array_creates_input_correctly(): void {

		$data = [
			'id'            => '10',
			'title'         => 'Sample Campaign',
			'slug'          => 'sample-campaign',
			'is_enabled'    => '1',
			'is_open'       => true,
			'has_target'    => true,
			'target_amount' => '1500',
		];

		$input = $this->factory->from_array( $data );

		$this->assertInstanceOf( AdminWordPressCampaignInput::class, $input );
		$this->assertSame( 10, $input->id );
		$this->assertSame( 'Sample Campaign', $input->title );
		$this->assertSame( 'sample-campaign', $input->slug );
		$this->assertTrue( $input->is_enabled );
		$this->assertTrue( $input->is_open );
		$this->assertTrue( $input->has_target );
		$this->assertSame( 1500, $input->target_amount );
	}

	#[Test]
	public function from_array_fills_in_defaults_when_keys_missing(): void {

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

		$post = Mockery::mock( WP_Post::class );

		$this->mapper
			->shouldReceive( 'to_array_from_post' )
			->once()
			->with( $post )
			->andReturn(
				[
					'id'            => 42,
					'title'         => 'Post Title',
					'slug'          => 'post-title',
					'is_enabled'    => true,
					'is_open'       => false,
					'has_target'    => true,
					'target_amount' => 500,
				]
			);

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
