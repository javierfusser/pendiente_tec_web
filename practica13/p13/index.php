<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

// Detectar automáticamente el basePath
$basePath = rtrim(str_ireplace('index.php', '', $_SERVER['SCRIPT_NAME']), '/');
$app->setBasePath($basePath);

// Ruta raíz
$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("API Slim funcionando. Rutas disponibles: /hola, /hola/{nombre}");
    return $response;
});

// GET - Obtener todos los recursos
$app->get('/hola', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hola mundo GET");
    return $response;
});

// GET con parámetros de ruta
$app->get('/hola/{nombre}', function (Request $request, Response $response, $args) {
    $nombre = $args['nombre'];
    $response->getBody()->write("Hola, {$nombre}");
    return $response;
});

// POST - Crear recurso
$app->post('/hola', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();
    $saludo = $data['saludo'] ?? 'Hola';
    $nombre = $data['nombre'] ?? 'desconocido';
    $response->getBody()->write("{$saludo} {$nombre} desde POST");
    return $response;
});

// GET - Endpoint que regresa JSON con array de personas
$app->get('/testjson', function (Request $request, Response $response, $args) {
    $personas = [
        ['nombre' => 'Juan', 'apellido' => 'Pérez'],
        ['nombre' => 'María', 'apellido' => 'García'],
        ['nombre' => 'Carlos', 'apellido' => 'López'],
        ['nombre' => 'Ana', 'apellido' => 'Martínez'],
        ['nombre' => 'Pedro', 'apellido' => 'Rodríguez']
    ];
    
    $response->getBody()->write(json_encode($personas));
    return $response->withHeader('Content-Type', 'application/json');
});

// PUT - Actualizar recurso completo
$app->put('/hola/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];
    $data = $request->getParsedBody();
    $nombre = $data['nombre'] ?? 'desconocido';
    $response->getBody()->write("Actualizando recurso {$id} con PUT: {$nombre}");
    return $response;
});

// PATCH - Actualizar recurso parcial
$app->patch('/hola/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];
    $data = $request->getParsedBody();
    $nombre = $data['nombre'] ?? 'desconocido';
    $response->getBody()->write("Actualizando parcialmente recurso {$id} con PATCH: {$nombre}");
    return $response;
});

// DELETE - Eliminar recurso
$app->delete('/hola/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];
    $response->getBody()->write("Eliminando recurso {$id}");
    return $response;
});

$app->run();
