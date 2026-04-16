<?php
// Archivo de verificación del menú de administración
session_start();
require_once("config/conexion.php");
require_once("config/permisos.php");

// Simular estar logeado como superadmin
$_SESSION['usuario'] = 'superadmin';
$_SESSION['usuario_id'] = 5;
$_SESSION['rol'] = 'administrador';

echo "=== VERIFICACIÓN DEL MENÚ DE ADMINISTRACIÓN ===\n\n";
echo "Usuario: " . $_SESSION['usuario'] . "\n";
echo "Rol: " . $_SESSION['rol'] . "\n\n";

echo "Verificando permisos que debe tener 'administrador':\n";
$permisos_admin = [
    'ver_auditoria',
    'admin_usuarios',
    'admin_roles'
];

$todos_ok = true;
foreach ($permisos_admin as $permiso) {
    $tiene = tienePermiso($permiso);
    echo "- $permiso: " . ($tiene ? "✓ SÍ" : "✗ NO") . "\n";
    if (!$tiene) $todos_ok = false;
}

if ($todos_ok) {
    echo "\n✓ CORRECTO: El usuario administrador tiene todos los permisos necesarios.\n";
    echo "\nPasos ahora:\n";
    echo "1. Abre tu navegador y ve a: http://localhost/sistemaarchivo-main/\n";
    echo "2. Inicia sesión con: superadmin / admin123\n";
    echo "3. El menú de Administración debe aparecer en la barra lateral izquierda\n";
    echo "4. Haz clic en 'Gestionar Usuarios' para ver la interfaz\n";
} else {
    echo "\n✗ ERROR: Falta verificar los permisos en la base de datos.\n";
}
?>