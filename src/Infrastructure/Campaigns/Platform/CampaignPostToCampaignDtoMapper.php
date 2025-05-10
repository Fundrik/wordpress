<?php
/**
 * CampaignPostToCampaignDtoMapper class.
 *
 * @since 1.0.0
 */

namespace Fundrik\WordPress\Infrastructure\Campaigns\Platform;

use WP_Post;
use Fundrik\Core\Application\Campaigns\CampaignDtoFactory;
use Fundrik\Core\Domain\Campaigns\CampaignDto;

/**
 * Extracts relevant campaign data from the WordPress campaign post object
 * and converts it to CampaignDto.
 *
 * It also handles conversion of WordPress post metadata to campaign-specific values.
 *
 * @since 1.0.0
 */
final readonly class CampaignPostToCampaignDtoMapper {

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
			'is_enabled'       => true, // TODO.
			'is_open'          => $this->get_bool_meta( $post->ID, CampaignPostType::META_IS_OPEN ),
			'has_target'       => $this->get_bool_meta( $post->ID, CampaignPostType::META_HAS_TARGET ),
			'target_amount'    => $this->get_int_meta( $post->ID, CampaignPostType::META_TARGET_AMOUNT ),
			'collected_amount' => $this->get_int_meta( $post->ID, CampaignPostType::META_COLLECTED_AMOUNT ),
		];

		return $this->dto_factory->from_array( $data );
	}

	/**
	 * Retrieves a boolean value from post metadata.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $post_id The ID of the post.
	 * @param string $key     The metadata key.
	 *
	 * @return bool The boolean value of the metadata.
	 */
	private function get_bool_meta( int $post_id, string $key ): bool {

		return filter_var(
			get_post_meta( $post_id, $key, true ),
			FILTER_VALIDATE_BOOLEAN
		);
	}

	/**
	 * Retrieves an integer value from post metadata.
	 *
	 * @param int    $post_id The ID of the post.
	 * @param string $key     The metadata key.
	 *
	 * @return int The integer value of the metadata.
	 */
	private function get_int_meta( int $post_id, string $key ): int {

		return (int) get_post_meta( $post_id, $key, true );
	}
}
