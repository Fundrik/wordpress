<?php
/**
 * Defines an interface for mapping a WordPress campaign post (WP_Post)
 * to a structured WordPressCampaignDto.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Campaigns\Platform\Interfaces;

use Fundrik\WordPress\Application\Campaigns\WordPressCampaignDto;
use WP_Post;

interface WordPressCampaignPostMapperInterface {

	/**
	 * Maps a WordPress campaign post to a WordPressCampaignDto.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The WordPress post object.
	 *
	 * @return WordPressCampaignDto The corresponding WordPressCampaignDto object.
	 */
	public function from_wp_post( WP_Post $post ): WordPressCampaignDto;
}
