<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces;

use WP_Post;

interface WordPressCampaignPostMapperInterface {

	/**
	 * Maps a WP_Post to a raw associative array.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The WordPress post object.
	 *
	 * @return array<string, mixed> An associative array of raw post data.
	 */
	public function to_array_from_post( WP_Post $post ): array;
}
