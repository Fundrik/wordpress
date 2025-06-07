<?php
/**
 * WordPressCampaignSlug value object.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Domain\Campaigns;

use InvalidArgumentException;

/**
 * Represents a slug specific to a WordPress campaign.
 *
 * Ensures the slug is non-empty and properly trimmed.
 *
 * @since 1.0.0
 */
final readonly class WordPressCampaignSlug {

	/**
	 * WordPressCampaignSlug constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value The campaign slug.
	 *
	 * @throws InvalidArgumentException If the slug is empty or whitespace.
	 */
	public function __construct(
		public string $value,
	) {
		if ( '' === trim( $value ) ) {
			throw new InvalidArgumentException( 'Campaign slug cannot be empty or whitespace.' );
		}
	}
}
