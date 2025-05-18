<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Campaigns\Platform;

use Fundrik\Core\Domain\EntityId;
use Fundrik\WordPress\Application\Campaigns\Interfaces\WordPressCampaignServiceInterface;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignDto;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces\WordPressCampaignPostMapperInterface;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignPostType;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\WordPressCampaignSyncListener;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use WP_Post;

#[CoversClass( WordPressCampaignSyncListener::class )]
#[UsesClass( WordPressCampaignPostType::class )]
class WordPressCampaignSyncListenerTest extends FundrikTestCase {

	private WordPressCampaignPostMapperInterface&MockInterface $mapper;
	private WordPressCampaignServiceInterface&MockInterface $service;

	private WordPressCampaignPostType $post_type;
	private WordPressCampaignSyncListener $listener;

	protected function setUp(): void {

		parent::setUp();

		$this->mapper  = Mockery::mock( WordPressCampaignPostMapperInterface::class );
		$this->service = Mockery::mock( WordPressCampaignServiceInterface::class );

		$this->post_type = new WordPressCampaignPostType();

		$this->listener = new WordPressCampaignSyncListener(
			$this->post_type,
			$this->mapper,
			$this->service,
		);
	}

	#[Test]
	public function register_hooks(): void {

		$this->listener->register();

		self::assertNotFalse(
			has_action(
				'wp_after_insert_post',
				$this->listener->sync( ... )
			)
		);

		self::assertNotFalse(
			has_action(
				'delete_post',
				$this->listener->delete( ... )
			)
		);
	}

	#[Test]
	public function sync_method_saves_entity(): void {

		$post            = Mockery::mock( 'WP_Post' );
		$post->post_type = $this->post_type->get_type();

		$dto = new WordPressCampaignDto(
			id            : 123,
			title         : 'Post Campaign',
			slug          : 'post-campaign',
			is_enabled    : true,
			is_open       : true,
			has_target    : true,
			target_amount : 1500,
		);

		$this->mapper
			->shouldReceive( 'from_wp_post' )
			->once()
			->with( $post )
			->andReturn( $dto );

		$this->service
			->shouldReceive( 'save_campaign' )
			->once()
			->with( $dto );

		$this->listener->sync( 123, $post );
	}

	#[Test]
	public function sync_method_does_not_save_entity_for_other_post_type(): void {

		$post            = Mockery::mock( WP_Post::class );
		$post->post_type = 'different_post_type';

		$this->mapper
			->shouldNotReceive( 'from_wp_post' );

		$this->service
			->shouldNotReceive( 'save_campaign' );

		$this->listener->sync( 123, $post );
	}

	#[Test]
	public function delete_method_deletes_entity(): void {

		$post            = Mockery::mock( WP_Post::class );
		$post->post_type = $this->post_type->get_type();
		$post_id         = 123;

		$entity_id = EntityId::create( $post_id );

		$this->service
			->shouldReceive( 'delete_campaign' )
			->once()
			->with(
				Mockery::on(
					function ( $arg ) use ( $entity_id ) {
						return $arg instanceof EntityId && $arg->value === $entity_id->value;
					}
				)
			);

		$this->listener->delete( $post_id, $post );
	}

	#[Test]
	public function delete_method_does_not_delete_for_other_post_type(): void {

		$post            = Mockery::mock( WP_Post::class );
		$post->post_type = 'different_post_type';

		$this->service
			->shouldNotReceive( 'delete_campaign' );

		$this->listener->delete( 123, $post );
	}
}
