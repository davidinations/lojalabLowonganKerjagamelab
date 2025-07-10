<?php

class AddForeignKeyConstraints
{
	public function up($db)
	{
		// Add foreign key constraints
		$db->query("
            ALTER TABLE `tbl_admins`
            ADD CONSTRAINT `fk_admins_role`
            FOREIGN KEY (`id_role`) REFERENCES `tbl_roles`(`id`)
            ON UPDATE CASCADE ON DELETE RESTRICT
        ");

		$db->query("
            ALTER TABLE `tbl_lowongans`
            ADD CONSTRAINT `fk_lowongans_organizations`
            FOREIGN KEY (`id_organizations`) REFERENCES `tbl_organizations`(`id`)
            ON UPDATE CASCADE ON DELETE RESTRICT
        ");

		$db->query("
            ALTER TABLE `tbl_lowongans`
            ADD CONSTRAINT `fk_lowongans_type`
            FOREIGN KEY (`id_typeLowongans`) REFERENCES `tbl_typeLowongans`(`id`)
            ON UPDATE CASCADE ON DELETE RESTRICT
        ");

		$db->query("
            ALTER TABLE `tbl_applyLowongans`
            ADD CONSTRAINT `fk_apply_user`
            FOREIGN KEY (`id_user`) REFERENCES `tbl_users`(`id`)
            ON UPDATE CASCADE ON DELETE RESTRICT
        ");

		$db->query("
            ALTER TABLE `tbl_applyLowongans`
            ADD CONSTRAINT `fk_apply_lowongan`
            FOREIGN KEY (`id_lowongans`) REFERENCES `tbl_lowongans`(`id`)
            ON UPDATE CASCADE ON DELETE RESTRICT
        ");

		echo "Added foreign key constraints\n";
	}

	public function down($db)
	{
		// Remove foreign key constraints
		$db->query("ALTER TABLE `tbl_admins` DROP FOREIGN KEY `fk_admins_role`");
		$db->query("ALTER TABLE `tbl_lowongans` DROP FOREIGN KEY `fk_lowongans_organizations`");
		$db->query("ALTER TABLE `tbl_lowongans` DROP FOREIGN KEY `fk_lowongans_type`");
		$db->query("ALTER TABLE `tbl_applyLowongans` DROP FOREIGN KEY `fk_apply_user`");
		$db->query("ALTER TABLE `tbl_applyLowongans` DROP FOREIGN KEY `fk_apply_lowongan`");

		echo "Removed foreign key constraints\n";
	}
}
