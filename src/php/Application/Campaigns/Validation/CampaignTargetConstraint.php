<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Validation;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Custom validation constraint to ensure that target_amount is logically consistent
 * with has_target.
 *
 * - If has_target is true, then target_amount must be greater than zero.
 * - If has_target is false, then target_amount must be exactly zero.
 *
 * This is a class-level constraint intended to complement field-level assertions.
 *
 * @since 1.0.0
 */
#[Attribute]
final class CampaignTargetConstraint extends Constraint {

	/**
	 * Error message when target amount is invalid while targeting is enabled.
	 *
	 * Applied when $has_target is true and $target_amount <= 0.
	 *
	 * @since 1.0.0
	 */
	public string $enabled_invalid;

	/**
	 * Error message when target amount is invalid while targeting is disabled.
	 *
	 * Applied when $has_target is false and $target_amount > 0.
	 *
	 * @since 1.0.0
	 */
	public string $disabled_invalid;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed>|null $options Optional options for the constraint.
	 * @param array<int, string>|null $groups Optional validation groups.
	 * @param object|null $payload Optional payload data.
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	public function __construct(
		?array $options = null,
		?array $groups = null,
		?object $payload = null,
	) {

		$this->enabled_invalid = __( 'Target amount must be greater than zero when targeting is enabled.', 'fundrik' );
		$this->disabled_invalid = __( 'Target amount must be zero when targeting is disabled.', 'fundrik' );

		parent::__construct( $options, $groups, $payload );
	}

	/**
	 * Defines the target of the constraint.
	 *
	 * This constraint should be applied at the class level.
	 *
	 * @since 1.0.0
	 *
	 * @return self::CLASS_CONSTRAINT The constraint target type.
	 */
	public function getTargets(): string {

		return self::CLASS_CONSTRAINT;
	}
}
