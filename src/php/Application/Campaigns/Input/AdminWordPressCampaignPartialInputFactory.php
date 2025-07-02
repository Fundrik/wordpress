<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input;

use Fundrik\Core\Support\ArrayExtractor;
use Fundrik\Core\Support\Exceptions\ArrayExtractionException;
use Fundrik\WordPress\Application\Campaigns\Input\Exceptions\InvalidAdminWordPressCampaignPartialInputException;
use RuntimeException;

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
	 * phpcs:disable SlevomatCodingStandard.Files.LineLength.LineTooLong
	 *
	 * @param array<string, scalar|array<string, scalar>> $data The raw input data from WordPress admin edit page submission with keys:
	 *        - id (int): The campaign ID.
	 *        - title (string): The campaign title (optional).
	 *        - slug (string): The campaign slug (optional).
	 *        - meta (array): The array of meta values, including:
	 *          - is_open (bool): Whether the campaign is open.
	 *          - has_target (bool): Whether the campaign has a target amount.
	 *          - target_amount (int): The campaign target amount.
	 *
	 * phpcs:enable
	 *
	 * @phpstan-param array{
	 *     id: int,
	 *     title?: string,
	 *     slug?: string,
	 *     meta: array{
	 *         is_open: bool,
	 *         has_target: bool,
	 *         target_amount: int
	 *     }
	 * } $data
	 *
	 * @return AdminWordPressCampaignPartialInput The input DTO with partial data from WordPress admin form.
	 */
	public function from_array( array $data ): AdminWordPressCampaignPartialInput {

		try {
			$parameters = $this->build_parameters_from_array( $data );
		} catch ( ArrayExtractionException $e ) {
			throw new InvalidAdminWordPressCampaignPartialInputException(
				'Failed to build AdminWordPressCampaignPartialInput: ' . $e->getMessage(),
				previous: $e,
			);
		}

		$input = fundrik()->make( AdminWordPressCampaignPartialInput::class, $parameters );

		if ( ! $input instanceof AdminWordPressCampaignPartialInput ) {

			throw new RuntimeException(
				sprintf(
					'Factory returned an instance of %s, but %s expected.',
					$input::class,
					AdminWordPressCampaignPartialInput::class,
				),
			);
		}

		return $input;
	}

	/**
	 * Builds a parameter array for creating a DTO from raw input data.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, scalar|array<string, scalar>> $data The raw associative array.
	 *
	 * @phpstan-param array{
	 *     id: int,
	 *     title?: string,
	 *     slug?: string,
	 *     meta: array{
	 *         is_open: bool,
	 *         has_target: bool,
	 *         target_amount: int
	 *     }
	 * } $data
	 *
	 * @phpstan-return array{
	 *     id: int,
	 *     title: string|null,
	 *     slug: string|null,
	 *     is_open: bool,
	 *     has_target: bool,
	 *     target_amount: int
	 * }
	 *
	 * @return array<string, scalar|null> Normalized parameters for DTO construction.
	 */
	private function build_parameters_from_array( array $data ): array {

		$meta = ArrayExtractor::extract_array_required( $data, 'meta' );

		return [
			'id' => ArrayExtractor::extract_id_int_required( $data, 'id' ),
			'title' => ArrayExtractor::extract_string_optional( $data, 'title' ),
			'slug' => ArrayExtractor::extract_string_optional( $data, 'slug' ),
			'is_open' => ArrayExtractor::extract_bool_required( $meta, 'is_open' ),
			'has_target' => ArrayExtractor::extract_bool_required( $meta, 'has_target' ),
			'target_amount' => ArrayExtractor::extract_int_required( $meta, 'target_amount' ),
		];
	}
}
