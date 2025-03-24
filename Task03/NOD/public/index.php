<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Neospaer\NOD\Controller;
use Neospaer\NOD\Database;

$app = AppFactory::create();

// Добавляем middleware для парсинга JSON
$app->addBodyParsingMiddleware();

$app->addErrorMiddleware(true, true, true)->setDefaultErrorHandler(function (
    Request $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) use ($app) {
    $payload = ['error' => $exception->getMessage()];
    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write(json_encode($payload));
    return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
});

$app->get('/', function (Request $request, Response $response) {
    $html = file_get_contents(__DIR__ . '/index.html');
    $response->getBody()->write($html);
    return $response->withHeader('Content-Type', 'text/html');
});

$app->get('/games', function (Request $request, Response $response) {
    try {
        $games = Controller\getAllGames();
        $response->getBody()->write(json_encode($games ?? []));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (\Exception $e) {
        error_log('Error in GET /games: ' . $e->getMessage());
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

$app->get('/games/{id}', function (Request $request, Response $response, array $args) {
    $game = Controller\getGameById($args['id']);
    if ($game) {
        $response->getBody()->write(json_encode($game));
        return $response->withHeader('Content-Type', 'application/json');
    }
    $response->getBody()->write(json_encode(['error' => 'Game not found']));
    return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
});

$app->post('/games', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    error_log('POST /games received: ' . json_encode($data));
    $playerName = isset($data['playerName']) && !empty(trim($data['playerName'])) ? trim($data['playerName']) : 'Player';
    error_log('Using playerName: ' . $playerName);
    $gameId = Controller\startNewGame($playerName);
    $response->getBody()->write(json_encode(['id' => $gameId]));
    return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
});

$app->post('/step/{id}', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    error_log('POST /step/' . $args['id'] . ' received: ' . json_encode($data));
    $playerAnswer = isset($data['playerAnswer']) ? (int) $data['playerAnswer'] : 0;
    error_log('Using playerAnswer: ' . $playerAnswer);
    $result = Controller\makeStep($args['id'], $playerAnswer);
    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/clear', function (Request $request, Response $response) {
    Database\clearDatabase();
    $response->getBody()->write(json_encode(['message' => 'База данных успешно очищена']));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
