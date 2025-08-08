<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Integration\HookMappers;

use Fundrik\WordPress\Infrastructure\Integration\HookMappers\Mappers\AllowedBlockTypesAllMapper;
use Fundrik\WordPress\Infrastructure\Integration\HookMappers\Mappers\DeletePostMapper;
use Fundrik\WordPress\Infrastructure\Integration\HookMappers\Mappers\InitMapper;
use Fundrik\WordPress\Infrastructure\Integration\HookMappers\Mappers\RestPreInsertCampaignMapper;
use Fundrik\WordPress\Infrastructure\Integration\HookMappers\Mappers\WpAfterInsertPostMapper;

/**
 * Provides the list of mapper classes.
 *
 * @since 1.0.0
 *
 * @internal
 */
class HookMapperRegistry {

	/**
	 * Returns the list of mapper class names.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string> The list of mapper classes.
	 *
	 * @phpstan-return list<class-string<HookToEventMapperInterface>>
	 */
	public function get_mapper_classes(): array {

		return [
			AllowedBlockTypesAllMapper::class,
			DeletePostMapper::class,
			InitMapper::class,
			RestPreInsertCampaignMapper::class,
			WpAfterInsertPostMapper::class,
		];
	}
}
