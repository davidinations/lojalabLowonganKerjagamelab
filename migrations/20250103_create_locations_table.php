<?php

class CreateLocationsTable
{
	public function up($db)
	{
		$db->query("
			CREATE TABLE IF NOT EXISTS `tbl_locations` (
				`id` INT NOT NULL AUTO_INCREMENT,
				`nama` VARCHAR(255),
				`alamat` TEXT,
				`kecamatan` VARCHAR(255),
				`kabupaten` VARCHAR(255),
				`provinsi` VARCHAR(255),
				`negara` VARCHAR(255),
				`create_time` DATETIME,
				`update_time` DATETIME,
				`create_id` INT,
				`update_id` INT,
				`archived` INT DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
		");

		echo "Created table: tbl_locations\n";
	}

	public function down($db)
	{
		$db->query("DROP TABLE IF EXISTS `tbl_locations`");
		echo "Dropped table: tbl_locations\n";
	}
}
