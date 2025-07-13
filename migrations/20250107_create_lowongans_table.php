<?php

class CreateLowongansTable
{
	public function up($db)
	{
		$db->query("
			CREATE TABLE IF NOT EXISTS `tbl_lowongans` (
				`id` INT NOT NULL AUTO_INCREMENT,
				`id_organizations` INT,
				`id_typeLowongans` INT,
				`id_locations` INT,
				`title` VARCHAR(255),
				`jenis_pekerjaan` BIGINT,
				`deskripsi` LONGTEXT,
				`create_time` DATETIME,
				`update_time` DATETIME,
				`create_id` INT,
				`update_id` INT,
				`archived` TINYINT DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
		");

		echo "Created table: tbl_lowongans\n";
	}

	public function down($db)
	{
		$db->query("DROP TABLE IF EXISTS `tbl_lowongans`");
		echo "Dropped table: tbl_lowongans\n";
	}
}
