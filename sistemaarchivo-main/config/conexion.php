<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "sistema_archivo_db";

$GLOBALS['conn'] = new mysqli($host, $user, $pass, $db);

if ($GLOBALS['conn']->connect_error) {
    die("Error de conexión: " . $GLOBALS['conn']->connect_error);
}

// Opcional: configurar charset
$GLOBALS['conn']->set_charset("utf8");

// Hacer accesible como variable local también
$conn = $GLOBALS['conn'];
?>
