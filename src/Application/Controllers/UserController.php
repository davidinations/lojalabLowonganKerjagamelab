<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Models\UserModel;
use App\Application\Models\ApplyLowonganModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class UserController
{
	private $db;
	private $view;
	private $userModel;
	private $applyLowonganModel;

	public function __construct($db, Twig $view)
	{
		$this->db = $db;
		$this->view = $view;
		$this->userModel = new UserModel($db);
		$this->applyLowonganModel = new ApplyLowonganModel($db);
	}

	// Show user dashboard
	public function dashboard(Request $request, Response $response): Response
	{
		$userId = $_SESSION['user_id'];

		$user = $this->userModel->findById($userId);
		$applications = $this->applyLowonganModel->findByUserId($userId);

		return $this->view->render($response, 'dashboard/user.twig', [
			'user' => $user,
			'applications' => $applications
		]);
	}

	// Show all users (for admin)
	public function index(Request $request, Response $response): Response
	{
		$users = $this->userModel->getAllUsers();

		return $this->view->render($response, 'users/index.twig', [
			'users' => $users
		]);
	}

	// Show user profile
	public function profile(Request $request, Response $response, array $args): Response
	{
		$userId = (int) $args['id'];
		$user = $this->userModel->findById($userId);

		if (!$user) {
			return $response->withStatus(404);
		}

		return $this->view->render($response, 'users/profile.twig', [
			'user' => $user
		]);
	}

	// Show edit profile form
	public function showEdit(Request $request, Response $response, array $args): Response
	{
		$userId = (int) $args['id'];

		// Check permissions for admin user editing
		if ($_SESSION['user_type'] === 'admin') {
			// Only Super Admin can edit users via admin panel
			if ($_SESSION['role_level'] != 1) {
				return $response->withStatus(403);
			}
		} else {
			// Regular users can only edit their own profile
			if ($_SESSION['user_id'] !== $userId) {
				return $response->withStatus(403);
			}
		}

		$user = $this->userModel->findById($userId);

		if (!$user) {
			return $response->withStatus(404);
		}

		return $this->view->render($response, 'users/edit.twig', [
			'user' => $user
		]);
	}

	// Update user profile
	public function update(Request $request, Response $response, array $args): Response
	{
		$userId = (int) $args['id'];

		// Check permissions for admin user editing
		if ($_SESSION['user_type'] === 'admin') {
			// Only Super Admin can edit users via admin panel
			if ($_SESSION['role_level'] != 1) {
				return $response->withStatus(403);
			}
		} else {
			// Regular users can only edit their own profile
			if ($_SESSION['user_id'] !== $userId) {
				return $response->withStatus(403);
			}
		}

		$data = $request->getParsedBody();

		$updateData = [
			'tempat_lahir' => $data['tempat_lahir'] ?? '',
			'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
			'agama' => $data['agama'] ?? '',
			'gender' => $data['gender'] ?? '',
			'pendidikan_terakhir' => $data['pendidikan_terakhir'] ?? '',
			'tempat_pendidikan_terakhir' => $data['tempat_pendidikan_terakhir'] ?? '',
			'jurusan_pendidikan_terakhir' => $data['jurusan_pendidikan_terakhir'] ?? '',
			'update_id' => $_SESSION['user_id']
		];

		$success = $this->userModel->update($userId, $updateData);

		if ($success) {
			return $response->withHeader('Location', "/users/{$userId}/profile")->withStatus(302);
		}

		$user = $this->userModel->findById($userId);
		return $this->view->render($response, 'users/edit.twig', [
			'user' => $user,
			'error' => 'Failed to update profile'
		]);
	}

	// Delete user (admin only)
	public function delete(Request $request, Response $response, array $args): Response
	{
		$userId = (int) $args['id'];

		// Mark as archived instead of actual delete
		$success = $this->userModel->update($userId, [
			'archived' => 1,
			'update_time' => date('Y-m-d H:i:s')
		]);

		if ($success) {
			return $response->withHeader('Location', '/admin/users')->withStatus(302);
		}

		return $response->withStatus(500);
	}

	// Show create user form (admin only)
	public function showCreate(Request $request, Response $response): Response
	{
		// Only Admin and Super Admin can create users
		if ($_SESSION['role_level'] > 2) {
			return $response->withStatus(403);
		}

		return $this->view->render($response, 'users/create.twig');
	}

	// Create new user (admin only)
	public function create(Request $request, Response $response): Response
	{
		// Only Admin and Super Admin can create users
		if ($_SESSION['role_level'] > 2) {
			return $response->withStatus(403);
		}

		$data = $request->getParsedBody();

		// Validate required fields
		$required = ['username', 'email', 'password'];
		foreach ($required as $field) {
			if (empty($data[$field])) {
				return $this->view->render($response, 'users/create.twig', [
					'error' => "Field '$field' is required",
					'data' => $data
				]);
			}
		}

		// Check if username or email already exists
		if ($this->userModel->findByUsername($data['username'])) {
			return $this->view->render($response, 'users/create.twig', [
				'error' => 'Username already exists',
				'data' => $data
			]);
		}

		if ($this->userModel->findByEmail($data['email'])) {
			return $this->view->render($response, 'users/create.twig', [
				'error' => 'Email already exists',
				'data' => $data
			]);
		}

		$userData = [
			'username' => $data['username'],
			'email' => $data['email'],
			'password' => password_hash($data['password'], PASSWORD_DEFAULT),
			'tempat_lahir' => $data['tempat_lahir'] ?? '',
			'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
			'agama' => $data['agama'] ?? '',
			'gender' => $data['gender'] ?? '',
			'pendidikan_terakhir' => $data['pendidikan_terakhir'] ?? '',
			'tempat_pendidikan_terakhir' => $data['tempat_pendidikan_terakhir'] ?? '',
			'jurusan_pendidikan_terakhir' => $data['jurusan_pendidikan_terakhir'] ?? '',
			'create_time' => date('Y-m-d H:i:s'),
			'create_id' => $_SESSION['user_id']
		];

		$userId = $this->userModel->create($userData);

		if ($userId) {
			return $response->withHeader('Location', '/admin/users')->withStatus(302);
		}

		return $this->view->render($response, 'users/create.twig', [
			'error' => 'Failed to create user',
			'data' => $data
		]);
	}
}
