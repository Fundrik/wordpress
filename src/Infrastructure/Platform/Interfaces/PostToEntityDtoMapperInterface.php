<?php
/**
 * Interface for converting a WP_Post to a specific entity DTO.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Platform\Interfaces;

use Fundrik\Core\Domain\Interfaces\EntityDto;
use WP_Post;

interface PostToEntityDtoMapperInterface {

	/**
	 * Converts a WP_Post to a specific Dto.
	 *
	 * @param WP_Post $post The WordPress post.
	 *
	 * @return object Entity DTO.
	 */
	public function from_wp_post( WP_Post $post ): EntityDto;
}
