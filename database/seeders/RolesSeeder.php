<?php

return function ($db) {
	echo "Seeding roles...\n";

	// Check if roles already exist
	$existingRoles = $db->count('tbl_roles');
	if ($existingRoles > 0) {
		echo "Roles already exist, skipping...\n";
		return;
	}

	$roles = [
		[
			'nama' => 'Super Admin',
			'level' => 1,
			'description' => 'Complete system control - can manage all admins, organizations, users, jobs, and settings.',
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		[
			'nama' => 'Admin',
			'level' => 2,
			'description' => 'Can manage users, jobs, applications, and specific organizations. Cannot manage other admins.',
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		[
			'nama' => 'Recruiter',
			'level' => 3,
			'description' => 'Limited to creating/editing jobs and reviewing applications for assigned organization only.',
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		[
			'nama' => 'Viewer',
			'level' => 4,
			'description' => 'Read-only access to all data. Cannot create, edit, or delete anything.',
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		]
	];

	foreach ($roles as $role) {
		$db->insert('tbl_roles', $role);
	}

	echo "Roles seeded successfully!\n";
};
