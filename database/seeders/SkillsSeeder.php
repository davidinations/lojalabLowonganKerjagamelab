<?php

return function ($db) {
	echo "Seeding skills...\n";

	// Check if skills already exist
	$existingSkills = $db->count('tbl_skills');
	if ($existingSkills > 0) {
		echo "Skills already exist, skipping...\n";
		return;
	}

	$skills = [
		[
			'nama' => 'PHP',
			'created_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		[
			'nama' => 'JavaScript',
			'created_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		[
			'nama' => 'MySQL',
			'created_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		[
			'nama' => 'Laravel',
			'created_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		[
			'nama' => 'React',
			'created_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		[
			'nama' => 'Node.js',
			'created_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		]
	];

	foreach ($skills as $skill) {
		$db->insert('tbl_skills', $skill);
	}

	echo "Skills seeded successfully!\n";
};
