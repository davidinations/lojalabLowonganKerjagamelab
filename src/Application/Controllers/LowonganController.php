<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Models\LowonganModel;
use App\Application\Models\OrganizationModel;
use App\Application\Models\RoleModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class LowonganController
{
	private $db;
	private $view;
	private $lowonganModel;
	private $organizationModel;

	public function __construct($db, Twig $view)
	{
		$this->db = $db;
		$this->view = $view;
		$this->lowonganModel = new LowonganModel($db);
		$this->organizationModel = new OrganizationModel($db);
	}

	// Public job listings
	public function index(Request $request, Response $response): Response
	{
		$jobs = $this->lowonganModel->getAllJobs();

		return $this->view->render($response, 'jobs/index.twig', [
			'jobs' => $jobs
		]);
	}

	// Show single job
	public function show(Request $request, Response $response, array $args): Response
	{
		$jobId = (int) $args['id'];
		$job = $this->lowonganModel->findById($jobId);

		if (!$job) {
			return $response->withStatus(404);
		}

		return $this->view->render($response, 'jobs/show.twig', [
			'job' => $job
		]);
	}

	// Admin job listings
	public function adminIndex(Request $request, Response $response): Response
	{


		// If admin belongs to specific organization, only show their jobs
		if (isset($_SESSION['organization_id']) && $_SESSION['organization_id']) {
			$jobs = $this->lowonganModel->getJobsByOrganization($_SESSION['organization_id']);
		} else {
			$jobs = $this->lowonganModel->getAllJobs();
		}

		return $this->view->render($response, 'admin/jobs/index.twig', [
			'jobs' => $jobs
		]);
	}

	// Admin show job
	public function adminShow(Request $request, Response $response, array $args): Response
	{
		$jobId = (int) $args['id'];
		$job = $this->lowonganModel->findById($jobId);

		if (!$job) {
			return $response->withStatus(404);
		}

		return $this->view->render($response, 'admin/jobs/show.twig', [
			'job' => $job
		]);
	}

	// Show create job form
	public function showCreate(Request $request, Response $response): Response
	{
		// Filter organizations based on role
		if ($_SESSION['role_level'] == 3) { // Recruiter
			// Recruiters can only see their own organization
			$organizations = $_SESSION['organization_id'] ?
				[$this->organizationModel->findById($_SESSION['organization_id'])] : [];
		} else {
			// Admin and Super Admin can see all organizations
			$organizations = $this->organizationModel->getAllOrganizations();
		}

		$categories = $this->db->select('tbl_typeLowongans', '*', ['archived' => 0]);
		$locations = $this->db->select('tbl_locations', '*', ['archived' => 0]);

		return $this->view->render($response, 'admin/jobs/create.twig', [
			'organizations' => $organizations,
			'categories' => $categories,
			'locations' => $locations
		]);
	}

	// Create new job
	public function create(Request $request, Response $response): Response
	{
		$data = $request->getParsedBody();

		// Check role-based organization restrictions
		if ($_SESSION['role_level'] == 3) { // Recruiter
			// Recruiters can only post jobs for their own organization
			if (!isset($_SESSION['organization_id']) || $_SESSION['organization_id'] != $data['id_organizations']) {
				return $response->withStatus(403);
			}
		}
		// Admin (level 2) and Super Admin (level 1) can create jobs for any organization

		$jobData = [
			'id_organizations' => (int) $data['id_organizations'],
			'id_typeLowongans' => (int) $data['id_typeLowongans'],
			'id_locations' => (int) $data['id_locations'],
			'title' => $data['title'],
			'jenis_pekerjaan' => (int) $data['jenis_pekerjaan'],
			'deskripsi' => $data['deskripsi'],
			'create_id' => $_SESSION['user_id']
		];

		$jobId = $this->lowonganModel->create($jobData);

		if ($jobId) {
			return $response->withHeader('Location', '/admin/jobs')->withStatus(302);
		}

		return $response->withStatus(500);
	}

	// Show edit job form
	public function showEdit(Request $request, Response $response, array $args): Response
	{
		$jobId = (int) $args['id'];
		$job = $this->lowonganModel->findById($jobId);

		if (!$job) {
			return $response->withStatus(404);
		}

		$organizations = $this->organizationModel->getAllOrganizations();
		$categories = $this->db->select('tbl_typeLowongans', '*', ['archived' => 0]);
		$locations = $this->db->select('tbl_locations', '*', ['archived' => 0]);

		return $this->view->render($response, 'admin/jobs/edit.twig', [
			'job' => $job,
			'organizations' => $organizations,
			'categories' => $categories,
			'locations' => $locations
		]);
	}

	// Update job
	public function update(Request $request, Response $response, array $args): Response
	{
		$jobId = (int) $args['id'];
		$data = $request->getParsedBody();

		// Check if job exists and get current data
		$existingJob = $this->lowonganModel->findById($jobId);
		if (!$existingJob) {
			return $response->withStatus(404);
		}

		// Check role-based organization restrictions
		if ($_SESSION['role_level'] == 3) { // Recruiter
			// Recruiters can only edit jobs from their own organization
			if (
				!isset($_SESSION['organization_id']) ||
				$existingJob['id_organizations'] != $_SESSION['organization_id'] ||
				$data['id_organizations'] != $_SESSION['organization_id']
			) {
				return $response->withStatus(403);
			}
		}
		// Admin (level 2) and Super Admin (level 1) can edit any job

		$updateData = [
			'id_organizations' => (int) $data['id_organizations'],
			'id_typeLowongans' => (int) $data['id_typeLowongans'],
			'id_locations' => (int) $data['id_locations'],
			'title' => $data['title'],
			'jenis_pekerjaan' => (int) $data['jenis_pekerjaan'],
			'deskripsi' => $data['deskripsi'],
			'update_id' => $_SESSION['user_id']
		];

		$success = $this->lowonganModel->update($jobId, $updateData);

		if ($success) {
			return $response->withHeader('Location', '/admin/jobs')->withStatus(302);
		}

		return $response->withStatus(500);
	}

	// Delete job
	public function delete(Request $request, Response $response, array $args): Response
	{
		$jobId = (int) $args['id'];

		// Check if job exists and get current data
		$existingJob = $this->lowonganModel->findById($jobId);
		if (!$existingJob) {
			return $response->withStatus(404);
		}

		// Check role-based restrictions
		if ($_SESSION['role_level'] == 3) { // Recruiter
			// Recruiters can only delete jobs from their own organization
			if (
				!isset($_SESSION['organization_id']) ||
				$existingJob['id_organizations'] != $_SESSION['organization_id']
			) {
				return $response->withStatus(403);
			}
		}
		// Admin (level 2) and Super Admin (level 1) can delete any job

		$success = $this->lowonganModel->delete($jobId);

		if ($success) {
			return $response->withHeader('Location', '/admin/jobs')->withStatus(302);
		}

		return $response->withStatus(500);
	}

	// Search jobs
	public function search(Request $request, Response $response): Response
	{
		$queryParams = $request->getQueryParams();
		$filters = [
			'keyword' => $queryParams['keyword'] ?? '',
			'category' => $queryParams['category'] ?? '',
			'location' => $queryParams['location'] ?? ''
		];

		$jobs = $this->lowonganModel->searchJobs($filters);

		return $this->view->render($response, 'jobs/search.twig', [
			'jobs' => $jobs,
			'filters' => $filters
		]);
	}

	// API endpoints
	public function apiIndex(Request $request, Response $response): Response
	{
		$jobs = $this->lowonganModel->getAllJobs();

		$response->getBody()->write(json_encode($jobs));
		return $response->withHeader('Content-Type', 'application/json');
	}

	public function apiShow(Request $request, Response $response, array $args): Response
	{
		$jobId = (int) $args['id'];
		$job = $this->lowonganModel->findById($jobId);

		if (!$job) {
			$response->getBody()->write(json_encode(['error' => 'Job not found']));
			return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
		}

		$response->getBody()->write(json_encode($job));
		return $response->withHeader('Content-Type', 'application/json');
	}
}
