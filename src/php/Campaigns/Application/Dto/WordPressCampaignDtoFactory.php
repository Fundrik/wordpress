<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Campaigns\Application\Dto;

use Fundrik\Core\Support\ArrayExtractor;
use Fundrik\Core\Support\Exceptions\ArrayExtractionException;
use Fundrik\WordPress\Campaigns\Application\Dto\Exceptions\InvalidWordPressCampaignDtoException;
use Fundrik\WordPress\Campaigns\Application\Input\AdminWordPressCampaignInput;
use Fundrik\WordPress\Campaigns\Domain\WordPressCampaign;

/**
 * Factory for creating WordPressCampaignDto objects.
 *
 * @since 1.0.0
 */
final readonly class WordPressCampaignDtoFactory {

	/**
	 * Create a WordPressCampaignDto from an associative array of data.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string,scalar> $data Associative array with keys:
	 *        - id (int): The campaign ID.
	 *        - title (string): The campaign title.
	 *        - slug (string): The campaign slug.
	 *        - is_enabled (bool): Whether the campaign is enabled.
	 *        - is_open (bool): Whether the campaign is open.
	 *        - has_target (bool): Whether the campaign has a target amount.
	 *        - target_amount (int): The campaign target amount.
	 *
	 * @phpstan-param array{
	 *     id: int,
	 *     title: string,
	 *     slug: string,
	 *     is_enabled: bool,
	 *     is_open: bool,
	 *     has_target: bool,
	 *     target_amount: int
	 * } $data
	 *
	 * @return WordPressCampaignDto A DTO representing the campaign data.
	 */
	public function from_array( array $data ): WordPressCampaignDto {

		try {
			return new WordPressCampaignDto(
				id: ArrayExtractor::extract_id_int_required( $data, 'id' ),
				title: ArrayExtractor::extract_string_required( $data, 'title' ),
				slug: ArrayExtractor::extract_string_required( $data, 'slug' ),
				is_enabled: ArrayExtractor::extract_bool_required( $data, 'is_enabled' ),
				is_open: ArrayExtractor::extract_bool_required( $data, 'is_open' ),
				has_target: ArrayExtractor::extract_bool_required( $data, 'has_target' ),
				target_amount: ArrayExtractor::extract_int_required( $data, 'target_amount' ),
			);
		} catch ( ArrayExtractionException $e ) {
			throw new InvalidWordPressCampaignDtoException(
				'Failed to build WordPressCampaignDto: ' . $e->getMessage(),
				previous: $e,
			);
		}
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
