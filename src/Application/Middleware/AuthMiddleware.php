<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class AuthMiddleware implements Middleware
{
	/**
	 * {@inheritdoc}
	 */
	public function process(Request $request, RequestHandler $handler): Response
	{
		// Session is already started by SessionMiddleware

		// Check if user is logged in
		if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
			$response = new SlimResponse();
			return $response->withHeader('Location', '/login')->withStatus(302);
		}

		// Add user data to request attributes
		$request = $request->withAttribute('user_id', $_SESSION['user_id']);
		$request = $request->withAttribute('username', $_SESSION['username']);
		$request = $request->withAttribute('user_type', $_SESSION['user_type']);

		if (isset($_SESSION['role_id'])) {
			$request = $request->withAttribute('role_id', $_SESSION['role_id']);
			$request = $request->withAttribute('role_name', $_SESSION['role_name']);
			$request = $request->withAttribute('role_level', $_SESSION['role_level']);
		}

		return $handler->handle($request);
	}
}
