<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class AdminMiddleware implements Middleware
{
	private $requiredLevel;

	public function __construct(int $requiredLevel = 3) // Default to recruiter level
	{
		$this->requiredLevel = $requiredLevel;
	}

	/**
	 * {@inheritdoc}
	 */
	public function process(Request $request, RequestHandler $handler): Response
	{
		// Session is already started by SessionMiddleware

		// Check if user is logged in and is an admin
		if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
			$response = new SlimResponse();
			return $response->withHeader('Location', '/login')->withStatus(302);
		}

		// Check role level (lower number = higher authority)
		// 1 = Super Admin, 2 = Admin, 3 = Recruiter
		if (!isset($_SESSION['role_level']) || $_SESSION['role_level'] > $this->requiredLevel) {
			$response = new SlimResponse();
			return $response->withStatus(403);
		}

		return $handler->handle($request);
	}
}
