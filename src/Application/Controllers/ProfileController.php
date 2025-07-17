<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Models\UserModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ProfileController
{
	private $db;
	private $view;
	private $userModel;

	public function __construct($db, Twig $view)
	{
		$this->db = $db;
		$this->view = $view;
		$this->userModel = new UserModel($db);
	}

	// Show current user's profile
	public function show(Request $request, Response $response): Response
	{

		$user = $this->userModel->findById($_SESSION['user_id']);

		return $this->view->render($response, 'profiles/show.twig', [
			'user' => $user
		]);
	}

	// Show edit profile form
	public function showEdit(Request $request, Response $response): Response
	{

		$user = $this->userModel->findById($_SESSION['user_id']);

		return $this->view->render($response, 'profiles/edit.twig', [
			'user' => $user
		]);
	}

	// Update profile
	public function update(Request $request, Response $response): Response
	{

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

		// Handle profile photo upload
		$uploadedFiles = $request->getUploadedFiles();
		if (isset($uploadedFiles['foto_pribadi'])) {
			$uploadedFile = $uploadedFiles['foto_pribadi'];
			if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
				$filename = moveUploadedFile(__DIR__ . '/../../../public/uploads/profiles', $uploadedFile);
				$updateData['foto_pribadi'] = $filename;
			}
		}

		$success = $this->userModel->update($_SESSION['user_id'], $updateData);

		if ($success) {
			return $response->withHeader('Location', '/profile')->withStatus(302);
		}

		$user = $this->userModel->findById($_SESSION['user_id']);
		return $this->view->render($response, 'profiles/edit.twig', [
			'user' => $user,
			'error' => 'Failed to update profile'
		]);
	}

	// API endpoints
	public function apiShow(Request $request, Response $response): Response
	{

		$user = $this->userModel->findById($_SESSION['user_id']);

		// Remove sensitive data
		unset($user['password']);

		$response->getBody()->write(json_encode($user));
		return $response->withHeader('Content-Type', 'application/json');
	}

	public function apiUpdate(Request $request, Response $response): Response
	{

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

		$success = $this->userModel->update($_SESSION['user_id'], $updateData);

		$result = [
			'success' => $success,
			'message' => $success ? 'Profile updated successfully' : 'Failed to update profile'
		];

		$response->getBody()->write(json_encode($result));
		return $response->withHeader('Content-Type', 'application/json');
	}
}

// Helper function for file upload (if not already defined)
if (!function_exists('moveUploadedFile')) {
	function moveUploadedFile($directory, $uploadedFile)
	{
		$extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
		$basename = bin2hex(random_bytes(8));
		$filename = sprintf('%s.%0.8s', $basename, $extension);

		if (!is_dir($directory)) {
			mkdir($directory, 0755, true);
		}

		$uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

		return $filename;
	}
}
