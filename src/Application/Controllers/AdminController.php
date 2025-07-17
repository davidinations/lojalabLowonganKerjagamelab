<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Models\AdminModel;
use App\Application\Models\RoleModel;
use App\Application\Models\OrganizationModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class AdminController
{
	private $db;
	private $view;
	private $adminModel;
	private $roleModel;
	private $organizationModel;

	public function __construct($db, Twig $view)
	{
		$this->db = $db;
		$this->view = $view;
		$this->adminModel = new AdminModel($db);
		$this->roleModel = new RoleModel($db);
		$this->organizationModel = new OrganizationModel($db);
	}

	// Show admin dashboard
	public function dashboard(Request $request, Response $response): Response
	{
		$adminId = $_SESSION['user_id'];

		$admin = $this->adminModel->findById($adminId);

		// Get dashboard statistics
		$stats = [
			'total_users' => $this->db->count('tbl_users', ['archived' => 0]),
			'total_jobs' => $this->db->count('tbl_lowongans', ['archived' => 0]),
			'total_applications' => $this->db->count('tbl_applyLowongans', ['archived' => 0]),
			'total_organizations' => $this->db->count('tbl_organizations', ['archived' => 0])
		];

		return $this->view->render($response, 'dashboard/admin.twig', [
			'admin' => $admin,
			'stats' => $stats
		]);
	}

	// Show all admins
	public function index(Request $request, Response $response): Response
	{
		$admins = $this->adminModel->getAllAdmins();

		return $this->view->render($response, 'admins/index.twig', [
			'admins' => $admins
		]);
	}

	// Show create admin form
	public function showCreate(Request $request, Response $response): Response
	{
		$roles = $this->roleModel->getAllRoles();
		$organizations = $this->organizationModel->getAllOrganizations();

		return $this->view->render($response, 'admins/create.twig', [
			'roles' => $roles,
			'organizations' => $organizations
		]);
	}

	// Create new admin
	public function create(Request $request, Response $response): Response
	{
		$data = $request->getParsedBody();

		// Validate required fields
		$required = ['username', 'email', 'password', 'id_role'];
		$errors = [];

		foreach ($required as $field) {
			if (empty($data[$field])) {
				$errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
			}
		}

		// Check if username or email already exists
		if ($this->adminModel->findByUsername($data['username'])) {
			$errors[] = 'Username already exists';
		}

		if ($this->adminModel->findByEmail($data['email'])) {
			$errors[] = 'Email already exists';
		}

		if (!empty($errors)) {
			$roles = $this->roleModel->getAllRoles();
			$organizations = $this->organizationModel->getAllOrganizations();

			return $this->view->render($response, 'admins/create.twig', [
				'errors' => $errors,
				'data' => $data,
				'roles' => $roles,
				'organizations' => $organizations
			]);
		}

		$adminData = [
			'username' => $data['username'],
			'email' => $data['email'],
			'password' => $data['password'],
			'id_role' => (int) $data['id_role'],
			'id_organizations' => !empty($data['id_organizations']) ? (int) $data['id_organizations'] : null,
			'create_id' => $_SESSION['user_id']
		];

		$adminId = $this->adminModel->create($adminData);

		if ($adminId) {
			return $response->withHeader('Location', '/admin/admins')->withStatus(302);
		}

		$roles = $this->roleModel->getAllRoles();
		$organizations = $this->organizationModel->getAllOrganizations();

		return $this->view->render($response, 'admins/create.twig', [
			'error' => 'Failed to create admin',
			'data' => $data,
			'roles' => $roles,
			'organizations' => $organizations
		]);
	}

	// Show edit admin form
	public function showEdit(Request $request, Response $response, array $args): Response
	{
		$adminId = (int) $args['id'];
		$admin = $this->adminModel->findById($adminId);

		if (!$admin) {
			return $response->withStatus(404);
		}

		$roles = $this->roleModel->getAllRoles();
		$organizations = $this->organizationModel->getAllOrganizations();

		return $this->view->render($response, 'admins/edit.twig', [
			'admin' => $admin,
			'roles' => $roles,
			'organizations' => $organizations
		]);
	}

	// Update admin
	public function update(Request $request, Response $response, array $args): Response
	{
		$adminId = (int) $args['id'];
		$data = $request->getParsedBody();

		$updateData = [
			'id_role' => (int) $data['id_role'],
			'id_organizations' => !empty($data['id_organizations']) ? (int) $data['id_organizations'] : null,
			'update_id' => $_SESSION['user_id']
		];

		// Only update password if provided
		if (!empty($data['password'])) {
			$updateData['password'] = $data['password'];
		}

		$success = $this->adminModel->update($adminId, $updateData);

		if ($success) {
			return $response->withHeader('Location', '/admin/admins')->withStatus(302);
		}

		$admin = $this->adminModel->findById($adminId);
		$roles = $this->roleModel->getAllRoles();
		$organizations = $this->organizationModel->getAllOrganizations();

		return $this->view->render($response, 'admins/edit.twig', [
			'admin' => $admin,
			'roles' => $roles,
			'organizations' => $organizations,
			'error' => 'Failed to update admin'
		]);
	}

	// Delete admin
	public function delete(Request $request, Response $response, array $args): Response
	{
		$adminId = (int) $args['id'];

		// Mark as archived instead of actual delete
		$success = $this->adminModel->update($adminId, [
			'archived' => 1,
			'update_time' => date('Y-m-d H:i:s')
		]);

		if ($success) {
			return $response->withHeader('Location', '/admin/admins')->withStatus(302);
		}

		return $response->withStatus(500);
	}
}
