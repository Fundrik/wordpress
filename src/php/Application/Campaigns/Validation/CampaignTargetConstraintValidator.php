<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Application\Campaigns\Validation;

use Fundrik\WordPress\Application\Campaigns\Input\Abstracts\AbstractBaseAdminWordPressCampaignInput;
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
	 * @param mixed $input The value being validated (should be AbstractBaseAdminWordPressCampaignInput).
	 * @param Constraint $constraint The constraint instance (must be CampaignTargetConstraint).
	 *
	 * @since 1.0.0
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	public function validate( mixed $input, Constraint $constraint ): void {

		if ( ! $input instanceof AbstractBaseAdminWordPressCampaignInput ) {
			// @todo Escaping
			throw new UnexpectedValueException( $input, AbstractBaseAdminWordPressCampaignInput::class );
		}

		if ( ! $constraint instanceof CampaignTargetConstraint ) {
			// @todo Escaping
			throw new UnexpectedValueException( $constraint, $constraint::class );
		}

		$this->validateTargetAmount( $input, $constraint );
	}

	/**
	 * Checks target amount consistency with has_target flag.
	 *
	 * @since 1.0.0
	 *
	 * @param AbstractBaseAdminWordPressCampaignInput $input The campaign input data.
	 * @param CampaignTargetConstraint $constraint The validation constraint instance.
	 */
	private function validateTargetAmount(
		AbstractBaseAdminWordPressCampaignInput $input,
		CampaignTargetConstraint $constraint,
	): void {

		if ( $input->has_target ) {

			if ( $input->target_amount <= 0 ) {
				$this->context->buildViolation( $constraint->enabled_invalid )
					->atPath( 'target_amount' )
					->addViolation();

				return;
			}
		} elseif ( $input->target_amount !== 0 ) {
			$this->context->buildViolation( $constraint->disabled_invalid )
				->atPath( 'target_amount' )
				->addViolation();
		}
	}
}
