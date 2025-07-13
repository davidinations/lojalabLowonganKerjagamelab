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


CREATE TABLE IF NOT EXISTS `tbl_locations` (
    `id` INT NOT NULL,
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
    `archived` INT,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `tbl_lowongans` (
    `id` INT NOT NULL,
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


CREATE TABLE IF NOT EXISTS `tbl_skill_users` (
    `id` INT NOT NULL,
    `id_users` INT,
    `id_skills` INT,
    `certificate` VARCHAR(255),
    `descriptions` VARCHAR(255),
    `created_time` DATETIME,
    `update_time` DATETIME,
    `create_id` INT,
    `update_id` INT,
    `archived` INT,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `tbl_admins` (
    `id` INT NOT NULL,
    `id_role` INT,
    `id_organizations` INT,
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
    `id_locations` INT,
    `nama` VARCHAR(255),
    `email` VARCHAR(255),
    `bisnis_perusahaan` VARCHAR(255),
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


CREATE TABLE IF NOT EXISTS `tbl_skills` (
    `id` INT NOT NULL,
    `nama` VARCHAR(255),
    `created_time` DATETIME,
    `update_time` DATETIME,
    `create_id` INT,
    `update_id` INT,
    `archived` INT,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `tbl_skill_typeLowongans` (
    `id` BIGINT NOT NULL,
    `id_skills` INT,
    `id_lowongans` INT,
    `created_time` DATETIME,
    `updated_time` DATETIME,
    `create_id` INT,
    `update_id` INT,
    `archived` INT,
    PRIMARY KEY (`id`)
);


-- Foreign key constraints

ALTER TABLE `tbl_admins`
ADD CONSTRAINT `fk_tbl_admins_id_organizations` FOREIGN KEY(`id_organizations`) REFERENCES `tbl_organizations`(`id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `tbl_lowongans`
ADD CONSTRAINT `fk_tbl_lowongans_id` FOREIGN KEY(`id`) REFERENCES `tbl_applyLowongans`(`id_lowongans`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `tbl_organizations`
ADD CONSTRAINT `fk_tbl_organizations_id` FOREIGN KEY(`id`) REFERENCES `tbl_lowongans`(`id_organizations`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `tbl_roles`
ADD CONSTRAINT `fk_tbl_roles_id` FOREIGN KEY(`id`) REFERENCES `tbl_admins`(`id_role`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `tbl_typeLowongans`
ADD CONSTRAINT `fk_tbl_typeLowongans_id` FOREIGN KEY(`id`) REFERENCES `tbl_lowongans`(`id_typeLowongans`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `tbl_users`
ADD CONSTRAINT `fk_tbl_users_id` FOREIGN KEY(`id`) REFERENCES `tbl_applyLowongans`(`id_user`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `tbl_locations`
ADD CONSTRAINT `fk_tbl_locations_id` FOREIGN KEY(`id`) REFERENCES `tbl_lowongans`(`id_locations`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `tbl_locations`
ADD CONSTRAINT `fk_tbl_locations_id` FOREIGN KEY(`id`) REFERENCES `tbl_organizations`(`id_locations`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `tbl_users`
ADD CONSTRAINT `fk_tbl_users_id` FOREIGN KEY(`id`) REFERENCES `tbl_skill_users`(`id_users`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `tbl_skills`
ADD CONSTRAINT `fk_tbl_skills_id` FOREIGN KEY(`id`) REFERENCES `tbl_skill_users`(`id_skills`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `tbl_skills`
ADD CONSTRAINT `fk_tbl_skills_id` FOREIGN KEY(`id`) REFERENCES `tbl_skill_typeLowongans`(`id_skills`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `tbl_typeLowongans`
ADD CONSTRAINT `fk_tbl_typeLowongans_id` FOREIGN KEY(`id`) REFERENCES `tbl_skill_typeLowongans`(`id_lowongans`)
ON UPDATE CASCADE ON DELETE RESTRICT;

COMMIT;
