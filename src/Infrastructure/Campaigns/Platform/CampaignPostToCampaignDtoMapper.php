<?php
/**
 * CampaignPostToCampaignDtoMapper class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Campaigns\Platform;

use WP_Post;
use Fundrik\Core\Application\Campaigns\CampaignDtoFactory;
use Fundrik\Core\Domain\Campaigns\CampaignDto;
use Fundrik\WordPress\Infrastructure\Platform\Interfaces\PostToEntityDtoMapperInterface;
use Fundrik\WordPress\Support\PostMetaHelper;

/**
 * Extracts relevant campaign data from the WordPress campaign post object
 * and converts it to CampaignDto.
 *
 * It also handles conversion of WordPress post metadata to campaign-specific values.
 *
 * @since 1.0.0
 */
class CampaignPostToCampaignDtoMapper implements PostToEntityDtoMapperInterface {

	/**
	 * PostToCampaignDtoMapper constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param CampaignDtoFactory $dto_factory A factory to create CampaignDto objects.
	 */
	public function __construct(
		private CampaignDtoFactory $dto_factory
	) {
	}

	/**
	 * Maps data from a WP_Post to a CampaignDto.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The WordPress post object.
	 *
	 * @return CampaignDto The corresponding CampaignDto object.
	 */
	public function from_wp_post( WP_Post $post ): CampaignDto {

		$data = [
			'id'               => $post->ID,
			'title'            => $post->post_title,
			'slug'             => $post->post_name,
			'is_enabled'       => 'publish' === $post->post_status,
			'is_open'          => PostMetaHelper::get_bool( $post->ID, CampaignPostType::META_IS_OPEN ),
			'has_target'       => PostMetaHelper::get_bool( $post->ID, CampaignPostType::META_HAS_TARGET ),
			'target_amount'    => PostMetaHelper::get_int( $post->ID, CampaignPostType::META_TARGET_AMOUNT ),
			'collected_amount' => PostMetaHelper::get_int( $post->ID, CampaignPostType::META_COLLECTED_AMOUNT ),
		];

		return $this->dto_factory->from_array( $data );
	}
}
