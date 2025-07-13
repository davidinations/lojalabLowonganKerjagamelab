<?php

class CreateSkillTypeLowongansTable
{
	public function up($db)
	{
		$db->query("
			CREATE TABLE IF NOT EXISTS `tbl_skill_typeLowongans` (
				`id` BIGINT NOT NULL AUTO_INCREMENT,
				`id_skills` INT,
				`id_lowongans` INT,
				`created_time` DATETIME,
				`updated_time` DATETIME,
				`create_id` INT,
				`update_id` INT,
				`archived` INT DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
		");

		echo "Created table: tbl_skill_typeLowongans\n";
	}

	public function down($db)
	{
		$db->query("DROP TABLE IF EXISTS `tbl_skill_typeLowongans`");
		echo "Dropped table: tbl_skill_typeLowongans\n";
	}
}
