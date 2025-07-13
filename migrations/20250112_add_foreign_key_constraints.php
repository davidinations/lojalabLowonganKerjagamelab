<?php

class AddForeignKeyConstraints
{
	public function up($db)
	{
		// Add foreign key for admins -> roles
		$db->query("
			ALTER TABLE `tbl_admins`
			ADD CONSTRAINT `fk_admins_role`
			FOREIGN KEY (`id_role`) REFERENCES `tbl_roles`(`id`)
			ON UPDATE CASCADE ON DELETE RESTRICT
		");

		// Add foreign key for admins -> organizations
		$db->query("
			ALTER TABLE `tbl_admins`
			ADD CONSTRAINT `fk_admins_organizations`
			FOREIGN KEY (`id_organizations`) REFERENCES `tbl_organizations`(`id`)
			ON UPDATE CASCADE ON DELETE RESTRICT
		");

		// Add foreign key for organizations -> locations
		$db->query("
			ALTER TABLE `tbl_organizations`
			ADD CONSTRAINT `fk_organizations_locations`
			FOREIGN KEY (`id_locations`) REFERENCES `tbl_locations`(`id`)
			ON UPDATE CASCADE ON DELETE RESTRICT
		");

		// Add foreign key for lowongans -> organizations
		$db->query("
			ALTER TABLE `tbl_lowongans`
			ADD CONSTRAINT `fk_lowongans_organizations`
			FOREIGN KEY (`id_organizations`) REFERENCES `tbl_organizations`(`id`)
			ON UPDATE CASCADE ON DELETE RESTRICT
		");

		// Add foreign key for lowongans -> type lowongans
		$db->query("
			ALTER TABLE `tbl_lowongans`
			ADD CONSTRAINT `fk_lowongans_type`
			FOREIGN KEY (`id_typeLowongans`) REFERENCES `tbl_typeLowongans`(`id`)
			ON UPDATE CASCADE ON DELETE RESTRICT
		");

		// Add foreign key for lowongans -> locations
		$db->query("
			ALTER TABLE `tbl_lowongans`
			ADD CONSTRAINT `fk_lowongans_locations`
			FOREIGN KEY (`id_locations`) REFERENCES `tbl_locations`(`id`)
			ON UPDATE CASCADE ON DELETE RESTRICT
		");

		// Add foreign key for apply lowongans -> users
		$db->query("
			ALTER TABLE `tbl_applyLowongans`
			ADD CONSTRAINT `fk_apply_user`
			FOREIGN KEY (`id_user`) REFERENCES `tbl_users`(`id`)
			ON UPDATE CASCADE ON DELETE RESTRICT
		");

		// Add foreign key for apply lowongans -> lowongans
		$db->query("
			ALTER TABLE `tbl_applyLowongans`
			ADD CONSTRAINT `fk_apply_lowongan`
			FOREIGN KEY (`id_lowongans`) REFERENCES `tbl_lowongans`(`id`)
			ON UPDATE CASCADE ON DELETE RESTRICT
		");

		// Add foreign key for skill_users -> users
		$db->query("
			ALTER TABLE `tbl_skill_users`
			ADD CONSTRAINT `fk_skill_users_users`
			FOREIGN KEY (`id_users`) REFERENCES `tbl_users`(`id`)
			ON UPDATE CASCADE ON DELETE RESTRICT
		");

		// Add foreign key for skill_users -> skills
		$db->query("
			ALTER TABLE `tbl_skill_users`
			ADD CONSTRAINT `fk_skill_users_skills`
			FOREIGN KEY (`id_skills`) REFERENCES `tbl_skills`(`id`)
			ON UPDATE CASCADE ON DELETE RESTRICT
		");

		// Add foreign key for skill_typeLowongans -> skills
		$db->query("
			ALTER TABLE `tbl_skill_typeLowongans`
			ADD CONSTRAINT `fk_skill_type_skills`
			FOREIGN KEY (`id_skills`) REFERENCES `tbl_skills`(`id`)
			ON UPDATE CASCADE ON DELETE RESTRICT
		");

		// Add foreign key for skill_typeLowongans -> lowongans
		$db->query("
			ALTER TABLE `tbl_skill_typeLowongans`
			ADD CONSTRAINT `fk_skill_type_lowongans`
			FOREIGN KEY (`id_lowongans`) REFERENCES `tbl_lowongans`(`id`)
			ON UPDATE CASCADE ON DELETE RESTRICT
		");

		echo "Added all foreign key constraints\n";
	}

	public function down($db)
	{
		// Remove all foreign key constraints
		$db->query("ALTER TABLE `tbl_admins` DROP FOREIGN KEY `fk_admins_role`");
		$db->query("ALTER TABLE `tbl_admins` DROP FOREIGN KEY `fk_admins_organizations`");
		$db->query("ALTER TABLE `tbl_organizations` DROP FOREIGN KEY `fk_organizations_locations`");
		$db->query("ALTER TABLE `tbl_lowongans` DROP FOREIGN KEY `fk_lowongans_organizations`");
		$db->query("ALTER TABLE `tbl_lowongans` DROP FOREIGN KEY `fk_lowongans_type`");
		$db->query("ALTER TABLE `tbl_lowongans` DROP FOREIGN KEY `fk_lowongans_locations`");
		$db->query("ALTER TABLE `tbl_applyLowongans` DROP FOREIGN KEY `fk_apply_user`");
		$db->query("ALTER TABLE `tbl_applyLowongans` DROP FOREIGN KEY `fk_apply_lowongan`");
		$db->query("ALTER TABLE `tbl_skill_users` DROP FOREIGN KEY `fk_skill_users_users`");
		$db->query("ALTER TABLE `tbl_skill_users` DROP FOREIGN KEY `fk_skill_users_skills`");
		$db->query("ALTER TABLE `tbl_skill_typeLowongans` DROP FOREIGN KEY `fk_skill_type_skills`");
		$db->query("ALTER TABLE `tbl_skill_typeLowongans` DROP FOREIGN KEY `fk_skill_type_lowongans`");

		echo "Removed all foreign key constraints\n";
	}
}
