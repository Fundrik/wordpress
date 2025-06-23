<?php

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
	 * @since 1.0.0
	 *
	 * @param array<string, int|string|bool> $data Raw partial input data from WordPress post meta and form submission.
	 *
	 * @return AdminWordPressCampaignPartialInput Input DTO with partial data from WordPress admin form.
	 */
	public function from_array( array $data ): AdminWordPressCampaignPartialInput {

		$id = TypeCaster::to_id( $data['id'] );

		$title = array_key_exists( 'title', $data ) ? TypeCaster::to_string( $data['title'] ) : null;
		$slug = array_key_exists( 'slug', $data ) ? TypeCaster::to_string( $data['slug'] ) : null;
		$is_open = TypeCaster::to_bool( $data['meta']['is_open'] );
		$has_target = TypeCaster::to_bool( $data['meta']['has_target'] );
		$target_amount = TypeCaster::to_int( $data['meta']['target_amount'] );

		return new AdminWordPressCampaignPartialInput(
			id: $id,
			title: $title,
			slug: $slug,
			is_open: $is_open,
			has_target: $has_target,
			target_amount: $target_amount,
		);
	}
}
