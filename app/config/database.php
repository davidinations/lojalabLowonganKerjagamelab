<?php

use Medoo\Medoo;

$container->set('db', function () {
	return new Medoo([
		'type' => 'mysql',
		'host' => 'localhost',
		'port' => 3306,
		'database' => '',
		'username' => 'root',
		'password' => '',
		'charset' => 'utf8mb4',
	]);
});
