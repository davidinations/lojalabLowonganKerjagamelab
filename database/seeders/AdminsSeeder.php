<?php

return function ($db) {
	echo "Seeding admins...\n";

	// Check if admins already exist
	$existingAdmins = $db->count('tbl_admins');
	if ($existingAdmins > 0) {
		echo "Admins already exist, skipping...\n";
		return;
	}

	$admins = [
		[
			'id_role' => 1, // Super Admin
			'username' => 'superadmin',
			'email' => 'superadmin@company.com',
			'password' => password_hash('admin123', PASSWORD_DEFAULT),
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		[
			'id_role' => 2, // Admin
			'username' => 'admin',
			'email' => 'admin@company.com',
			'password' => password_hash('admin123', PASSWORD_DEFAULT),
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		[
			'id_role' => 3, // Recruiter
			'username' => 'recruiter',
			'email' => 'recruiter@company.com',
			'password' => password_hash('recruiter123', PASSWORD_DEFAULT),
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		]
	];

	foreach ($admins as $admin) {
		$db->insert('tbl_admins', $admin);
	}

	echo "Admins seeded successfully!\n";
};
