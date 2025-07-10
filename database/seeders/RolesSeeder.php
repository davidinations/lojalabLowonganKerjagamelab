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
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		[
			'nama' => 'Admin',
			'level' => 2,
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		// [
		// 	'nama' => 'HR Manager',
		// 	'level' => 3,
		// 	'create_time' => date('Y-m-d H:i:s'),
		// 	'create_id' => 1,
		// 	'archived' => 0
		// ],
		// [
		// 	'nama' => 'Recruiter',
		// 	'level' => 4,
		// 	'create_time' => date('Y-m-d H:i:s'),
		// 	'create_id' => 1,
		// 	'archived' => 0
		// ]
	];

	foreach ($roles as $role) {
		$db->insert('tbl_roles', $role);
	}

	echo "Roles seeded successfully!\n";
};
