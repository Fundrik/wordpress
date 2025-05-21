<?php
/**
 * WordPressCampaignInputFactory class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input;

use Fundrik\Core\Support\TypeCaster;

/**
 * Factory for creating WordPressCampaignInput DTOs.
 *
 * @since 1.0.0
 */
final readonly class WordPressCampaignInputFactory {

	/**
	 * Creates a WordPressCampaignInput object from an associative array.
	 *
	 * This method performs type casting and fills in default values for missing keys.
	 * If the 'id' key is present and not empty, it returns an UpdateWordPressCampaignInput;
	 * otherwise, it returns a CreateWordPressCampaignInput.
	 *
	 * @param array<string, mixed> $data Raw input data, typically from WordPress post meta or form submission.
	 *
	 * @return WordPressCampaignInput Either a Create or Update input DTO, depending on presence of 'id'.
	 */
	public function from_array( array $data ): WordPressCampaignInput {

		$title         = TypeCaster::to_string( $data['title'] ?? '' );
		$slug          = TypeCaster::to_string( $data['slug'] ?? '' );
		$is_enabled    = TypeCaster::to_bool( $data['is_enabled'] ?? false );
		$is_open       = TypeCaster::to_bool( $data['is_open'] ?? false );
		$has_target    = TypeCaster::to_bool( $data['has_target'] ?? false );
		$target_amount = TypeCaster::to_int( $data['target_amount'] ?? 0 );

		if ( ! empty( $data['id'] ) ) {

			$id = TypeCaster::to_id( $data['id'] );

			return new UpdateWordPressCampaignInput(
				id: $id,
				title: $title,
				slug: $slug,
				is_enabled: $is_enabled,
				is_open: $is_open,
				has_target: $has_target,
				target_amount: $target_amount,
			);
		}

		return new CreateWordPressCampaignInput(
			title: $title,
			slug: $slug,
			is_enabled: $is_enabled,
			is_open: $is_open,
			has_target: $has_target,
			target_amount: $target_amount,
		);
	}
}
