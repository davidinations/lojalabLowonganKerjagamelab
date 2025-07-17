<?php

declare(strict_types=1);

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
use App\Application\Middleware\AuthMiddleware;
use App\Application\Middleware\AdminMiddleware;
use App\Application\Middleware\PermissionMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    // CORS Pre-Flight OPTIONS Request Handler
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    // Public Routes (No authentication required)
    $app->get('/', function (Request $request, Response $response) {
        return $response->withHeader('Location', '/login')->withStatus(302);
    });

    // Authentication Routes
    $app->get('/login', AuthController::class . ':showLogin');
    $app->post('/login', AuthController::class . ':login');
    $app->get('/register', AuthController::class . ':showRegister');
    $app->post('/register', AuthController::class . ':register');
    $app->get('/forgot-password', AuthController::class . ':showForgotPassword');
    $app->post('/forgot-password', AuthController::class . ':forgotPassword');
    $app->get('/logout', AuthController::class . ':logout');

    // Protected Routes (Authentication required)
    $app->group('', function (Group $group) {

        // Dashboard Routes
        $group->get('/dashboard', UserController::class . ':dashboard');

        // User Profile Routes
        $group->get('/profile', ProfileController::class . ':show');
        $group->get('/profile/edit', ProfileController::class . ':showEdit');
        $group->post('/profile/edit', ProfileController::class . ':update');

        // Job Listings (Public viewing)
        $group->get('/jobs', LowonganController::class . ':index');
        $group->get('/jobs/search', LowonganController::class . ':search');
        $group->get('/jobs/{id}', LowonganController::class . ':show');

        // Job Applications
        $group->get('/jobs/{id}/apply', ApplyLowonganController::class . ':showApply');
        $group->post('/jobs/{id}/apply', ApplyLowonganController::class . ':apply');
        $group->get('/my-applications', ApplyLowonganController::class . ':myApplications');

        // User Management (for users to view their own data)
        $group->get('/users/{id}/profile', UserController::class . ':profile');
        $group->get('/users/{id}/edit', UserController::class . ':showEdit');
        $group->post('/users/{id}/edit', UserController::class . ':update');
    })->add(AuthMiddleware::class);

    // Admin Routes (Admin authentication + role-based access)
    $app->group('/admin', function (Group $group) {

        // Admin Dashboard
        $group->get('/dashboard', AdminController::class . ':dashboard');

        // User Management (Admin/Super Admin can create, only Super Admin can edit)
        $group->group('/users', function (Group $subGroup) {
            $subGroup->get('', UserController::class . ':index');
            $subGroup->get('/create', UserController::class . ':showCreate');
            $subGroup->post('/create', UserController::class . ':create');
            $subGroup->get('/{id}', UserController::class . ':profile');
            $subGroup->get('/{id}/edit', UserController::class . ':showEdit');
            $subGroup->post('/{id}/edit', UserController::class . ':update');
            $subGroup->delete('/{id}', UserController::class . ':delete');
        })->add(new AdminMiddleware(2)); // Admin level required

        // Admin Management (Super Admin only)
        $group->group('/admins', function (Group $subGroup) {
            $subGroup->get('', AdminController::class . ':index');
            $subGroup->get('/create', AdminController::class . ':showCreate');
            $subGroup->post('/create', AdminController::class . ':create');
            $subGroup->get('/{id}/edit', AdminController::class . ':showEdit');
            $subGroup->post('/{id}/edit', AdminController::class . ':update');
            $subGroup->delete('/{id}', AdminController::class . ':delete');
        })->add(new AdminMiddleware(1)); // Super Admin level required

        // Role Management (Super Admin only)
        $group->group('/roles', function (Group $subGroup) {
            $subGroup->get('', RoleController::class . ':index');
            $subGroup->get('/create', RoleController::class . ':showCreate');
            $subGroup->post('/create', RoleController::class . ':create');
            $subGroup->get('/{id}/edit', RoleController::class . ':showEdit');
            $subGroup->post('/{id}/edit', RoleController::class . ':update');
            $subGroup->delete('/{id}', RoleController::class . ':delete');
        })->add(new AdminMiddleware(1)); // Super Admin level required

        // Organization Management (Admin/Super Admin)
        $group->group('/organizations', function (Group $subGroup) {
            $subGroup->get('', OrganizationController::class . ':index');
            $subGroup->get('/create', OrganizationController::class . ':showCreate');
            $subGroup->post('/create', OrganizationController::class . ':create');
            $subGroup->get('/{id}', OrganizationController::class . ':show');
            $subGroup->get('/{id}/edit', OrganizationController::class . ':showEdit');
            $subGroup->post('/{id}/edit', OrganizationController::class . ':update');
            $subGroup->delete('/{id}', OrganizationController::class . ':delete');
        })->add(new AdminMiddleware(2)); // Admin level required

        // Job Management (All admin roles)
        $group->group('/jobs', function (Group $subGroup) {
            $subGroup->get('', LowonganController::class . ':adminIndex');
            $subGroup->get('/create', LowonganController::class . ':showCreate');
            $subGroup->post('/create', LowonganController::class . ':create');
            $subGroup->get('/{id}', LowonganController::class . ':adminShow');
            $subGroup->get('/{id}/edit', LowonganController::class . ':showEdit');
            $subGroup->post('/{id}/edit', LowonganController::class . ':update');
            $subGroup->delete('/{id}', LowonganController::class . ':delete');
            $subGroup->get('/{id}/applications', ApplyLowonganController::class . ':jobApplications');
        })->add(new AdminMiddleware(3)); // Recruiter level and above

        // Application Management (All admin roles)
        $group->group('/applications', function (Group $subGroup) {
            $subGroup->get('', ApplyLowonganController::class . ':adminIndex');
            $subGroup->get('/{id}', ApplyLowonganController::class . ':adminShow');
            $subGroup->post('/{id}/approve', ApplyLowonganController::class . ':approve');
            $subGroup->post('/{id}/reject', ApplyLowonganController::class . ':reject');
            $subGroup->delete('/{id}', ApplyLowonganController::class . ':delete');
        })->add(new AdminMiddleware(3)); // Recruiter level and above

        // Skill Management (Admin/Super Admin)
        $group->group('/skills', function (Group $subGroup) {
            $subGroup->get('', SkillController::class . ':index');
            $subGroup->get('/create', SkillController::class . ':showCreate');
            $subGroup->post('/create', SkillController::class . ':create');
            $subGroup->get('/{id}/edit', SkillController::class . ':showEdit');
            $subGroup->post('/{id}/edit', SkillController::class . ':update');
            $subGroup->delete('/{id}', SkillController::class . ':delete');
        })->add(new AdminMiddleware(2)); // Admin level required

    })->add(new AdminMiddleware(3)); // Base admin requirement

    // API Routes (for AJAX/JSON responses)
    $app->group('/api', function (Group $group) {

        // Public API endpoints
        $group->get('/jobs', LowonganController::class . ':apiIndex');
        $group->get('/jobs/{id}', LowonganController::class . ':apiShow');
        $group->get('/organizations', OrganizationController::class . ':apiIndex');

        // Protected API endpoints
        $group->group('/user', function (Group $subGroup) {
            $subGroup->get('/profile', ProfileController::class . ':apiShow');
            $subGroup->post('/profile', ProfileController::class . ':apiUpdate');
            $subGroup->get('/applications', ApplyLowonganController::class . ':apiMyApplications');
        })->add(AuthMiddleware::class);

        // Admin API endpoints
        $group->group('/admin', function (Group $subGroup) {
            $subGroup->get('/stats', DashboardController::class . ':apiStats');
            $subGroup->get('/users', UserController::class . ':apiIndex');
            $subGroup->get('/applications', ApplyLowonganController::class . ':apiAdminIndex');
        })->add(new AdminMiddleware(3));
    });
};
