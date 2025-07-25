<?php

class CreateSkillsTable
{
	public function up($db)
	{
		$db->query("
			CREATE TABLE IF NOT EXISTS `tbl_skills` (
				`id` INT NOT NULL AUTO_INCREMENT,
				`nama` VARCHAR(255),
				`created_time` DATETIME,
				`update_time` DATETIME,
				`create_id` INT,
				`update_id` INT,
				`archived` INT DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
		");

		echo "Created table: tbl_skills\n";
	}

	public function down($db)
	{
		$db->query("DROP TABLE IF EXISTS `tbl_skills`");
		echo "Dropped table: tbl_skills\n";
	}
}
