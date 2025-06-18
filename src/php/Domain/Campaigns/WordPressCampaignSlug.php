<?php

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
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value The campaign slug.
	 */
	private function __construct(
		public string $value,
	) {}

	/**
	 * Factory method to create a WordPressCampaignSlug instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value The campaign slug.
	 *
	 * @return self New instance of WordPressCampaignSlug.
	 *
	 * @throws InvalidArgumentException If the slug is empty or whitespace.
	 */
	public static function create( string $value ): self {

		$value = trim( $value );

		if ( '' === $value ) {
			throw new InvalidArgumentException( 'Campaign slug cannot be empty or whitespace.' );
		}

		return new self( $value );
	}
}
