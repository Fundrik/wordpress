<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Campaigns\Application\Dto\Exceptions;

use Fundrik\WordPress\Campaigns\Application\Exceptions\WordPressCampaignApplicationException;

/**
 * Exception thrown when invalid data is provided to create a WordPressCampaignDto.
 *
 * @since 1.0.0
 */
final class InvalidWordPressCampaignDtoException extends WordPressCampaignApplicationException {}
