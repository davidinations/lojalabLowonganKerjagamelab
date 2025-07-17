<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\Models\RoleModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class PermissionMiddleware implements Middleware
{
	private $requiredPermission;
	private $roleModel;

	public function __construct(string $requiredPermission, $db)
	{
		$this->requiredPermission = $requiredPermission;
		$this->roleModel = new RoleModel($db);
	}

	/**
	 * {@inheritdoc}
	 */
	public function process(Request $request, RequestHandler $handler): Response
	{
		// Session is already started by SessionMiddleware

		// Check if user is logged in
		if (!isset($_SESSION['user_id'])) {
			$response = new SlimResponse();
			return $response->withHeader('Location', '/login')->withStatus(302);
		}

		// Regular users have limited permissions
		if ($_SESSION['user_type'] === 'user') {
			$userPermissions = ['profile.view', 'profile.edit', 'jobs.view', 'jobs.apply'];

			if (!in_array($this->requiredPermission, $userPermissions)) {
				$response = new SlimResponse();
				return $response->withStatus(403);
			}
		} else {
			// Admin users - check role-based permissions
			if (!isset($_SESSION['role_level'])) {
				$response = new SlimResponse();
				return $response->withStatus(403);
			}

			$permissions = $this->roleModel->getRolePermissions($_SESSION['role_level']);

			if (!in_array($this->requiredPermission, $permissions)) {
				$response = new SlimResponse();
				return $response->withStatus(403);
			}
		}

		return $handler->handle($request);
	}
}
