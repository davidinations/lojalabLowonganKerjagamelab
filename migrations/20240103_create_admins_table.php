<?php

class CreateAdminsTable
{
	public function up($db)
	{
		$db->query("
            CREATE TABLE IF NOT EXISTS `tbl_admins` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `id_role` INT,
                `username` VARCHAR(255) NOT NULL,
                `email` VARCHAR(255) NOT NULL,
                `password` VARCHAR(255) NOT NULL,
                `create_time` DATETIME,
                `update_time` DATETIME,
                `create_id` INT,
                `update_id` INT,
                `archived` TINYINT DEFAULT 0,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_admin_username` (`username`),
                UNIQUE KEY `unique_admin_email` (`email`),
                KEY `idx_id_role` (`id_role`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

		echo "Created table: tbl_admins\n";
	}

	public function down($db)
	{
		$db->query("DROP TABLE IF EXISTS `tbl_admins`");
		echo "Dropped table: tbl_admins\n";
	}
}
