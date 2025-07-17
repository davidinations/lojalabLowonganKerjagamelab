<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Models\RoleModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class RoleController
{
	private $db;
	private $view;
	private $roleModel;

	public function __construct($db, Twig $view)
	{
		$this->db = $db;
		$this->view = $view;
		$this->roleModel = new RoleModel($db);
	}

	// Show all roles
	public function index(Request $request, Response $response): Response
	{
		$roles = $this->roleModel->getAllRolesWithAdminCount();

		return $this->view->render($response, 'admin/roles/index.twig', [
			'roles' => $roles,
			'admin' => [
				'role_level' => $_SESSION['role_level'] ?? 1,
				'role_name' => $_SESSION['role_name'] ?? 'Super Admin'
			]
		]);
	}

	// Show create role form
	public function showCreate(Request $request, Response $response): Response
	{
		return $this->view->render($response, 'admin/roles/create.twig');
	}

	// Create new role
	public function create(Request $request, Response $response): Response
	{
		$data = $request->getParsedBody();

		// Validate required fields
		$required = ['nama', 'level'];
		$errors = [];

		foreach ($required as $field) {
			if (empty($data[$field])) {
				$errors[] = ucfirst($field) . ' is required';
			}
		}

		if (!empty($errors)) {
			return $this->view->render($response, 'admin/roles/create.twig', [
				'errors' => $errors,
				'data' => $data
			]);
		}


		$roleData = [
			'nama' => $data['nama'],
			'level' => (int) $data['level'],
			'create_id' => $_SESSION['user_id']
		];

		$roleId = $this->roleModel->create($roleData);

		if ($roleId) {
			return $response->withHeader('Location', '/admin/roles')->withStatus(302);
		}

		return $this->view->render($response, 'admin/roles/create.twig', [
			'error' => 'Failed to create role',
			'data' => $data
		]);
	}

	// Show edit role form
	public function showEdit(Request $request, Response $response, array $args): Response
	{
		$roleId = (int) $args['id'];
		$role = $this->roleModel->findById($roleId);

		if (!$role) {
			return $response->withStatus(404);
		}

		return $this->view->render($response, 'admin/roles/edit.twig', [
			'role' => $role
		]);
	}

	// Update role
	public function update(Request $request, Response $response, array $args): Response
	{
		$roleId = (int) $args['id'];
		$data = $request->getParsedBody();


		$updateData = [
			'nama' => $data['nama'],
			'level' => (int) $data['level'],
			'update_id' => $_SESSION['user_id']
		];

		$success = $this->roleModel->update($roleId, $updateData);

		if ($success) {
			return $response->withHeader('Location', '/admin/roles')->withStatus(302);
		}

		$role = $this->roleModel->findById($roleId);
		return $this->view->render($response, 'admin/roles/edit.twig', [
			'role' => $role,
			'error' => 'Failed to update role'
		]);
	}

	// Delete role
	public function delete(Request $request, Response $response, array $args): Response
	{
		$roleId = (int) $args['id'];

		$success = $this->roleModel->delete($roleId);

		if ($success) {
			return $response->withHeader('Location', '/admin/roles')->withStatus(302);
		}

		return $response->withStatus(500);
	}
}
