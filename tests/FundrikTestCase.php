<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use ReflectionProperty;

abstract class FundrikTestCase extends PHPUnitTestCase {

	use MockeryPHPUnitIntegration;

	protected function setUp(): void {

		parent::setUp();

		Monkey\setUp();

		Functions\stubEscapeFunctions();
		Functions\stubTranslationFunctions();
	}

	protected function tearDown(): void {

		Monkey\tearDown();

		parent::tearDown();
	}

	protected function assertPropertyHasConstraint(
		string $class_name,
		string $property_name,
		string $constraint_class,
		?array $expected_values = null
	): void {

		$property   = new ReflectionProperty( $class_name, $property_name );
		$attributes = $property->getAttributes( $constraint_class );

		Assert::assertCount(
			1,
			$attributes,
			sprintf(
				'Property "%s" of class "%s" must have the "%s" constraint.',
				$property_name,
				$class_name,
				$constraint_class
			)
		);

		$instance = $attributes[0]->newInstance();

		Assert::assertInstanceOf(
			$constraint_class,
			$instance,
			sprintf(
				'Expected instance of "%s" for property "%s", got "%s"',
				$constraint_class,
				$property_name,
				get_class( $instance )
			)
		);

		if ( $expected_values ) {

			foreach ( $expected_values as $property => $expected ) {
				$actual = $instance->$property ?? null;

				Assert::assertSame(
					$expected,
					$actual,
					sprintf(
						'Expected value "%s" for property "%s" on constraint "%s", got "%s"',
						$expected,
						$property,
						$constraint_class,
						$actual
					)
				);
			}
		}
	}
}
