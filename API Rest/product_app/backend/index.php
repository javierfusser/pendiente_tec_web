<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use TECWEB\MYAPI\Create\Create;
use TECWEB\MYAPI\Read\Read;
use TECWEB\MYAPI\Update\Update;
use TECWEB\MYAPI\Delete\Delete;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// Detectar automáticamente el basePath
$basePath = rtrim(str_ireplace('index.php', '', $_SERVER['SCRIPT_NAME']), '/');
$app->setBasePath($basePath);

// GET /products - Listar todos los productos
$app->get('/products', function (Request $request, Response $response) {
    $productos = new Read('marketzone');
    $productos->list();
    $response->getBody()->write($productos->getData());
    return $response->withHeader('Content-Type', 'application/json');
});

// GET /products/{search} - Buscar productos
$app->get('/products/{search}', function (Request $request, Response $response, $args) {
    $search = $args['search'];
    $productos = new Read('marketzone');
    $productos->search($search);
    $response->getBody()->write($productos->getData());
    return $response->withHeader('Content-Type', 'application/json');
});

// GET /product/{id} - Obtener un producto por ID
$app->get('/product/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];
    $productos = new Read('marketzone');
    $productos->single($id);
    $response->getBody()->write($productos->getData());
    return $response->withHeader('Content-Type', 'application/json');
});

// POST /product - Crear producto
$app->post('/product', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $productos = new Create('marketzone');
    $productos->add(json_decode(json_encode($data)));
    $response->getBody()->write($productos->getData());
    return $response->withHeader('Content-Type', 'application/json');
});

// PUT /product - Actualizar producto
$app->put('/product', function (Request $request, Response $response) {
    // Leer el body raw para PUT
    $body = $request->getBody()->getContents();
    parse_str($body, $data);

    // Si viene vacío, intentar con getParsedBody
    if (empty($data)) {
        $data = $request->getParsedBody();
    }

    $productos = new Update('marketzone');
    $productos->edit(json_decode(json_encode($data)));
    $response->getBody()->write($productos->getData());
    return $response->withHeader('Content-Type', 'application/json');
});

// DELETE /product - Eliminar producto
$app->delete('/product', function (Request $request, Response $response) {
    // Leer el body raw para DELETE
    $body = $request->getBody()->getContents();
    parse_str($body, $data);

    // Si viene vacío, intentar con getParsedBody
    if (empty($data)) {
        $data = $request->getParsedBody();
    }

    $productos = new Delete('marketzone');
    $productos->delete($data['id'] ?? null);
    $response->getBody()->write($productos->getData());
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();