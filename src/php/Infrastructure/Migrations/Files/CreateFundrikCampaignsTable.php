<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Infrastructure\Migrations\Files;

use Fundrik\WordPress\Infrastructure\Migrations\AbstractMigration;
use Fundrik\WordPress\Infrastructure\Migrations\MigrationVersion;

/**
 * Creates the `fundrik_campaigns` table in the database.
 *
 * @since 1.0.0
 *
 * @internal
 *
 * @codeCoverageIgnore
 */
#[MigrationVersion( '2025_06_15_00' )]
final readonly class CreateFundrikCampaignsTable extends AbstractMigration {

	/**
	 * Applies the table creation schema for campaign data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $charset_collate The charset and collation string for table creation.
	 */
	public function apply( string $charset_collate ): void {

		$this->database->query(
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
