<?php
/**
 * WordPressCampaignDtoFactory class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns;

use Fundrik\Core\Support\TypeCaster;
use Fundrik\WordPress\Application\Campaigns\Input\AdminWordPressCampaignInput;
use Fundrik\WordPress\Domain\Campaigns\WordPressCampaign;

/**
 * Factory for creating WordPressCampaignDto objects from trusted data arrays.
 *
 * Assumes data has already been validated or is trusted (no checks performed).
 *
 * @since 1.0.0
 */
final readonly class WordPressCampaignDtoFactory {

	/**
	 * Create a WordPressCampaignDto from an associative array of data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Associative array with keys:
	 *                    - id            (int|string)
	 *                    - title         (string)
	 *                    - slug          (string)
	 *                    - is_enabled    (bool)
	 *                    - is_open       (bool)
	 *                    - has_target    (bool)
	 *                    - target_amount (int).
	 *
	 * @return WordPressCampaignDto A DTO representing the campaign data.
	 */
	public function from_array( array $data ): WordPressCampaignDto {

		return new WordPressCampaignDto(
			id: TypeCaster::to_id( $data['id'] ),
			title: TypeCaster::to_string( $data['title'] ),
			slug: TypeCaster::to_string( $data['slug'] ),
			is_enabled: TypeCaster::to_bool( $data['is_enabled'] ),
			is_open: TypeCaster::to_bool( $data['is_open'] ),
			has_target: TypeCaster::to_bool( $data['has_target'] ),
			target_amount: TypeCaster::to_int( $data['target_amount'] ),
		);
	}

	/**
	 * Create a WordPressCampaignDto from a WordPressCampaign.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressCampaign $campaign The Campaign entity.
	 *
	 * @return WordPressCampaignDto A DTO representing the campaign.
	 */
	public function from_campaign( WordPressCampaign $campaign ): WordPressCampaignDto {

		return new WordPressCampaignDto(
			id: $campaign->get_id(),
			title: $campaign->get_title(),
			is_enabled: $campaign->is_enabled(),
			is_open: $campaign->is_open(),
			has_target: $campaign->has_target(),
			target_amount: $campaign->get_target_amount(),
			slug: $campaign->get_slug(),
		);
	}

	/**
	 * Create a WordPressCampaignDto from an AdminWordPressCampaignInput object.
	 *
	 * @since 1.0.0
	 *
	 * @param AdminWordPressCampaignInput $input Validated input data from WordPress admin post form.
	 *
	 * @return WordPressCampaignDto A DTO representing the campaign input.
	 */
	public function from_input( AdminWordPressCampaignInput $input ): WordPressCampaignDto {

		return new WordPressCampaignDto(
			id: $input->id,
			title: $input->title,
			slug: $input->slug,
			is_enabled: $input->is_enabled,
			is_open: $input->is_open,
			has_target: $input->has_target,
			target_amount: $input->target_amount,
		);
	}
}
