<?php 

require_once __DIR__ . '/../includes/app.php';

use Controllers\AdminControllers;
use Controllers\APIControllers;
use Controllers\CitaControllers;
use Controllers\LoginControllers;
use Controllers\ServiciosControllers;
use MVC\Router;

$router = new Router();

$router->get('/', [LoginControllers::class, 'login']);
$router->post('/', [LoginControllers::class, 'login']);
$router->get('/logout', [LoginControllers::class, 'logout']);

$router->get('/olvide', [LoginControllers::class, 'olvide']);
$router->post('/olvide', [LoginControllers::class, 'olvide']);
$router->get('/recuperar', [LoginControllers::class, 'recuperar']);
$router->post('/recuperar', [LoginControllers::class, 'recuperar']);

$router->get('/crear-cuenta', [LoginControllers::class, 'crear']);
$router->post('/crear-cuenta', [LoginControllers::class, 'crear']);

$router->get('/confirmar-cuenta', [LoginControllers::class, 'confirmar']);

$router->get('/mensaje', [LoginControllers::class, 'mensaje']);

$router->get('/cita', [CitaControllers::class, 'index']);
$router->get('/admin', [AdminControllers::class, 'index']);

$router->get('/api/servicios', [APIControllers::class, 'index']);
$router->post('/api/citas', [APIControllers::class, 'guardar']);
$router->post('/api/eliminar', [APIControllers::class, 'eliminar']);

$router->get('/servicios', [ServiciosControllers::class, 'index']);
$router->post('/servicios', [ServiciosControllers::class, 'index']);
$router->get('/servicios/crear', [ServiciosControllers::class, 'crear']);
$router->post('/servicios/crear', [ServiciosControllers::class, 'crear']);
$router->get('/servicios/actualizar', [ServiciosControllers::class, 'actualizar']);
$router->post('/servicios/actualizar', [ServiciosControllers::class, 'actualizar']);
$router->post('/servicios/eliminar', [ServiciosControllers::class, 'eliminar']);



// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();