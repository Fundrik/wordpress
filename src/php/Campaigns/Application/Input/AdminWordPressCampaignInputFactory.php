<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Campaigns\Application\Input;

use Fundrik\Core\Support\ArrayExtractor;
use Fundrik\Core\Support\Exceptions\ArrayExtractionException;
use Fundrik\WordPress\Campaigns\Application\Input\Exceptions\InvalidAdminWordPressCampaignInputException;
use Fundrik\WordPress\Infrastructure\Campaigns\WordPressCampaignPostType;
use Fundrik\WordPress\Support\Exceptions\InvalidPostMetaValueException;
use Fundrik\WordPress\Support\Exceptions\MissingPostMetaException;
use Fundrik\WordPress\Support\PostMeta;
use RuntimeException;
use WP_Post;

/**
 * Factory for creating AdminWordPressCampaignInput DTOs.
 *
 * @since 1.0.0
 */
final readonly class AdminWordPressCampaignInputFactory {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPressCampaignPostType $post_type Provides meta keys and config for campaign post type mapping.
	 */
	public function __construct(
		private WordPressCampaignPostType $post_type,
	) {}

	/**
	 * Creates an AdminWordPressCampaignInput object from an associative array.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, scalar> $data The raw input data from WordPress admin edit page submission with keys:
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
	 * @return AdminWordPressCampaignInput The input DTO with data from WordPress form and post meta.
	 */
	public function from_array( array $data ): AdminWordPressCampaignInput {

		try {
			$parameters = $this->build_parameters_from_array( $data );
		} catch ( ArrayExtractionException $e ) {
			throw new InvalidAdminWordPressCampaignInputException(
				'Failed to build AdminWordPressCampaignInput: ' . $e->getMessage(),
				previous: $e,
			);
		}

		$input = fundrik()->make( AdminWordPressCampaignInput::class, $parameters );

		if ( ! $input instanceof AdminWordPressCampaignInput ) {

			throw new RuntimeException(
				sprintf(
					'Factory returned an instance of %s, but %s expected.',
					$input::class,
					AdminWordPressCampaignInput::class,
				),
			);
		}

		return $input;
	}

	/**
	 * Creates an AdminWordPressCampaignInput object from a WP_Post instance.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The WordPress post object representing the campaign.
	 *
	 * @return AdminWordPressCampaignInput The DTO with normalized and casted data.
	 */
	public function from_wp_post( WP_Post $post ): AdminWordPressCampaignInput {

		try {
			$data = $this->map_post_to_array( $post );
		} catch ( MissingPostMetaException | InvalidPostMetaValueException $e ) {

			throw new InvalidAdminWordPressCampaignInputException(
				// phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong
				'Invalid or missing post meta when building AdminWordPressCampaignInput from WP_Post: ' . $e->getMessage(),
				previous: $e,
			);
		}

		return $this->from_array( $data );
	}

	/**
	 * Builds a parameter array for creating a DTO from raw input data.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, scalar> $data The raw associative array.
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
	 * @phpstan-return array{
	 *     id: int,
	 *     title: string,
	 *     slug: string,
	 *     is_enabled: bool,
	 *     is_open: bool,
	 *     has_target: bool,
	 *     target_amount: int
	 * }
	 *
	 * @return array<string, scalar> Normalized parameters for DTO construction.
	 */
	private function build_parameters_from_array( array $data ): array {

		return [
			'id' => ArrayExtractor::extract_id_int_required( $data, 'id' ),
			'title' => ArrayExtractor::extract_string_required( $data, 'title' ),
			'slug' => ArrayExtractor::extract_string_required( $data, 'slug' ),
			'is_enabled' => ArrayExtractor::extract_bool_required( $data, 'is_enabled' ),
			'is_open' => ArrayExtractor::extract_bool_required( $data, 'is_open' ),
			'has_target' => ArrayExtractor::extract_bool_required( $data, 'has_target' ),
			'target_amount' => ArrayExtractor::extract_int_required( $data, 'target_amount' ),
		];
	}

	/**
	 * Maps a WP_Post to an associative array.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The WordPress post object.
	 *
	 * @return array<string, scalar> The mapped post data.
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
	private function map_post_to_array( WP_Post $post ): array {

		$post_type_class = $this->post_type::class;

		return [
			'id' => $post->ID,
			'title' => $post->post_title,
			'slug' => $post->post_name,
			'is_enabled' => $post->post_status === 'publish',
			'is_open' => PostMeta::get_bool_required( $post->ID, $post_type_class::META_IS_OPEN ),
			'has_target' => PostMeta::get_bool_required( $post->ID, $post_type_class::META_HAS_TARGET ),
			'target_amount' => PostMeta::get_int_required( $post->ID, $post_type_class::META_TARGET_AMOUNT ),
		];
	}
}
