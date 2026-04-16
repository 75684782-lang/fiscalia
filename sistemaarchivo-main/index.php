<?php
session_start();

// Incluir configuración necesaria
require_once("config/conexion.php");
require_once("config/router.php");

$pagina_actual = $router->obtener_pagina();
$vistas_sin_layout = ['login'];

// Llamar al controlador de administración para procesar POST/GET de acciones
if (strpos($pagina_actual, 'admin_') === 0) {
    require_once("controllers/AdminController.php");
}
// Llamar a otros controladores similar
elseif (strpos($pagina_actual, 'usuario_') === 0) {
    require_once("controllers/UsuarioController.php");
}

if (!in_array($pagina_actual, $vistas_sin_layout)) {
    // Cargar header solo si NO estamos en el login
    include(APP_PATH . VIEWS_LAYOUTS . "header.php");
}

// Cargar la vista correspondiente según la ruta
$router->cargar_vista();

if (!in_array($pagina_actual, $vistas_sin_layout)) {
    // Cargar footer solo si NO estamos en el login
    include(APP_PATH . VIEWS_LAYOUTS . "footer.php");
}
?>
