<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Validation;

use Fundrik\WordPress\Application\Campaigns\Input\AbstractAdminWordPressCampaignInput;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Validator for the CampaignTargetConstraint.
 *
 * Validates that the campaign's target amount is consistent with
 * the has_target flag in the input DTO.
 *
 * - If targeting is enabled (`has_target` is true), the target amount must be > 0.
 * - If targeting is disabled (`has_target` is false), the target amount must be 0.
 *
 * Supports validation of both full and partial admin WordPress campaign input DTOs.
 *
 * @since 1.0.0
 */
final class CampaignTargetConstraintValidator extends ConstraintValidator {

	/**
	 * Validates the input against the CampaignTargetConstraint.
	 *
	 * @param mixed      $input      The value being validated (should be AdminWordPressCampaignInput or AdminWordPressCampaignPartialInput).
	 * @param Constraint $constraint The constraint instance (must be CampaignTargetConstraint).
	 *
	 * @throws UnexpectedValueException If the input or constraint are of unexpected types.
	 */
	public function validate( mixed $input, Constraint $constraint ): void {

		if ( ! $input instanceof AbstractAdminWordPressCampaignInput ) {
			// @todo Escaping
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new UnexpectedValueException( $input, $input::class );
		}

		if ( ! $constraint instanceof CampaignTargetConstraint ) {
			// @todo Escaping
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new UnexpectedValueException( $constraint, $constraint::class );
		}

		if ( $input->has_target && $input->target_amount <= 0 ) {

			$this->context->buildViolation( $constraint->enabled_invalid )
				->atPath( 'target_amount' )
				->addViolation();
		}

		if ( ! $input->has_target && 0 !== $input->target_amount ) {

			$this->context->buildViolation( $constraint->disabled_invalid )
				->atPath( 'target_amount' )
				->addViolation();
		}
	}
}
