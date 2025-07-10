<?php

return function ($db) {
	echo "Seeding type lowongans...\n";

	// Check if type lowongans already exist
	$existingTypes = $db->count('tbl_typeLowongans');
	if ($existingTypes > 0) {
		echo "Type lowongans already exist, skipping...\n";
		return;
	}

	$typeLowongans = [
		[
			'category' => 'Information Technology',
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		[
			'category' => 'Marketing & Sales',
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		[
			'category' => 'Human Resources',
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		[
			'category' => 'Finance & Accounting',
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		[
			'category' => 'Operations & Production',
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		[
			'category' => 'Customer Service',
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		[
			'category' => 'Design & Creative',
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		[
			'category' => 'Engineering',
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		]
	];

	foreach ($typeLowongans as $type) {
		$db->insert('tbl_typeLowongans', $type);
	}

	echo "Type lowongans seeded successfully!\n";
};
