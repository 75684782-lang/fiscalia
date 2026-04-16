<?php
// Script de prueba de tienePermiso()
session_start();
require_once("config/conexion.php");
require_once("config/permisos.php");

echo "=== PRUEBA DE FUNCIÓN tienePermiso() ===\n\n";

// Simular usuario logeado como superadmin
$_SESSION['usuario'] = 'superadmin';
$_SESSION['usuario_id'] = 5;
$_SESSION['rol'] = 'administrador';

echo "Sesión simulada:\n";
echo "- Usuario: " . $_SESSION['usuario'] . "\n";
echo "- Rol: " . $_SESSION['rol'] . "\n\n";

// Pruebas de tienePermiso
$permisos_prueba = [
    'admin_usuarios',
    'admin_roles',
    'ver_auditoria',
    'ver_carpetas',
    'crear_carpetas',
    'ver_prestamos'
];

echo "Pruebas de permisos:\n";
foreach ($permisos_prueba as $permiso) {
    $resultado = tienePermiso($permiso);
    $estado = $resultado ? '✓ SÍ' : '✗ NO';
    echo "- $permiso: $estado\n";
}

echo "\n=== SI VES TODOS CON ✓ SÍ, LA FUNCIÓN FUNCIONA CORRECTAMENTE ===\n";
?>