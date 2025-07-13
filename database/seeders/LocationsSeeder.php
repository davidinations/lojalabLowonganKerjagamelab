<?php

return function ($db) {
	echo "Seeding locations...\n";

	// Check if locations already exist
	$existingLocations = $db->count('tbl_locations');
	if ($existingLocations > 0) {
		echo "Locations already exist, skipping...\n";
		return;
	}

	$locations = [
		[
			'nama' => 'Jakarta Pusat',
			'alamat' => 'Jl. Sudirman No. 1',
			'kecamatan' => 'Tanah Abang',
			'kabupaten' => 'Jakarta Pusat',
			'provinsi' => 'DKI Jakarta',
			'negara' => 'Indonesia',
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		[
			'nama' => 'Bandung',
			'alamat' => 'Jl. Asia Afrika No. 100',
			'kecamatan' => 'Sumur Bandung',
			'kabupaten' => 'Bandung',
			'provinsi' => 'Jawa Barat',
			'negara' => 'Indonesia',
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		[
			'nama' => 'Surabaya',
			'alamat' => 'Jl. Pemuda No. 50',
			'kecamatan' => 'Genteng',
			'kabupaten' => 'Surabaya',
			'provinsi' => 'Jawa Timur',
			'negara' => 'Indonesia',
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		]
	];

	foreach ($locations as $location) {
		$db->insert('tbl_locations', $location);
	}

	echo "Locations seeded successfully!\n";
};
