<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Migrations\Files;

use Fundrik\WordPress\Infrastructure\Migrations\Files\Abstracts\AbstractMigration;

/**
 * Migration class to create the `fundrik_campaigns` table.
 *
 * @since 1.0.0
 *
 * @internal
 *
 * @codeCoverageIgnore
 */
final readonly class CreateFundrikCampaignsTable extends AbstractMigration {

	/**
	 * Applies the migration schema changes.
	 *
	 * @since 1.0.0
	 *
	 * @param string $charset_collate Charset and collation to apply to tables, if needed.
	 */
	public function apply( string $charset_collate ): void {

		$this->query_executor->query(
			"CREATE TABLE IF NOT EXISTS `fundrik_campaigns` (
			`id` bigint unsigned NOT NULL,
			`title` text NOT NULL,
			`slug` varchar(200) NOT NULL,
			`is_enabled` tinyint(1) NOT NULL,
			`is_open` tinyint(1) NOT NULL,
			`has_target` tinyint(1) NOT NULL,
			`target_amount` int unsigned NOT NULL,
			PRIMARY KEY (`id`),
			KEY `slug` (`slug`(191))
			) ENGINE=InnoDB {$charset_collate};",
		);
	}
}
