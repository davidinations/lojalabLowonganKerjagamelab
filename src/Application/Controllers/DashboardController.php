<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Models\LowonganModel;
use App\Application\Models\OrganizationModel;
use App\Application\Models\RoleModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class DashboardController
{
	private $db;
	private $view;

	public function __construct($db, Twig $view)
	{
		$this->db = $db;
		$this->view = $view;
	}

	// API endpoint for dashboard statistics
	public function apiStats(Request $request, Response $response): Response
	{
		$stats = [
			'total_users' => $this->db->count('tbl_users', ['archived' => 0]),
			'total_jobs' => $this->db->count('tbl_lowongans', ['archived' => 0]),
			'total_applications' => $this->db->count('tbl_applyLowongans', ['archived' => 0]),
			'total_organizations' => $this->db->count('tbl_organizations', ['archived' => 0]),
			'pending_applications' => $this->db->count('tbl_applyLowongans', [
				'archived' => 0,
				'status' => 'pending'
			]),
			'recent_jobs' => $this->db->select('tbl_lowongans', [
				'[>]tbl_organizations' => ['id_organizations' => 'id']
			], [
				'tbl_lowongans.id',
				'tbl_lowongans.title',
				'tbl_lowongans.create_time',
				'tbl_organizations.nama(company_name)'
			], [
				'tbl_lowongans.archived' => 0,
				'ORDER' => ['tbl_lowongans.create_time' => 'DESC'],
				'LIMIT' => 5
			])
		];

		$response->getBody()->write(json_encode($stats));
		return $response->withHeader('Content-Type', 'application/json');
	}
}
