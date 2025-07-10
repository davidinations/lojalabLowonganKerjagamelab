<?php

class CreateUsersTable
{
	public function up($db)
	{
		$db->query("
            CREATE TABLE IF NOT EXISTS `tbl_users` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `username` VARCHAR(255) NOT NULL,
                `email` VARCHAR(255) NOT NULL,
                `password` VARCHAR(255) NOT NULL,
                `tempat_lahir` VARCHAR(255),
                `tanggal_lahir` DATE,
                `agama` VARCHAR(255),
                `gender` VARCHAR(255),
                `pendidikan_terakhir` VARCHAR(255),
                `tempat_pendidikan_terakhir` VARCHAR(255),
                `jurusan_pendidikan_terakhir` VARCHAR(255),
                `foto_pribadi` VARCHAR(255),
                `create_time` DATETIME NOT NULL,
                `update_time` DATETIME,
                `create_id` INT NOT NULL,
                `update_id` INT,
                `archived` TINYINT DEFAULT 0,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_username` (`username`),
                UNIQUE KEY `unique_email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

		echo "Created table: tbl_users\n";
	}

	public function down($db)
	{
		$db->query("DROP TABLE IF EXISTS `tbl_users`");
		echo "Dropped table: tbl_users\n";
	}
}
