<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Campaigns\Domain;

use Fundrik\Core\Application\Campaigns\CampaignDtoFactory;
use Fundrik\Core\Domain\Campaigns\CampaignFactory;
use Fundrik\WordPress\Campaigns\Application\Dto\WordPressCampaignDto;

/**
 * Factory for creating WordPressCampaign instances.
 *
 * It validates input data and ensures that the WordPressCampaign is correctly initialized.
 *
 * @since 1.0.0
 */
final readonly class WordPressCampaignFactory {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param CampaignFactory $core_factory Factory to create core Campaign entities.
	 * @param CampaignDtoFactory $core_dto_factory Factory to convert raw data into core Campaign DTOs.
	 */
	public function __construct(
		private CampaignFactory $core_factory,
		private CampaignDtoFactory $core_dto_factory,
	) {}

	/**
	 * Create a WordPressCampaign instance from a WordPressCampaignDto.
	 *
	 * This method converts the WordPress-specific DTO into a core DTO and uses
	 * the core factory to create the Campaign object, which is then wrapped
	 * in a WordPressCampaign with additional metadata.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressCampaignDto $dto The data transfer object containing campaign data.
	 *
	 * @return WordPressCampaign A new WordPressCampaign instance constructed from the DTO.
	 */
	public function create( WordPressCampaignDto $dto ): WordPressCampaign {

		$core_dto = $this->core_dto_factory->from_array(
			[
				'id' => $dto->id,
				'title' => $dto->title,
				'is_enabled' => $dto->is_enabled,
				'is_open' => $dto->is_open,
				'has_target' => $dto->has_target,
				'target_amount' => $dto->target_amount,
			],
		);

		$campaign = $this->core_factory->create( $core_dto );

		$slug = WordPressCampaignSlug::create( $dto->slug );

		return new WordPressCampaign( $campaign, $slug );
	}
}
