<?php

class CreateRolesTable
{
	public function up($db)
	{
		$db->query("
			CREATE TABLE IF NOT EXISTS `tbl_roles` (
				`id` INT NOT NULL AUTO_INCREMENT,
				`nama` VARCHAR(255) NOT NULL,
				`level` TINYINT NOT NULL,
				`create_time` DATETIME NOT NULL,
				`update_time` DATETIME,
				`create_id` INT NOT NULL,
				`update_id` INT,
				`archived` TINYINT DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
		");

		echo "Created table: tbl_roles\n";
	}

	public function down($db)
	{
		$db->query("DROP TABLE IF EXISTS `tbl_roles`");
		echo "Dropped table: tbl_roles\n";
	}
}
