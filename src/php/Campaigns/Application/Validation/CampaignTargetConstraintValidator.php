<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Campaigns\Application\Validation;

use Fundrik\WordPress\Campaigns\Application\Input\AdminWordPressCampaignInput;
use Fundrik\WordPress\Campaigns\Application\Input\AdminWordPressCampaignPartialInput;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Validator for the CampaignTargetConstraint.
 *
 * Validates that the campaign's target amount is consistent with
 * the has_target flag in the input DTO.
 *
 * - If has_target is true, target_amount must be greater than zero.
 * - If has_target is false, target_amount must be exactly zero.
 *
 * Supports validation of both full and partial admin WordPress campaign input DTOs.
 *
 * @since 1.0.0
 */
final class CampaignTargetConstraintValidator extends ConstraintValidator {

	/**
	 * Validates the input against the CampaignTargetConstraint.
	 *
	 * @param mixed $input The object being validated.
	 *                     Must be an instance of AdminWordPressCampaignInput or AdminWordPressCampaignPartialInput.
	 * @param Constraint $constraint The constraint instance (must be CampaignTargetConstraint).
	 *
	 * @since 1.0.0
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 */
	public function validate( mixed $input, Constraint $constraint ): void {

		if (
			! $input instanceof AdminWordPressCampaignInput
			&& ! $input instanceof AdminWordPressCampaignPartialInput
		) {
			throw new UnexpectedValueException(
				$input,
				AdminWordPressCampaignInput::class . ' or ' . AdminWordPressCampaignPartialInput::class,
			);
		}

		if ( ! $constraint instanceof CampaignTargetConstraint ) {
			throw new UnexpectedValueException( $constraint, $constraint::class );
		}

		$this->validateTargetAmount( $input, $constraint );
	}

	/**
	 * Checks target_amount consistency with has_target.
	 *
	 * @since 1.0.0
	 *
	 * @param AdminWordPressCampaignInput|AdminWordPressCampaignPartialInput $input The campaign input data.
	 * @param CampaignTargetConstraint $constraint The validation constraint instance.
	 */
	private function validateTargetAmount(
		AdminWordPressCampaignInput|AdminWordPressCampaignPartialInput $input,
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
