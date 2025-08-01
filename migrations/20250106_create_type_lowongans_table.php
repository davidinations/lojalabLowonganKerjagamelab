<?php

class CreateTypeLowongansTable
{
	public function up($db)
	{
		$db->query("
			CREATE TABLE IF NOT EXISTS `tbl_typeLowongans` (
				`id` INT NOT NULL AUTO_INCREMENT,
				`category` VARCHAR(255),
				`create_time` DATETIME,
				`update_time` DATETIME,
				`create_id` INT,
				`update_id` INT,
				`archived` TINYINT DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
		");

		echo "Created table: tbl_typeLowongans\n";
	}

	public function down($db)
	{
		$db->query("DROP TABLE IF EXISTS `tbl_typeLowongans`");
		echo "Dropped table: tbl_typeLowongans\n";
	}
}
