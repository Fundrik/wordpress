<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input;

use Fundrik\Core\Support\TypeCaster;
use Fundrik\Core\Support\TypedArrayExtractor;
use Fundrik\WordPress\Application\Campaigns\Input\Abstracts\AbstractAdminWordPressCampaignPartialInput;
use InvalidArgumentException;

/**
 * Factory for creating AbstractAdminWordPressCampaignPartialInput DTOs.
 *
 * @since 1.0.0
 */
final readonly class AdminWordPressCampaignPartialInputFactory {

	// phpcs:disable SlevomatCodingStandard.Files.LineLength.LineTooLong
	/**
	 * Creates an AbstractAdminWordPressCampaignPartialInput object from an associative array.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, int|string|bool|array<string, int|string|bool>> $data Raw partial input data from WordPress post meta and form submission.
	 *
	 * @return AbstractAdminWordPressCampaignPartialInput Input DTO with partial data from WordPress admin form.
	 */
	public function from_array( array $data ): AbstractAdminWordPressCampaignPartialInput {

		if ( ! array_key_exists( 'id', $data ) ) {
			throw new InvalidArgumentException( 'Missing required key "id" in input data.' );
		}

		return fundrik()->make(
			AdminWordPressCampaignPartialInput::class,
			$this->build_parameters_from_array( $data ),
		);
	}
	// phpcs:enable SlevomatCodingStandard.Files.LineLength.LineTooLong

	// phpcs:disable SlevomatCodingStandard.Files.LineLength.LineTooLong
	/**
	 * Builds a parameter array for creating a DTO from raw input data.
	 *
	 * This method extracts and casts expected fields from the input array,
	 * including meta fields nested under the 'meta' key. It returns a
	 * normalized array suitable for use with a DI container's `make()` call.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, int|string|bool|array<string, int|string|bool>> $data Raw associative array with possible nested meta fields.
	 *
	 * @return array<string, int|string|bool|null> Normalized parameters for DTO construction.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.Files.LineLength.LineTooLong
	 */
	private function build_parameters_from_array( array $data ): array {

		$meta = TypedArrayExtractor::extract_array_or_empty( $data, 'meta' );

		return [
			'id' => TypeCaster::to_id( $data['id'] ),
			'title' => TypedArrayExtractor::extract_string_or_null( $data, 'title' ),
			'slug' => TypedArrayExtractor::extract_string_or_null( $data, 'slug' ),
			'is_open' => TypedArrayExtractor::extract_bool_or_false( $meta, 'is_open' ),
			'has_target' => TypedArrayExtractor::extract_bool_or_false( $meta, 'has_target' ),
			'target_amount' => TypedArrayExtractor::extract_int_or_zero( $meta, 'target_amount' ),
		];
	}
	// phpcs:enable SlevomatCodingStandard.Files.LineLength.LineTooLong
}
