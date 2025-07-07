-- MySQL database export

START TRANSACTION;

CREATE TABLE IF NOT EXISTS `tbl_applyLowongans` (
    `id` INT NOT NULL,
    `id_user` INT NOT NULL,
    `id_lowongans` INT NOT NULL,
    `cv_document` VARCHAR(255),
    `pesan` TEXT,
    `create_time` DATETIME,
    `update_time` DATETIME,
    `create_id` INT,
    `update_id` INT,
    `archived` tinyint,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `tbl_lowongans` (
    `id` INT NOT NULL,
    `id_organizations` INT,
    `id_typeLowongans` INT,
    `Kabupaten/Kota` VARCHAR(255),
    `Provinsi` VARCHAR(255),
    `title` VARCHAR(255),
    `jenis_pekerjaan` BIGINT,
    `deskripsi` LONGTEXT,
    `create_time` DATETIME,
    `update_time` DATETIME,
    `create_id` INT,
    `update_id` INT,
    `archived` tinyint,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `tbl_users` (
    `id` INT NOT NULL,
    `username` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `tempat lahir` VARCHAR(255),
    `tanggal lahir` DATE,
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
    `archived` tinyint,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `tbl_roles` (
    `id` INT NOT NULL,
    `nama` VARCHAR(255) NOT NULL,
    `level` tinyint NOT NULL,
    `create_time` DATETIME NOT NULL,
    `update_time` DATETIME,
    `create_id` INT NOT NULL,
    `update_id` INT,
    `archived` tinyint,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `tbl_admins` (
    `id` INT NOT NULL,
    `id_role` INT,
    `username` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `create_time` DATETIME,
    `update_time` DATETIME,
    `create_id` INT,
    `update_id` INT,
    `archived` tinyint,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `tbl_organizations` (
    `id` INT NOT NULL,
    `nama` VARCHAR(255),
    `email` VARCHAR(255),
    `bisnis_perusahaan` VARCHAR(255),
    `alamat_pusat` VARCHAR(255),
    `create_time` DATETIME,
    `update_time` DATETIME,
    `create_id` INT,
    `update_id` INT,
    `archived` tinyint,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `tbl_typeLowongans` (
    `id` INT NOT NULL,
    `category` VARCHAR(255),
    `create_time` DATETIME,
    `update_time` DATETIME,
    `create_id` INT,
    `update_id` INT,
    `archived` tinyint,
    PRIMARY KEY (`id`)
);


-- Foreign key constraints

ALTER TABLE `tbl_roles`
ADD CONSTRAINT `fk_tbl_roles_id` FOREIGN KEY(`id`) REFERENCES `tbl_admins`(`id_role`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `tbl_typeLowongans`
ADD CONSTRAINT `fk_tbl_typeLowongans_id` FOREIGN KEY(`id`) REFERENCES `tbl_lowongans`(`id_typeLowongans`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `tbl_organizations`
ADD CONSTRAINT `fk_tbl_organizations_id` FOREIGN KEY(`id`) REFERENCES `tbl_lowongans`(`id_organizations`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `tbl_users`
ADD CONSTRAINT `fk_tbl_users_id` FOREIGN KEY(`id`) REFERENCES `tbl_applyLowongans`(`id_user`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `tbl_lowongans`
ADD CONSTRAINT `fk_tbl_lowongans_id` FOREIGN KEY(`id`) REFERENCES `tbl_applyLowongans`(`id_lowongans`)
ON UPDATE CASCADE ON DELETE RESTRICT;

COMMIT;
