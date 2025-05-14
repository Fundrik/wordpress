<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Platform;

use Fundrik\WordPress\Infrastructure\Platform\PostSyncListener;
use Fundrik\WordPress\Infrastructure\Platform\Interfaces\PostToEntityDtoMapperInterface;
use Fundrik\Core\Application\Interfaces\EntityServiceInterface;
use Fundrik\Core\Domain\EntityId;
use Fundrik\Core\Domain\Interfaces\EntityDto;
use Fundrik\WordPress\Tests\FundrikTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use WP_Post;

#[CoversClass( PostSyncListener::class )]
class PostSyncListenerTest extends FundrikTestCase {

	private PostToEntityDtoMapperInterface&MockInterface $mapper;
	private EntityServiceInterface&MockInterface $service;
	private PostSyncListener $listener;

	protected function setUp(): void {

		parent::setUp();

		$this->mapper  = Mockery::mock( PostToEntityDtoMapperInterface::class );
		$this->service = Mockery::mock( EntityServiceInterface::class );

		$this->listener = new PostSyncListener(
			'custom_post_type',
			$this->mapper,
			$this->service
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
		$post->post_type = 'custom_post_type';

		$dto = Mockery::mock( EntityDto::class );

		$this->mapper
			->shouldReceive( 'from_wp_post' )
			->once()
			->with( $post )
			->andReturn( $dto );

		$this->service
			->shouldReceive( 'save' )
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
			->shouldNotReceive( 'save' );

		$this->listener->sync( 123, $post );
	}

	#[Test]
	public function delete_method_deletes_entity(): void {

		$post            = Mockery::mock( WP_Post::class );
		$post->post_type = 'custom_post_type';
		$post_id         = 123;

		$entity_id = EntityId::create( $post_id );

		$this->service
			->shouldReceive( 'delete' )
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
			->shouldNotReceive( 'delete' );

		$this->listener->delete( 123, $post );
	}
}
