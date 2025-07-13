<?php

class CreateApplyLowongansTable
{
	public function up($db)
	{
		$db->query("
			CREATE TABLE IF NOT EXISTS `tbl_applyLowongans` (
				`id` INT NOT NULL AUTO_INCREMENT,
				`id_user` INT NOT NULL,
				`id_lowongans` INT NOT NULL,
				`cv_document` VARCHAR(255),
				`pesan` TEXT,
				`create_time` DATETIME,
				`update_time` DATETIME,
				`create_id` INT,
				`update_id` INT,
				`archived` TINYINT DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
		");

		echo "Created table: tbl_applyLowongans\n";
	}

	public function down($db)
	{
		$db->query("DROP TABLE IF EXISTS `tbl_applyLowongans`");
		echo "Dropped table: tbl_applyLowongans\n";
	}
}
