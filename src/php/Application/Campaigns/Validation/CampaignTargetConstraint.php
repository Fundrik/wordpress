<?php
/**
 * CampaignTargetConstraint class.
 *
 * @since 1.0.0
 */

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Validation;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Custom validation constraint to ensure the campaign's target amount
 * is consistent with whether targeting is enabled or disabled.
 *
 * When targeting is enabled, the target amount must be greater than zero.
 * When targeting is disabled, the target amount must be zero.
 *
 * This constraint is designed to be applied at the class level.
 *
 * @since 1.0.0
 */
#[Attribute]
final class CampaignTargetConstraint extends Constraint {

	/**
	 * Error message when target amount is invalid while targeting is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public string $enabled_invalid;

	/**
	 * Error message when target amount is invalid while targeting is disabled.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public string $disabled_invalid;

	/**
	 * CampaignTargetConstraint constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed      $options Optional options for the constraint.
	 * @param array|null $groups  Optional validation groups.
	 * @param mixed      $payload Optional payload data.
	 */
	public function __construct( mixed $options = null, ?array $groups = null, mixed $payload = null ) {

		$this->enabled_invalid  = __( 'Target amount must be greater than zero when targeting is enabled.', 'fundrik' );
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
	 * @return string The constraint target type.
	 */
	public function getTargets(): string {

		return self::CLASS_CONSTRAINT;
	}
}
