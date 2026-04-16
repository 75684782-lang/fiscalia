<?php
require_once("config/rutas.php");

// Destruir la sesión
session_destroy();

// Redirigir al login
header("Location: " . ruta('login'));
exit;
?>