<?php

declare(strict_types=1);

use App\Application\Actions\Book\ListBooksAction;
use App\Application\Actions\Book\ViewBookAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $db = require_once "sql/connect.php";

        if(is_string($db)) {
            $response->getBody()->write('Hello Floflo - Erreur DB : '.$db);
        } else {
            $response->getBody()->write('Hello Floflo - Connexion réussie !');
        }
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    $app->group('/books', function (Group $group) {
        $group->get('', ListBooksAction::class);
        $group->get('/{id}', ViewBookAction::class);
    });
    $app->get('/login', function (Request $request, Response $response) {
        $data = ['message' => "Ici s'affichera la page de connexion"];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    });
    $app->get('/register', function (Request $request, Response $response) {
        $data = ['message' => "Ici s'affichera la page d'inscription"];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get("/check-auth", function (Request $request, Response $response) {
        require_once "auth/JwtManager.php";

        $authHeader = $request->getHeaderLine('Authorization');
        $jwtManager = new JwtManager();

        if (empty($authHeader)) {
            $data = ['authenticated' => false, 'message' => 'Token manquant'];
            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $token = $jwtManager->extractTokenFromHeader($authHeader);
        $userData = $jwtManager->validateToken($token);

        if ($userData) {
            $data = [
                'authenticated' => true,
                'user' => [
                    'id' => $userData['user_id'],
                    'email' => $userData['email']
                ]
            ];
        } else {
            $data = ['authenticated' => false, 'message' => 'Token invalide'];
        }

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->post("/login", function (Request $request, Response $response) {
        $_POST = $request->getParsedBody();
        ob_start();
        require_once "auth/login.php";
        $output = ob_get_clean();

        $response->getBody()->write($output);
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->post("/register", function (Request $request, Response $response) {
        $data = $request->getParsedBody();
        $_POST = $data;
        ob_start();
        require_once "auth/register.php";
        $output = ob_get_clean();

        $response->getBody()->write($output);
        return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', '*');
    });

    $app->post("/logout", function (Request $request, Response $response) {
        ob_start();
        require_once "auth/logout.php";
        $output = ob_get_clean();

        $response->getBody()->write($output);
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->post("/parcelle", function (Request $request, Response $response) {
        ob_start();
        require_once __DIR__."/parcelle/store.php";
        $output = ob_get_clean();

        $response->getBody()->write($output);
        return $response->withHeader('Content-Type', 'application/json');
    });
};
