<?php
// Script para ejecutar mejoras de roles y auditoría
require_once("config/conexion.php");

echo "Ejecutando mejoras de roles y auditoría...\n";

// Leer el archivo SQL
$sql = file_get_contents("mejoras_roles_auditoria.sql");

if (!$sql) {
    die("Error: No se pudo leer el archivo mejoras_roles_auditoria.sql\n");
}

// Dividir en consultas individuales
$queries = array_filter(array_map('trim', explode(';', $sql)));

$success = 0;
$errors = 0;

foreach ($queries as $query) {
    if (empty($query) || strpos($query, '--') === 0) continue;

    if ($conn->query($query)) {
        $success++;
        echo "✓ Consulta ejecutada correctamente\n";
    } else {
        $errors++;
        echo "✗ Error en consulta: " . $conn->error . "\n";
        echo "Consulta: " . substr($query, 0, 100) . "...\n";
    }
}

echo "\nResumen:\n";
echo "Consultas exitosas: $success\n";
echo "Errores: $errors\n";

if ($errors == 0) {
    echo "\n¡Mejoras aplicadas correctamente!\n";
    echo "Ahora puedes iniciar sesión como admin (usuario: admin, contraseña: 1234)\n";
    echo "y acceder al módulo de usuarios desde el menú Administración.\n";
} else {
    echo "\nHubo errores. Revisa los mensajes anteriores.\n";
}
?>