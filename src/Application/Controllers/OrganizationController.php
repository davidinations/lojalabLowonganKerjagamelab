<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Models\OrganizationModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class OrganizationController
{
	private $db;
	private $view;
	private $organizationModel;

	public function __construct($db, Twig $view)
	{
		$this->db = $db;
		$this->view = $view;
		$this->organizationModel = new OrganizationModel($db);
	}

	// Show all organizations
	public function index(Request $request, Response $response): Response
	{
		$organizations = $this->organizationModel->getAllOrganizations();

		return $this->view->render($response, 'organization/index.twig', [
			'organizations' => $organizations
		]);
	}

	// Show single organization
	public function show(Request $request, Response $response, array $args): Response
	{
		$organizationId = (int) $args['id'];
		$organization = $this->organizationModel->findById($organizationId);

		if (!$organization) {
			return $response->withStatus(404);
		}

		$jobCount = $this->organizationModel->getJobCount($organizationId);

		return $this->view->render($response, 'organization/show.twig', [
			'organization' => $organization,
			'job_count' => $jobCount
		]);
	}

	// Show create organization form
	public function showCreate(Request $request, Response $response): Response
	{
		$locations = $this->db->select('tbl_locations', '*', ['archived' => 0]);

		return $this->view->render($response, 'organization/create.twig', [
			'locations' => $locations
		]);
	}

	// Create new organization
	public function create(Request $request, Response $response): Response
	{
		// Admin and Super Admin can create organizations
		if ($_SESSION['role_level'] > 2) {
			return $response->withStatus(403);
		}

		$data = $request->getParsedBody();

		$organizationData = [
			'nama' => $data['nama'],
			'email' => $data['email'],
			'bisnis_perusahaan' => $data['bisnis_perusahaan'],
			'id_locations' => !empty($data['id_locations']) ? (int) $data['id_locations'] : null,
			'create_id' => $_SESSION['user_id']
		];

		$organizationId = $this->organizationModel->create($organizationData);

		if ($organizationId) {
			return $response->withHeader('Location', '/admin/organizations')->withStatus(302);
		}

		$locations = $this->db->select('tbl_locations', '*', ['archived' => 0]);
		return $this->view->render($response, 'organization/create.twig', [
			'error' => 'Failed to create organization',
			'data' => $data,
			'locations' => $locations
		]);
	}

	// Show edit organization form
	public function showEdit(Request $request, Response $response, array $args): Response
	{
		$organizationId = (int) $args['id'];
		$organization = $this->organizationModel->findById($organizationId);

		if (!$organization) {
			return $response->withStatus(404);
		}

		$locations = $this->db->select('tbl_locations', '*', ['archived' => 0]);

		return $this->view->render($response, 'organization/edit.twig', [
			'organization' => $organization,
			'locations' => $locations
		]);
	}

	// Update organization
	public function update(Request $request, Response $response, array $args): Response
	{
		// Admin and Super Admin can update organizations
		if ($_SESSION['role_level'] > 2) {
			return $response->withStatus(403);
		}

		$organizationId = (int) $args['id'];
		$data = $request->getParsedBody();

		$updateData = [
			'nama' => $data['nama'],
			'email' => $data['email'],
			'bisnis_perusahaan' => $data['bisnis_perusahaan'],
			'id_locations' => !empty($data['id_locations']) ? (int) $data['id_locations'] : null,
			'update_id' => $_SESSION['user_id']
		];

		$success = $this->organizationModel->update($organizationId, $updateData);

		if ($success) {
			return $response->withHeader('Location', '/admin/organizations')->withStatus(302);
		}

		$organization = $this->organizationModel->findById($organizationId);
		$locations = $this->db->select('tbl_locations', '*', ['archived' => 0]);

		return $this->view->render($response, 'organization/edit.twig', [
			'organization' => $organization,
			'locations' => $locations,
			'error' => 'Failed to update organization'
		]);
	}

	// Delete organization
	public function delete(Request $request, Response $response, array $args): Response
	{
		// Only Super Admin can delete organizations
		if ($_SESSION['role_level'] != 1) {
			return $response->withStatus(403);
		}

		$organizationId = (int) $args['id'];

		$success = $this->organizationModel->delete($organizationId);

		if ($success) {
			return $response->withHeader('Location', '/admin/organizations')->withStatus(302);
		}

		return $response->withStatus(500);
	}

	// API endpoint for organizations list
	public function apiIndex(Request $request, Response $response): Response
	{
		$organizations = $this->organizationModel->getAllOrganizations();

		$response->getBody()->write(json_encode($organizations));
		return $response->withHeader('Content-Type', 'application/json');
	}
}
