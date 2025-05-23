<?php
/**
 * CampaignPostToCampaignDtoMapper class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Campaigns\Platform;

use Fundrik\WordPress\Application\Campaigns\Input\WordPressCampaignInput;
use Fundrik\WordPress\Application\Campaigns\WordPressCampaignDtoFactory;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces\WordPressCampaignPostMapperInterface;
use WP_Post;
use Fundrik\WordPress\Support\PostMetaHelper;

/**
 * Maps data from a WordPress campaign post to a WordPressCampaignDto.
 *
 * Extracts relevant fields from the WP_Post object and associated post metadata,
 * converting them into a structured DTO.
 *
 * @since 1.0.0
 */
final readonly class WordPressCampaignPostMapper implements WordPressCampaignPostMapperInterface {

	/**
	 * WordPressCampaignPostMapper constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressCampaignDtoFactory $dto_factory A factory to create WordPressCampaignDto objects.
	 */
	public function __construct(
		private WordPressCampaignDtoFactory $dto_factory
	) {
	}

	/**
	 * Maps a WP_Post to a raw associative array.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The WordPress post object.
	 *
	 * @return array<string, mixed> An associative array of raw post data.
	 */
	public function to_array_from_post( WP_Post $post ): array {

		$post_type_class = fundrik()->get( WordPressCampaignPostType::class )::class;

		return [
			'id'            => $post->ID,
			'title'         => $post->post_title,
			'slug'          => $post->post_name,
			'is_enabled'    => 'publish' === $post->post_status,
			'is_open'       => PostMetaHelper::get_bool( $post->ID, $post_type_class::META_IS_OPEN ),
			'has_target'    => PostMetaHelper::get_bool( $post->ID, $post_type_class::META_HAS_TARGET ),
			'target_amount' => PostMetaHelper::get_int( $post->ID, $post_type_class::META_TARGET_AMOUNT ),
		];
	}
}
