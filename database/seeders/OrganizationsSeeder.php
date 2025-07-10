<?php

return function ($db) {
	echo "Seeding organizations...\n";

	// Check if organizations already exist
	$existingOrgs = $db->count('tbl_organizations');
	if ($existingOrgs > 0) {
		echo "Organizations already exist, skipping...\n";
		return;
	}

	$organizations = [
		// [
		// 	'nama' => 'PT. Teknologi Maju Indonesia',
		// 	'email' => 'info@teknomaju.co.id',
		// 	'bisnis_perusahaan' => 'Software Development',
		// 	'alamat_pusat' => 'Jl. Sudirman No. 123, Jakarta Pusat',
		// 	'create_time' => date('Y-m-d H:i:s'),
		// 	'create_id' => 1,
		// 	'archived' => 0
		// ],
		// [
		// 	'nama' => 'CV. Digital Solusi Kreatif',
		// 	'email' => 'hr@digitalsolusi.com',
		// 	'bisnis_perusahaan' => 'Digital Marketing',
		// 	'alamat_pusat' => 'Jl. Dago No. 45, Bandung',
		// 	'create_time' => date('Y-m-d H:i:s'),
		// 	'create_id' => 1,
		// 	'archived' => 0
		// ],
		// [
		// 	'nama' => 'PT. Manufaktur Sejahtera',
		// 	'email' => 'contact@manufaktursejahtera.co.id',
		// 	'bisnis_perusahaan' => 'Manufacturing',
		// 	'alamat_pusat' => 'Jl. Industri Raya No. 89, Surabaya',
		// 	'create_time' => date('Y-m-d H:i:s'),
		// 	'create_id' => 1,
		// 	'archived' => 0
		// ],
		// [
		// 	'nama' => 'Startup Innovation Lab',
		// 	'email' => 'careers@innovationlab.id',
		// 	'bisnis_perusahaan' => 'Technology Startup',
		// 	'alamat_pusat' => 'Jl. Kemang Raya No. 67, Jakarta Selatan',
		// 	'create_time' => date('Y-m-d H:i:s'),
		// 	'create_id' => 1,
		// 	'archived' => 0
		// ]
	];

	foreach ($organizations as $org) {
		$db->insert('tbl_organizations', $org);
	}

	echo "Organizations seeded successfully!\n";
};
