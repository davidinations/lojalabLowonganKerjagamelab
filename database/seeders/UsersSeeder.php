<?php

return function ($db) {
	echo "Seeding users...\n";

	// Check if users already exist
	$existingUsers = $db->count('tbl_users');
	if ($existingUsers > 0) {
		echo "Users already exist, skipping...\n";
		return;
	}

	$users = [
		// [
		// 	'username' => 'john_doe',
		// 	'email' => 'john.doe@example.com',
		// 	'password' => password_hash('password123', PASSWORD_DEFAULT),
		// 	'tempat_lahir' => 'Jakarta',
		// 	'tanggal_lahir' => '1990-05-15',
		// 	'agama' => 'Islam',
		// 	'gender' => 'Laki-laki',
		// 	'pendidikan_terakhir' => 'S1',
		// 	'tempat_pendidikan_terakhir' => 'Universitas Indonesia',
		// 	'jurusan_pendidikan_terakhir' => 'Teknik Informatika',
		// 	'create_time' => date('Y-m-d H:i:s'),
		// 	'create_id' => 1,
		// 	'archived' => 0
		// ],
		[
			'username' => 'jane_smith',
			'email' => 'jane.smith@example.com',
			'password' => password_hash('password123', PASSWORD_DEFAULT),
			'tempat_lahir' => 'Bandung',
			'tanggal_lahir' => '1992-08-20',
			'agama' => 'Kristen',
			'gender' => 'Perempuan',
			'pendidikan_terakhir' => 'S1',
			'tempat_pendidikan_terakhir' => 'Institut Teknologi Bandung',
			'jurusan_pendidikan_terakhir' => 'Sistem Informasi',
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => 1,
			'archived' => 0
		],
		// [
		// 	'username' => 'mike_johnson',
		// 	'email' => 'mike.johnson@example.com',
		// 	'password' => password_hash('password123', PASSWORD_DEFAULT),
		// 	'tempat_lahir' => 'Surabaya',
		// 	'tanggal_lahir' => '1988-12-10',
		// 	'agama' => 'Katolik',
		// 	'gender' => 'Laki-laki',
		// 	'pendidikan_terakhir' => 'S2',
		// 	'tempat_pendidikan_terakhir' => 'Universitas Gadjah Mada',
		// 	'jurusan_pendidikan_terakhir' => 'Manajemen',
		// 	'create_time' => date('Y-m-d H:i:s'),
		// 	'create_id' => 1,
		// 	'archived' => 0
		// ]
	];

	foreach ($users as $user) {
		$db->insert('tbl_users', $user);
	}

	echo "Users seeded successfully!\n";
};
