<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests;

use Brain\Monkey;
use InvalidArgumentException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use ReflectionClass;
use ReflectionProperty;

abstract class FundrikTestCase extends PHPUnitTestCase {

	use MockeryPHPUnitIntegration;

	protected function setUp(): void {

		parent::setUp();

		Monkey\setUp();

		Monkey\Functions\stubEscapeFunctions();
		Monkey\Functions\stubTranslationFunctions();
	}

	protected function tearDown(): void {

		Monkey\tearDown();

		parent::tearDown();
	}

	protected function assert_has_attribute_instance_of(
		string $class_name,
		string $target_name,
		string $attribute_class,
		?array $expected_values = null,
		string $target_type = 'property',
	): void {

		if ( $target_type === 'property' ) {
			$reflection = new ReflectionProperty( $class_name, $target_name );
		} elseif ( $target_type === 'class' ) {
			$reflection = new ReflectionClass( $class_name );
		} else {
			throw new InvalidArgumentException( 'Invalid target type. Use "property" or "class".' );
		}

		$attributes = $reflection->getAttributes( $attribute_class );

		Assert::assertCount(
			1,
			$attributes,
			sprintf(
				'%s "%s" of class "%s" must have the "%s" attribute.',
				ucfirst( $target_type ),
				$target_name,
				$class_name,
				$attribute_class,
			),
		);

		$instance = $attributes[0]->newInstance();

		Assert::assertInstanceOf(
			$attribute_class,
			$instance,
			sprintf(
				'Expected instance of "%s" for %s "%s", got "%s"',
				$attribute_class,
				$target_type,
				$target_name,
				$instance::class,
			),
		);

		if ( ! $expected_values ) {
			return;
		}

		foreach ( $expected_values as $property => $expected ) {
			$actual = $instance->$property ?? null;

			Assert::assertSame(
				$expected,
				$actual,
				sprintf(
					'Expected value "%s" for property "%s" on attribute "%s", got "%s"',
					$expected,
					$property,
					$attribute_class,
					$actual,
				),
			);
		}
	}

	protected function assert_property_has_attribute(
		string $class_name,
		string $property_name,
		string $attribute_class,
		?array $expected_values = null,
	): void {

		$this->assert_has_attribute_instance_of(
			class_name: $class_name,
			target_name: $property_name,
			attribute_class: $attribute_class,
			expected_values: $expected_values,
			target_type: 'property',
		);
	}

	protected function assert_сlass_has_attribute(
		string $class_name,
		string $attribute_class,
		?array $expected_values = null,
	): void {

		$this->assert_has_attribute_instance_of(
			class_name: $class_name,
			target_name: $class_name,
			attribute_class: $attribute_class,
			expected_values: $expected_values,
			target_type: 'class',
		);
	}
}
