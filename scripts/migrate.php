<?php
require 'vendor/autoload.php';

use DI\Container;

// 1. Inisialisasi container
$container = new Container();

// 2. Pastikan variabel $container tersedia sebelum require
require __DIR__ . '/../app/config/database.php';

// 3. Ambil instance Medoo dari container
$db = $container->get('db');


// Buat tabel penyimpanan versi migrasi jika belum ada
$db->query("
    CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL,
        batch INT NOT NULL,
        migrated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

// Argument: migrate atau rollback
$command = $argv[1] ?? 'migrate';

switch ($command) {
	case 'migrate':
		migrate($db);
		break;

	case 'rollback':
		rollback($db);
		break;

	default:
		echo "Command does not exist. Use: migrate | rollback\n";
}

function migrate($db)
{
	$files = glob(__DIR__ . '/../migrations/*.php');
	$migrated = $db->select('migrations', 'migration');
	$batch = (int) $db->max('migrations', 'batch') + 1;

	foreach ($files as $file) {
		$filename = basename($file);
		if (in_array($filename, $migrated)) {
			continue; // sudah dimigrasi
		}

		require_once $file;
		$className = class_from_filename($filename);
		$migration = new $className();
		$migration->up($db);

		$db->insert('migrations', [
			'migration' => $filename,
			'batch' => $batch,
		]);

		echo "Migration: $filename done\n";
	}
}

function rollback($db)
{
	$lastBatch = (int) $db->max('migrations', 'batch');
	if ($lastBatch === 0) {
		echo "Cannot find any migration. Rollback canceled!\n";
		return;
	}

	$migrations = $db->select('migrations', '*', ['batch' => $lastBatch]);

	foreach (array_reverse($migrations) as $migrationRow) {
		$filename = $migrationRow['migration'];
		$file = __DIR__ . "/../migrations/$filename";
		if (!file_exists($file)) {
			echo "File migration gone: $filename\n";
			continue;
		}

		require_once $file;
		$className = class_from_filename($filename);
		$migration = new $className();
		$migration->down($db);

		$db->delete('migrations', ['id' => $migrationRow['id']]);
		echo "Rollback: $filename done\n";
	}
}

function class_from_filename($filename)
{
	// contoh: 20240628_create_roles_table.php → CreateRolesTable
	$namePart = explode('_', preg_replace('/\.php$/', '', $filename), 2)[1] ?? '';
	return str_replace(' ', '', ucwords(str_replace('_', ' ', $namePart)));
}
