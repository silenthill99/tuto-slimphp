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
    $app->group('/login', function (Request $request, Response $response) {
        $response->getBody()->write("Ici s'affichera la page de connexion");
        return $response;
    });
    $app->group('/register', function (Request $request, Response $response) {
        $response->getBody()->write("Ici s'affichera la page de création de compte");
        return $response;
    });
};
