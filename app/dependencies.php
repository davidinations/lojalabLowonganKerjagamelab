<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use App\Application\Controllers\AuthController;
use App\Application\Controllers\UserController;
use App\Application\Controllers\AdminController;
use App\Application\Controllers\LowonganController;
use App\Application\Controllers\ApplyLowonganController;
use App\Application\Controllers\OrganizationController;
use App\Application\Controllers\ProfileController;
use App\Application\Controllers\RoleController;
use App\Application\Controllers\SkillController;
use App\Application\Controllers\DashboardController;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use Medoo\Medoo;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },

        // Database Configuration
        'db' => function (ContainerInterface $c) {
            return new Medoo([
                'type' => 'mysql',
                'host' => 'localhost',
                'port' => 3306,
                'database' => 'lowonganKerjaGamelab',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4',
            ]);
        },

        // Twig View Configuration
        Twig::class => function (ContainerInterface $c) {
            return Twig::create(__DIR__ . '/../templates', [
                'cache' => __DIR__ . '/../var/cache',
                'debug' => true
            ]);
        },

        // Controller Dependencies
        AuthController::class => function (ContainerInterface $c) {
            return new AuthController($c->get('db'), $c->get(Twig::class));
        },

        UserController::class => function (ContainerInterface $c) {
            return new UserController($c->get('db'), $c->get(Twig::class));
        },

        AdminController::class => function (ContainerInterface $c) {
            return new AdminController($c->get('db'), $c->get(Twig::class));
        },

        LowonganController::class => function (ContainerInterface $c) {
            return new LowonganController($c->get('db'), $c->get(Twig::class));
        },

        ApplyLowonganController::class => function (ContainerInterface $c) {
            return new ApplyLowonganController($c->get('db'), $c->get(Twig::class));
        },

        OrganizationController::class => function (ContainerInterface $c) {
            return new OrganizationController($c->get('db'), $c->get(Twig::class));
        },

        ProfileController::class => function (ContainerInterface $c) {
            return new ProfileController($c->get('db'), $c->get(Twig::class));
        },

        RoleController::class => function (ContainerInterface $c) {
            return new RoleController($c->get('db'), $c->get(Twig::class));
        },

        SkillController::class => function (ContainerInterface $c) {
            return new SkillController($c->get('db'), $c->get(Twig::class));
        },

        DashboardController::class => function (ContainerInterface $c) {
            return new DashboardController($c->get('db'), $c->get(Twig::class));
        },
    ]);
};
