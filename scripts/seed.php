<?php
require __DIR__ . '/../vendor/autoload.php';

use DI\Container;
use Medoo\Medoo;

// 1. Init container
$container = new Container();

// 2. Load DB config
require __DIR__ . '/../app/config/database.php';

// 3. Get Medoo instance
$db = $container->get('db');

// 4. Seeder files
$seederFiles = [
	__DIR__ . '/../database/seeders/RolesSeeder.php',
	__DIR__ . '/../database/seeders/UsersSeeder.php',
	__DIR__ . '/../database/seeders/AdminssSeeder.php',
];

foreach ($seederFiles as $file) {
	$seeder = require $file;
	$seeder($db);
}
