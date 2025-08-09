<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\Infrastructure\Integration\HookBridges;

use Fundrik\WordPress\Infrastructure\Integration\HookBridges\Bridges\AllowedBlockTypesAllFilterBridge;
use Fundrik\WordPress\Infrastructure\Integration\HookBridges\Bridges\DeletePostActionBridge;
use Fundrik\WordPress\Infrastructure\Integration\HookBridges\Bridges\InitActionBridge;
use Fundrik\WordPress\Infrastructure\Integration\HookBridges\Bridges\RestPreInsertCampaignFilterBridge;
use Fundrik\WordPress\Infrastructure\Integration\HookBridges\Bridges\WpAfterInsertPostActionBridge;
use Fundrik\WordPress\Infrastructure\Integration\HookBridges\HookBridgeRegistry;
use Fundrik\WordPress\Tests\MockeryTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass( HookBridgeRegistry::class )]
final class HookBridgeRegistryTest extends MockeryTestCase {

	#[Test]
	public function it_returns_expected_bridge_class_names(): void {

		$registry = new HookBridgeRegistry();

		$expected = [
			AllowedBlockTypesAllFilterBridge::class,
			DeletePostActionBridge::class,
			InitActionBridge::class,
			RestPreInsertCampaignFilterBridge::class,
			WpAfterInsertPostActionBridge::class,
		];

		$this->assertSame( $expected, $registry->get_bridge_classes() );
	}
}
