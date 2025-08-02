<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\WordPress\Events;

use Fundrik\WordPress\Infrastructure\WordPress\WordPressContext\WordPressContext;
use stdClass;
use WP_REST_Request;

/**
 * Represents the 'rest_pre_insert_fundrik_campaign' WordPress filter as a Fundrik event.
 *
 * @since 1.0.0
 *
 * @internal
 */
final class WordPressRestPreInsertCampaignFilterEvent {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * // phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong, SlevomatCodingStandard.Commenting.DocCommentSpacing.IncorrectLinesCountBetweenDifferentAnnotationsTypes
	 * @param stdClass $prepared_post An object representing a single post prepared for inserting or updating the database.
	 * @param WP_REST_Request $request Request object.
	 * @param WordPressContext $context The WordPress-specific plugin context.
	 *
	 * @phpstan-param WP_REST_Request<array<string, mixed>> $request
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	public function __construct(
		public stdClass $prepared_post,
		public readonly WP_REST_Request $request,
		public readonly WordPressContext $context,
	) {}
}
