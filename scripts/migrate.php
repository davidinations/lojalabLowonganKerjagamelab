<?php
require __DIR__ . '/../vendor/autoload.php';

use DI\Container;

// 1. Inisialisasi container
$container = new Container();

// 2. Create database if it doesn't exist
createDatabaseIfNotExists();

// 3. Pastikan variabel $container tersedia sebelum require
require __DIR__ . '/../app/config/database.php';

// 4. Ambil instance Medoo dari container
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

// Argument: migrate, rollback, seed, atau reset
$command = $argv[1] ?? 'migrate';

switch ($command) {
	case 'migrate':
		migrate($db);
		break;

	case 'rollback':
		rollback($db);
		break;

	case 'seed':
		seed($db);
		break;

	case 'reset':
		resetMigrations($db);
		break;

	case 'fresh':
		fresh($db);
		break;

	default:
		echo "Available commands:\n";
		echo "  migrate  - Run pending migrations\n";
		echo "  rollback - Rollback last batch of migrations\n";
		echo "  seed     - Run database seeders\n";
		echo "  reset    - Rollback all migrations\n";
		echo "  fresh    - Drop all tables and re-run migrations with seeders\n";
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

function seed($db)
{
	echo "Running database seeders...\n";

	// Load seeder files in order
	$seederFiles = [
		__DIR__ . '/../database/seeders/RolesSeeder.php',
		__DIR__ . '/../database/seeders/UsersSeeder.php',
		__DIR__ . '/../database/seeders/AdminsSeeder.php',
		__DIR__ . '/../database/seeders/OrganizationsSeeder.php',
		__DIR__ . '/../database/seeders/TypeLowongansSeeder.php',
	];

	foreach ($seederFiles as $file) {
		if (!file_exists($file)) {
			echo "Seeder file not found: " . basename($file) . "\n";
			continue;
		}

		$seeder = require $file;
		if (is_callable($seeder)) {
			$seeder($db);
			echo "Seeder completed: " . basename($file) . "\n";
		} else {
			echo "Invalid seeder format: " . basename($file) . "\n";
		}
	}

	echo "All seeders completed!\n";
}

function resetMigrations($db)
{
	echo "Rolling back all migrations...\n";

	while (true) {
		$lastBatch = (int) $db->max('migrations', 'batch');
		if ($lastBatch === 0) {
			break;
		}

		$migrations = $db->select('migrations', '*', ['batch' => $lastBatch]);

		foreach (array_reverse($migrations) as $migrationRow) {
			$filename = $migrationRow['migration'];
			$file = __DIR__ . "/../migrations/$filename";
			if (!file_exists($file)) {
				echo "File migration gone: $filename\n";
				$db->delete('migrations', ['id' => $migrationRow['id']]);
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

	echo "All migrations rolled back!\n";
}

function fresh($db)
{
	echo "Dropping all tables and running fresh migration...\n";

	// Drop all tables (be careful with this in production!)
	$tables = ['tbl_applyLowongans', 'tbl_lowongans', 'tbl_users', 'tbl_roles', 'tbl_admins', 'tbl_organizations', 'tbl_typeLowongans', 'migrations'];

	foreach ($tables as $table) {
		try {
			$db->query("DROP TABLE IF EXISTS `$table`");
			echo "Dropped table: $table\n";
		} catch (Exception $e) {
			echo "Could not drop table $table: " . $e->getMessage() . "\n";
		}
	}

	// Recreate migrations table
	$db->query("
		CREATE TABLE IF NOT EXISTS migrations (
			id INT AUTO_INCREMENT PRIMARY KEY,
			migration VARCHAR(255) NOT NULL,
			batch INT NOT NULL,
			migrated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
		)
	");

	// Run migrations
	migrate($db);

	// Run seeders
	seed($db);

	echo "Fresh migration completed!\n";
}

function class_from_filename($filename)
{
	// contoh: 20240628_create_roles_table.php → CreateRolesTable
	$namePart = explode('_', preg_replace('/\.php$/', '', $filename), 2)[1] ?? '';
	return str_replace(' ', '', ucwords(str_replace('_', ' ', $namePart)));
}

function createDatabaseIfNotExists()
{
	// Database configuration
	$host = 'localhost';
	$port = 3306;
	$database = 'lowonganKerjaGamelab';
	$username = 'root';
	$password = '';

	try {
		// Connect to MySQL without specifying database
		$pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $username, $password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// Create database if it doesn't exist
		$sql = "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
		$pdo->exec($sql);

		echo "Database '$database' is ready.\n";
	} catch (PDOException $e) {
		echo "Error creating database: " . $e->getMessage() . "\n";
		exit(1);
	}
}
