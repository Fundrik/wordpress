<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns;

/**
 * Represents the full state of a campaign entity within the WordPress implementation.
 *
 * @since 1.0.0
 */
final readonly class WordPressCampaignDto {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id The campaign ID.
	 * @param string $title The campaign title.
	 * @param string $slug The campaign slug.
	 * @param bool $is_enabled Whether the campaign is enabled.
	 * @param bool $is_open Whether the campaign is open.
	 * @param bool $has_target Whether the campaign has a target amount.
	 * @param int $target_amount The campaign target amount.
	 */
	public function __construct(
		public int $id,
		public string $title,
		public string $slug,
		public bool $is_enabled,
		public bool $is_open,
		public bool $has_target,
		public int $target_amount,
	) {
	}

	/**
	 * Converts the DTO properties to an associative array.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, scalar> The DTO data as a key-value array with keys:
	 *         - id (int): The campaign ID.
	 *         - title (string): The campaign title.
	 *         - slug (string): The campaign slug.
	 *         - is_enabled (bool): Whether the campaign is enabled.
	 *         - is_open (bool): Whether the campaign is open.
	 *         - has_target (bool): Whether the campaign has a target amount.
	 *         - target_amount (int): The campaign target amount.
	 *
	 * @phpstan-return array{
	 *     id: int,
	 *     title: string,
	 *     slug: string,
	 *     is_enabled: bool,
	 *     is_open: bool,
	 *     has_target: bool,
	 *     target_amount: int
	 * }
	 */
	public function to_array(): array {

		return [
			'id' => $this->id,
			'title' => $this->title,
			'slug' => $this->slug,
			'is_enabled' => $this->is_enabled,
			'is_open' => $this->is_open,
			'has_target' => $this->has_target,
			'target_amount' => $this->target_amount,
		];
	}
}
