<?php

error_reporting(-1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Argentina/Buenos_Aires');

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;


require_once './vendor/autoload.php';

require_once './models/VerificacionMW.php';
require_once './models/AuthJWT.php';
require_once './db/AccesoDatos.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/ComandaController.php';



// Instantiate App
$app = AppFactory::create();
$app->setBasePath("/LaComandaLocal/app");
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
// Add error middleware
$app->addErrorMiddleware(true, true, true);

require_once 'config.php';

$app->group('/login', function (RouteCollectorProxy $group) {
    $group->post('[/]', \UsuarioController::class . ':Loguear');
});

// Routes con MW
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/rol/{rol}', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(\VerificacionMW::class . ':VerificarAdmin');
    $group->post('/delete', \UsuarioController::class . ':BorrarUno')->add(\VerificacionMW::class . ':VerificarAdmin');
    $group->post('/modificar', \UsuarioController::class . ':ModificarUno')->add(\VerificacionMW::class . ':VerificarAdmin');
  })->add(\VerificacionMW::class . ':VerificarToken');

  $app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductoController::class . ':TraerTodos')->add(\VerificacionMW::class . ':VerificarEmpleado');
    $group->get('/rol/{rolResponsable}', \ProductoController::class . ':TraerTodos')->add(\VerificacionMW::class . ':VerificarEmpleado');
    $group->get('/tipo/{tipo}', \ProductoController::class . ':TraerTodos')->add(\VerificacionMW::class . ':VerificarEmpleado');
    $group->get('/{id}', \ProductoController::class . ':TraerUno')->add(\VerificacionMW::class . ':VerificarEmpleado');
    $group->post('[/]', \ProductoController::class . ':CargarUno')->add(\VerificacionMW::class . ':VerificarAdmin');
    $group->post('/delete', \ProductoController::class . ':BorrarUno')->add(\VerificacionMW::class . ':VerificarAdmin');
    $group->post('/modificar', \ProductoController::class . ':ModificarUno')->add(\VerificacionMW::class . ':VerificarAdmin');
  })->add(\VerificacionMW::class . ':VerificarToken');

  $app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \MesaController::class . ':TraerTodos')->add(\VerificacionMW::class . ':VerificarMozo');
    $group->get('/{id}', \MesaController::class . ':TraerUno')->add(\VerificacionMW::class . ':VerificarMozo');
    $group->post('[/]', \MesaController::class . ':CargarUno')->add(\VerificacionMW::class . ':VerificarMozo');
    $group->post('/delete', \MesaController::class . ':BorrarUno')->add(\VerificacionMW::class . ':VerificarAdmin');
    $group->post('/modificar', \MesaController::class . ':ModificarUno')->add(\VerificacionMW::class . ':VerificarMozo');
  })->add(\VerificacionMW::class . ':VerificarToken');

  $app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \PedidoController::class . ':TraerTodos')->add(\VerificacionMW::class . ':VerificarEmpleado');
    $group->get('/{codigo}', \PedidoController::class . ':TraerUno')->add(\VerificacionMW::class . ':VerificarEmpleado');
    $group->post('[/]', \PedidoController::class . ':CargarUno')->add(\VerificacionMW::class . ':VerificarMozo');
    $group->post('/delete', \PedidoController::class . ':BorrarUno')->add(\VerificacionMW::class . ':VerificarMozo');
    $group->post('/modificar', \PedidoController::class . ':ModificarUno')->add(\VerificacionMW::class . ':VerificarEmpleado');
  })->add(\VerificacionMW::class . ':VerificarToken');

  $app->group('/comandas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ComandaController::class . ':TraerTodos')->add(\VerificacionMW::class . ':VerificarMozo');
    $group->get('/{id}', \ComandaController::class . ':TraerUno')->add(\VerificacionMW::class . ':VerificarMozo');
    $group->post('[/]', \ComandaController::class . ':CargarUno')->add(\VerificacionMW::class . ':VerificarMozo');
    $group->post('/cerrar', \ComandaController::class . ':BorrarUno')->add(\VerificacionMW::class . ':VerificarMozo');
    $group->post('/modificar', \ComandaController::class . ':ModificarUno')->add(\VerificacionMW::class . ':VerificarMozo');
})->add(\VerificacionMW::class . ':VerificarToken');

//// Routes sin MW
// $app->group('/usuarios', function (RouteCollectorProxy $group) {
//   $group->get('[/]', \UsuarioController::class . ':TraerTodos');
//   $group->get('/rol/{rol}', \UsuarioController::class . ':TraerTodos');
//   $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
//   $group->post('[/]', \UsuarioController::class . ':CargarUno');
//   $group->post('/delete', \UsuarioController::class . ':BorrarUno');
//   $group->post('/modificar', \UsuarioController::class . ':ModificarUno');
// });

// $app->group('/productos', function (RouteCollectorProxy $group) {
//   $group->get('[/]', \ProductoController::class . ':TraerTodos');
//   $group->get('/rol/{rolResponsable}', \ProductoController::class . ':TraerTodos');
//   $group->get('/tipo/{tipo}', \ProductoController::class . ':TraerTodos');
//   $group->get('/{id}', \ProductoController::class . ':TraerUno');
//   $group->post('[/]', \ProductoController::class . ':CargarUno');
//   $group->post('/delete', \ProductoController::class . ':BorrarUno');
//   $group->post('/modificar', \ProductoController::class . ':ModificarUno');
// });

// $app->group('/mesas', function (RouteCollectorProxy $group) {
//   $group->get('[/]', \MesaController::class . ':TraerTodos');
//   $group->get('/{id}', \MesaController::class . ':TraerUno');
//   $group->post('[/]', \MesaController::class . ':CargarUno');
//   $group->post('/delete', \MesaController::class . ':BorrarUno');
//   $group->post('/modificar', \MesaController::class . ':ModificarUno');
// });

// $app->group('/pedidos', function (RouteCollectorProxy $group) {
//   $group->get('[/]', \PedidoController::class . ':TraerTodos');
//   $group->get('/{codigo}', \PedidoController::class . ':TraerUno');
//   $group->post('[/]', \PedidoController::class . ':CargarUno');
//   $group->post('/delete', \PedidoController::class . ':BorrarUno');
//   $group->post('/modificar', \PedidoController::class . ':ModificarUno');
// });

// $app->group('/comandas', function (RouteCollectorProxy $group) {
//   $group->get('[/]', \ComandaController::class . ':TraerTodos');
//   $group->get('/{id}', \ComandaController::class . ':TraerUno');
//   $group->post('[/]', \ComandaController::class . ':CargarUno');
//   $group->post('/cerrar', \ComandaController::class . ':BorrarUno');
//   $group->post('/modificar', \ComandaController::class . ':ModificarUno');
// });

$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("Slim Framework 4 PHP");
    return $response;
});

$app->run();
?>