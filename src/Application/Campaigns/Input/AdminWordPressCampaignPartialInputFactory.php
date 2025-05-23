<?php
/**
 * AdminWordPressCampaignPartialInputFactory class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input;

use Fundrik\Core\Support\TypeCaster;

/**
 * Factory for creating AdminWordPressCampaignPartialInput DTOs.
 *
 * @since 1.0.0
 */
final readonly class AdminWordPressCampaignPartialInputFactory {

	/**
	 * Creates an AdminWordPressCampaignPartialInput object from an associative array.
	 *
	 * Fields not present in the array will be set to null, except for ID which is required.
	 *
	 * @param array<string, mixed> $data Raw partial input data from WordPress post meta and form submission.
	 *
	 * @return AdminWordPressCampaignPartialInput Input DTO with partial data from WordPress admin form.
	 */
	public function from_array( array $data ): AdminWordPressCampaignPartialInput {

		$id = TypeCaster::to_id( $data['id'] );

		$title         = array_key_exists( 'title', $data ) ? TypeCaster::to_string( $data['title'] ) : null;
		$slug          = array_key_exists( 'slug', $data ) ? TypeCaster::to_string( $data['slug'] ) : null;
		$is_enabled    = array_key_exists( 'is_enabled', $data ) ? TypeCaster::to_bool( $data['is_enabled'] ) : null;
		$is_open       = array_key_exists( 'is_open', $data ) ? TypeCaster::to_bool( $data['is_open'] ) : null;
		$has_target    = array_key_exists( 'has_target', $data ) ? TypeCaster::to_bool( $data['has_target'] ) : null;
		$target_amount = array_key_exists( 'target_amount', $data ) ? TypeCaster::to_int( $data['target_amount'] ) : null;

		return new AdminWordPressCampaignPartialInput(
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
