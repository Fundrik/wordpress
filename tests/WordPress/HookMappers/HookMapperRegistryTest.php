<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\WordPress\HookMappers;

use Fundrik\WordPress\Infrastructure\WordPress\HookMappers\HookMapperRegistry;
use Fundrik\WordPress\Infrastructure\WordPress\HookMappers\Mappers\AllowedBlockTypesAllMapper;
use Fundrik\WordPress\Infrastructure\WordPress\HookMappers\Mappers\DeletePostMapper;
use Fundrik\WordPress\Infrastructure\WordPress\HookMappers\Mappers\InitMapper;
use Fundrik\WordPress\Infrastructure\WordPress\HookMappers\Mappers\RestPreInsertCampaignMapper;
use Fundrik\WordPress\Infrastructure\WordPress\HookMappers\Mappers\WpAfterInsertPostMapper;
use Fundrik\WordPress\Tests\MockeryTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( HookMapperRegistry::class )]
final class HookMapperRegistryTest extends MockeryTestCase {

	#[Test]
	public function it_returns_expected_mapper_class_names(): void {

		$registry = new HookMapperRegistry();

		$expected = [
			AllowedBlockTypesAllMapper::class,
			DeletePostMapper::class,
			InitMapper::class,
			RestPreInsertCampaignMapper::class,
			WpAfterInsertPostMapper::class,
		];

		$this->assertSame( $expected, $registry->get_mapper_classes() );
	}
}
