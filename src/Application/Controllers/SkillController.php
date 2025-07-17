<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class SkillController
{
	private $db;
	private $view;

	public function __construct($db, Twig $view)
	{
		$this->db = $db;
		$this->view = $view;
	}

	// Show all skills
	public function index(Request $request, Response $response): Response
	{
		$skills = $this->db->select('tbl_skills', '*', [
			'archived' => 0,
			'ORDER' => ['nama' => 'ASC']
		]);

		return $this->view->render($response, 'skills/index.twig', [
			'skills' => $skills
		]);
	}

	// Show create skill form
	public function showCreate(Request $request, Response $response): Response
	{
		return $this->view->render($response, 'skills/create.twig');
	}

	// Create new skill
	public function create(Request $request, Response $response): Response
	{
		$data = $request->getParsedBody();

		if (empty($data['nama'])) {
			return $this->view->render($response, 'skills/create.twig', [
				'error' => 'Skill name is required',
				'data' => $data
			]);
		}


		$skillData = [
			'nama' => $data['nama'],
			'created_time' => date('Y-m-d H:i:s'),
			'create_id' => $_SESSION['user_id'],
			'archived' => 0
		];

		$result = $this->db->insert('tbl_skills', $skillData);

		if ($result) {
			return $response->withHeader('Location', '/admin/skills')->withStatus(302);
		}

		return $this->view->render($response, 'skills/create.twig', [
			'error' => 'Failed to create skill',
			'data' => $data
		]);
	}

	// Show edit skill form
	public function showEdit(Request $request, Response $response, array $args): Response
	{
		$skillId = (int) $args['id'];
		$skill = $this->db->get('tbl_skills', '*', [
			'id' => $skillId,
			'archived' => 0
		]);

		if (!$skill) {
			return $response->withStatus(404);
		}

		return $this->view->render($response, 'skills/edit.twig', [
			'skill' => $skill
		]);
	}

	// Update skill
	public function update(Request $request, Response $response, array $args): Response
	{
		$skillId = (int) $args['id'];
		$data = $request->getParsedBody();

		if (empty($data['nama'])) {
			$skill = $this->db->get('tbl_skills', '*', ['id' => $skillId]);
			return $this->view->render($response, 'skills/edit.twig', [
				'skill' => $skill,
				'error' => 'Skill name is required'
			]);
		}


		$updateData = [
			'nama' => $data['nama'],
			'update_time' => date('Y-m-d H:i:s'),
			'update_id' => $_SESSION['user_id']
		];

		$result = $this->db->update('tbl_skills', $updateData, ['id' => $skillId]);

		if ($result->rowCount() > 0) {
			return $response->withHeader('Location', '/admin/skills')->withStatus(302);
		}

		$skill = $this->db->get('tbl_skills', '*', ['id' => $skillId]);
		return $this->view->render($response, 'skills/edit.twig', [
			'skill' => $skill,
			'error' => 'Failed to update skill'
		]);
	}

	// Delete skill
	public function delete(Request $request, Response $response, array $args): Response
	{
		$skillId = (int) $args['id'];

		$result = $this->db->update('tbl_skills', [
			'archived' => 1,
			'update_time' => date('Y-m-d H:i:s')
		], ['id' => $skillId]);

		if ($result->rowCount() > 0) {
			return $response->withHeader('Location', '/admin/skills')->withStatus(302);
		}

		return $response->withStatus(500);
	}
}
