<?php

class CreateSkillUsersTable
{
	public function up($db)
	{
		$db->query("
			CREATE TABLE IF NOT EXISTS `tbl_skill_users` (
				`id` INT NOT NULL AUTO_INCREMENT,
				`id_users` INT,
				`id_skills` INT,
				`certificate` VARCHAR(255),
				`descriptions` VARCHAR(255),
				`created_time` DATETIME,
				`update_time` DATETIME,
				`create_id` INT,
				`update_id` INT,
				`archived` INT DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
		");

		echo "Created table: tbl_skill_users\n";
	}

	public function down($db)
	{
		$db->query("DROP TABLE IF EXISTS `tbl_skill_users`");
		echo "Dropped table: tbl_skill_users\n";
	}
}
