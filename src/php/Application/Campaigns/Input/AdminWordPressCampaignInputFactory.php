<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Input;

use Fundrik\Core\Support\TypeCaster;
use Fundrik\Core\Support\TypedArrayExtractor;
use Fundrik\WordPress\Application\Campaigns\Input\Abstracts\AbstractAdminWordPressCampaignInput;
use Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces\WordPressCampaignPostMapperInterface;
use InvalidArgumentException;
use RuntimeException;
use WP_Post;

/**
 * Factory for creating AbstractAdminWordPressCampaignInput DTOs.
 *
 * @since 1.0.0
 */
final readonly class AdminWordPressCampaignInputFactory {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressCampaignPostMapperInterface $mapper Mapper to extract structured data from WP_Post.
	 */
	public function __construct(
		private WordPressCampaignPostMapperInterface $mapper,
	) {}

	/**
	 * Creates an AbstractAdminWordPressCampaignInput object from an associative array.
	 *
	 * This method performs type casting and fills in default values for missing keys.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, scalar> $data Raw input data from WordPress post meta and form submission.
	 *
	 * @return AbstractAdminWordPressCampaignInput Input DTO with data from WordPress form and post meta.
	 */
	public function from_array( array $data ): AbstractAdminWordPressCampaignInput {

		if ( ! array_key_exists( 'id', $data ) ) {
			throw new InvalidArgumentException( 'Missing required key "id" in input data.' );
		}

		$input = fundrik()->make(
			AbstractAdminWordPressCampaignInput::class,
			$this->build_parameters_from_array( $data ),
		);

		if ( ! $input instanceof AbstractAdminWordPressCampaignInput ) {

			throw new RuntimeException(
				sprintf(
					'Factory returned an instance of %s, but %s expected.',
					$input::class,
					AbstractAdminWordPressCampaignInput::class,
				),
			);
		}

		return $input;
	}

	/**
	 * Creates an AbstractAdminWordPressCampaignInput object from a WP_Post instance.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post WordPress post object representing the campaign.
	 *
	 * @return AbstractAdminWordPressCampaignInput DTO with normalized and casted data.
	 */
	public function from_wp_post( WP_Post $post ): AbstractAdminWordPressCampaignInput {

		$data = $this->mapper->to_array_from_post( $post );

		return $this->from_array( $data );
	}

	/**
	 * Builds a parameter array for creating a DTO from raw input data.
	 *
	 * This method extracts and casts expected fields from the input array,
	 * including meta fields nested under the 'meta' key. It returns a
	 * normalized array suitable for use with a DI container's `make()` call.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, scalar> $data Raw associative array with possible nested meta fields.
	 *
	 * @return array<string, scalar> Normalized parameters for DTO construction.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.Files.LineLength.LineTooLong
	 */
	private function build_parameters_from_array( array $data ): array {

		return [
			'id' => TypeCaster::to_id( $data['id'] ),
			'title' => TypedArrayExtractor::extract_string_or_empty( $data, 'title' ),
			'slug' => TypedArrayExtractor::extract_string_or_empty( $data, 'slug' ),
			'is_enabled' => TypedArrayExtractor::extract_bool_or_false( $data, 'is_enabled' ),
			'is_open' => TypedArrayExtractor::extract_bool_or_false( $data, 'is_open' ),
			'has_target' => TypedArrayExtractor::extract_bool_or_false( $data, 'has_target' ),
			'target_amount' => TypedArrayExtractor::extract_int_or_zero( $data, 'target_amount' ),
		];
	}
}
