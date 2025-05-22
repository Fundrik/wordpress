<?php
/**
 * AdminWordPressCampaignInputFactory class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input;

use Fundrik\Core\Support\TypeCaster;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces\WordPressCampaignPostMapperInterface;
use WP_Post;

/**
 * Factory for creating AdminWordPressCampaignInput DTOs.
 *
 * @since 1.0.0
 */
final readonly class AdminWordPressCampaignInputFactory {

	/**
	 * AdminWordPressCampaignInputFactory constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressCampaignPostMapperInterface $mapper Mapper to extract structured data from WP_Post.
	 */
	public function __construct(
		private WordPressCampaignPostMapperInterface $mapper
	) {}

	/**
	 * Creates an AdminWordPressCampaignInput object from an associative array.
	 *
	 * This method performs type casting and fills in default values for missing keys.
	 *
	 * @param array<string, mixed> $data Raw input data from WordPress post meta and form submission.
	 *
	 * @return AdminWordPressCampaignInput Input DTO with data from WordPress form and post meta.
	 */
	public function from_array( array $data ): AdminWordPressCampaignInput {

		$id            = TypeCaster::to_id( $data['id'] );
		$title         = TypeCaster::to_string( $data['title'] ?? '' );
		$slug          = TypeCaster::to_string( $data['slug'] ?? '' );
		$is_enabled    = TypeCaster::to_bool( $data['is_enabled'] ?? false );
		$is_open       = TypeCaster::to_bool( $data['is_open'] ?? false );
		$has_target    = TypeCaster::to_bool( $data['has_target'] ?? false );
		$target_amount = TypeCaster::to_int( $data['target_amount'] ?? 0 );

		return new AdminWordPressCampaignInput(
			id: $id,
			title: $title,
			slug: $slug,
			is_enabled: $is_enabled,
			is_open: $is_open,
			has_target: $has_target,
			target_amount: $target_amount,
		);
	}

	/**
	 * Creates an AdminWordPressCampaignInput object from a WP_Post instance.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post WordPress post object representing the campaign.
	 *
	 * @return AdminWordPressCampaignInput DTO with normalized and casted data.
	 */
	public function from_wp_post( WP_Post $post ): AdminWordPressCampaignInput {

		$data = $this->mapper->to_array_from_post( $post );

		return $this->from_array( $data );
	}
}
