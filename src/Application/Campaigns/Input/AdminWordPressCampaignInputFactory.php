<?php
/**
 * AdminWordPressCampaignInputFactory class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input;

use Fundrik\Core\Support\TypeCaster;

/**
 * Factory for creating AdminWordPressCampaignInput DTOs.
 *
 * @since 1.0.0
 */
final readonly class AdminWordPressCampaignInputFactory {

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
}
