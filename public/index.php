<?php 

require_once __DIR__ . '/../includes/app.php';

use Controllers\DashboardController;
use Controllers\Logincontroller;
use Controllers\TareaController;
use MVC\Router;
$router = new Router();

//login
$router->get('/', [Logincontroller::class, 'login']);
$router->post('/', [Logincontroller::class, 'login']);
$router->get('/logout', [Logincontroller::class, 'logout']);

//Crear Cuenta
$router->get('/crear', [Logincontroller::class, 'crear']);
$router->post('/crear', [Logincontroller::class, 'crear']);

//formulario de olvide mi password
$router->get('/olvide', [Logincontroller::class, 'olvide']);
$router->post('/olvide', [Logincontroller::class, 'olvide']);

//Colocar nuevo password
$router->get('/reestablecer', [Logincontroller::class, 'reestablecer']);
$router->post('/reestablecer', [Logincontroller::class, 'reestablecer']);

//Mensaje de confirmacion de cuenta
$router->get('/mensaje', [Logincontroller::class, 'mensaje']);
$router->get('/confirmar', [Logincontroller::class, 'confirmar']);

//Zona de proyectos
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/crear-proyecto', [DashboardController::class, 'crear_proyecto']);
$router->post('/crear-proyecto', [DashboardController::class, 'crear_proyecto']);
$router->get('/proyecto', [DashboardController::class, 'proyecto']);
$router->get('/perfil', [DashboardController::class, 'perfil']);
$router->post('/perfil', [DashboardController::class, 'perfil']);
$router->get('/cambiar-password', [DashboardController::class, 'cambiar_password']);
$router->post('/cambiar-password', [DashboardController::class, 'cambiar_password']);

//API para tareas
$router->get('/api/tareas', [TareaController::class, 'index']);
$router->post('/api/tareas', [TareaController::class, 'crear']);
$router->post('/api/tareas/actualizar', [TareaController::class, 'actualizar']);
$router->post('/api/tareas/eliminar', [TareaController::class, 'eliminar']);



// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();