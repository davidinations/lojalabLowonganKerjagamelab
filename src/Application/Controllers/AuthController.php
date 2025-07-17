<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Models\UserModel;
use App\Application\Models\AdminModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class AuthController
{
	private $db;
	private $view;
	private $userModel;
	private $adminModel;

	public function __construct($db, Twig $view)
	{
		$this->db = $db;
		$this->view = $view;
		$this->userModel = new UserModel($db);
		$this->adminModel = new AdminModel($db);
	}

	// Show login form
	public function showLogin(Request $request, Response $response): Response
	{
		return $this->view->render($response, 'auth/login.twig');
	}

	// Handle user login
	public function login(Request $request, Response $response): Response
	{
		$data = $request->getParsedBody();
		$username = $data['username'] ?? '';
		$password = $data['password'] ?? '';
		$userType = $data['user_type'] ?? 'user'; // 'user' or 'admin'

		if ($userType === 'admin') {
			$user = $this->adminModel->findByUsername($username);
			$redirectRoute = '/admin/dashboard';
		} else {
			$user = $this->userModel->findByUsername($username);
			$redirectRoute = '/dashboard';
		}

		if ($user && password_verify($password, $user['password'])) {
			// Store user data in session
			$_SESSION['user_id'] = $user['id'];
			$_SESSION['username'] = $user['username'];
			$_SESSION['user_type'] = $userType;

			if ($userType === 'admin') {
				$_SESSION['role_id'] = $user['id_role'];
				$_SESSION['role_name'] = $user['role_name'];
				$_SESSION['role_level'] = $user['role_level'];
				$_SESSION['organization_id'] = $user['id_organizations'];
			}

			return $response->withHeader('Location', $redirectRoute)->withStatus(302);
		}

		// Login failed
		return $this->view->render($response, 'auth/login.twig', [
			'error' => 'Invalid username or password',
			'username' => $username
		]);
	}

	// Show registration form
	public function showRegister(Request $request, Response $response): Response
	{
		return $this->view->render($response, 'auth/register.twig');
	}

	// Handle user registration
	public function register(Request $request, Response $response): Response
	{
		$data = $request->getParsedBody();

		// Validate required fields
		$required = ['username', 'email', 'password', 'confirm_password'];
		$errors = [];

		foreach ($required as $field) {
			if (empty($data[$field])) {
				$errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
			}
		}

		if ($data['password'] !== $data['confirm_password']) {
			$errors[] = 'Passwords do not match';
		}

		// Check if username or email already exists
		if ($this->userModel->findByUsername($data['username'])) {
			$errors[] = 'Username already exists';
		}

		if ($this->userModel->findByEmail($data['email'])) {
			$errors[] = 'Email already exists';
		}

		if (!empty($errors)) {
			return $this->view->render($response, 'auth/register.twig', [
				'errors' => $errors,
				'data' => $data
			]);
		}

		// Create new user
		$userData = [
			'username' => $data['username'],
			'email' => $data['email'],
			'password' => $data['password'],
			'tempat_lahir' => $data['tempat_lahir'] ?? '',
			'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
			'agama' => $data['agama'] ?? '',
			'gender' => $data['gender'] ?? '',
			'pendidikan_terakhir' => $data['pendidikan_terakhir'] ?? '',
			'tempat_pendidikan_terakhir' => $data['tempat_pendidikan_terakhir'] ?? '',
			'jurusan_pendidikan_terakhir' => $data['jurusan_pendidikan_terakhir'] ?? '',
			'create_id' => 1
		];

		$userId = $this->userModel->create($userData);

		if ($userId) {
			// Auto login after registration
			$_SESSION['user_id'] = $userId;
			$_SESSION['username'] = $data['username'];
			$_SESSION['user_type'] = 'user';

			return $response->withHeader('Location', '/dashboard')->withStatus(302);
		}

		return $this->view->render($response, 'auth/register.twig', [
			'error' => 'Registration failed. Please try again.',
			'data' => $data
		]);
	}

	// Handle logout
	public function logout(Request $request, Response $response): Response
	{
		session_destroy();

		return $response->withHeader('Location', '/login')->withStatus(302);
	}

	// Show forgot password form
	public function showForgotPassword(Request $request, Response $response): Response
	{
		return $this->view->render($response, 'auth/forgot-password.twig');
	}

	// Handle forgot password
	public function forgotPassword(Request $request, Response $response): Response
	{
		$data = $request->getParsedBody();
		$email = $data['email'] ?? '';

		$user = $this->userModel->findByEmail($email);

		if ($user) {
			// In a real application, you would send an email with reset link
			// For now, just show success message
			return $this->view->render($response, 'auth/forgot-password.twig', [
				'success' => 'Password reset instructions have been sent to your email.'
			]);
		}

		return $this->view->render($response, 'auth/forgot-password.twig', [
			'error' => 'No account found with that email address.'
		]);
	}
}
