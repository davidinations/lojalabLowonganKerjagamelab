<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Models\ApplyLowonganModel;
use App\Application\Models\LowonganModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ApplyLowonganController
{
	private $db;
	private $view;
	private $applyLowonganModel;
	private $lowonganModel;

	public function __construct($db, Twig $view)
	{
		$this->db = $db;
		$this->view = $view;
		$this->applyLowonganModel = new ApplyLowonganModel($db);
		$this->lowonganModel = new LowonganModel($db);
	}

	// Show apply form
	public function showApply(Request $request, Response $response, array $args): Response
	{
		$jobId = (int) $args['id'];
		$job = $this->lowonganModel->findById($jobId);

		if (!$job) {
			return $response->withStatus(404);
		}



		// Check if user already applied
		if ($this->applyLowonganModel->checkExistingApplication($_SESSION['user_id'], $jobId)) {
			return $this->view->render($response, 'applyjobs/already-applied.twig', [
				'job' => $job
			]);
		}

		return $this->view->render($response, 'applyjobs/apply.twig', [
			'job' => $job
		]);
	}

	// Submit application
	public function apply(Request $request, Response $response, array $args): Response
	{
		$jobId = (int) $args['id'];
		$data = $request->getParsedBody();


		$userId = $_SESSION['user_id'];

		// Check if user already applied
		if ($this->applyLowonganModel->checkExistingApplication($userId, $jobId)) {
			return $response->withStatus(400);
		}

		// Handle file upload for CV
		$cvDocument = '';
		$uploadedFiles = $request->getUploadedFiles();
		if (isset($uploadedFiles['cv_document'])) {
			$uploadedFile = $uploadedFiles['cv_document'];
			if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
				$filename = moveUploadedFile(__DIR__ . '/../../../public/uploads/cv', $uploadedFile);
				$cvDocument = $filename;
			}
		}

		$applicationData = [
			'id_user' => $userId,
			'id_lowongans' => $jobId,
			'cv_document' => $cvDocument,
			'pesan' => $data['pesan'] ?? '',
			'create_id' => $userId
		];

		$applicationId = $this->applyLowonganModel->create($applicationData);

		if ($applicationId) {
			return $response->withHeader('Location', '/my-applications')->withStatus(302);
		}

		return $response->withStatus(500);
	}

	// Show user's applications
	public function myApplications(Request $request, Response $response): Response
	{

		$applications = $this->applyLowonganModel->findByUserId($_SESSION['user_id']);

		return $this->view->render($response, 'applyjobs/my-applications.twig', [
			'applications' => $applications
		]);
	}

	// Admin: Show all applications
	public function adminIndex(Request $request, Response $response): Response
	{
		$applications = $this->applyLowonganModel->getAllApplications();

		return $this->view->render($response, 'admin/applications/index.twig', [
			'applications' => $applications
		]);
	}

	// Admin: Show single application
	public function adminShow(Request $request, Response $response, array $args): Response
	{
		$applicationId = (int) $args['id'];
		$application = $this->applyLowonganModel->findById($applicationId);

		if (!$application) {
			return $response->withStatus(404);
		}

		return $this->view->render($response, 'admin/applications/show.twig', [
			'application' => $application
		]);
	}

	// Show applications for specific job
	public function jobApplications(Request $request, Response $response, array $args): Response
	{
		$jobId = (int) $args['id'];
		$job = $this->lowonganModel->findById($jobId);
		$applications = $this->applyLowonganModel->findByJobId($jobId);

		return $this->view->render($response, 'admin/jobs/applications.twig', [
			'job' => $job,
			'applications' => $applications
		]);
	}

	// Approve application
	public function approve(Request $request, Response $response, array $args): Response
	{
		$applicationId = (int) $args['id'];


		$success = $this->applyLowonganModel->update($applicationId, [
			'status' => 'approved',
			'update_id' => $_SESSION['user_id']
		]);

		if ($success) {
			return $response->withHeader('Location', '/admin/applications')->withStatus(302);
		}

		return $response->withStatus(500);
	}

	// Reject application
	public function reject(Request $request, Response $response, array $args): Response
	{
		$applicationId = (int) $args['id'];


		$success = $this->applyLowonganModel->update($applicationId, [
			'status' => 'rejected',
			'update_id' => $_SESSION['user_id']
		]);

		if ($success) {
			return $response->withHeader('Location', '/admin/applications')->withStatus(302);
		}

		return $response->withStatus(500);
	}

	// Delete application
	public function delete(Request $request, Response $response, array $args): Response
	{
		$applicationId = (int) $args['id'];

		$success = $this->applyLowonganModel->delete($applicationId);

		if ($success) {
			return $response->withHeader('Location', '/admin/applications')->withStatus(302);
		}

		return $response->withStatus(500);
	}

	// API endpoints
	public function apiMyApplications(Request $request, Response $response): Response
	{

		$applications = $this->applyLowonganModel->findByUserId($_SESSION['user_id']);

		$response->getBody()->write(json_encode($applications));
		return $response->withHeader('Content-Type', 'application/json');
	}

	public function apiAdminIndex(Request $request, Response $response): Response
	{
		$applications = $this->applyLowonganModel->getAllApplications();

		$response->getBody()->write(json_encode($applications));
		return $response->withHeader('Content-Type', 'application/json');
	}
}

// Helper function for file upload
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
