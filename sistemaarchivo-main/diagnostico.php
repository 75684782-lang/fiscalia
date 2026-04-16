<?php
// Script de diagnóstico para verificar permisos y menú
require_once("config/conexion.php");
require_once("config/permisos.php");

echo "=== DIAGNÓSTICO DEL SISTEMA ===\n\n";

// 1. Verificar permisos en BD
echo "1. Verificar tabla 'permiso':\n";
$result = $conn->query("SELECT COUNT(*) as count FROM permiso");
$count = $result->fetch_assoc()['count'];
echo "   - Total de permisos: $count\n";
if ($count == 0) {
    echo "   ⚠️ ERROR: No hay permisos registrados\n";
} else {
    echo "   ✓ Permisos registrados correctamente\n";
}

// 2. Verificar tabla rol_permiso
echo "\n2. Verificar tabla 'rol_permiso':\n";
$result = $conn->query("SELECT COUNT(*) as count FROM rol_permiso");
$count = $result->fetch_assoc()['count'];
echo "   - Total de asignaciones rol-permiso: $count\n";
if ($count == 0) {
    echo "   ⚠️ ERROR: No hay roles con permisos asignados\n";
} else {
    echo "   ✓ Asignaciones correctas\n";
}

// 3. Verificar usuario superadmin
echo "\n3. Verificar usuario 'superadmin':\n";
$stmt = $conn->prepare("SELECT id, username, rol FROM usuario WHERE username = ?");
$stmt->bind_param("s", $username);
$username = 'superadmin';
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "   ✓ Usuario encontrado\n";
    echo "   - ID: " . $user['id'] . "\n";
    echo "   - Username: " . $user['username'] . "\n";
    echo "   - Rol: " . $user['rol'] . "\n";
} else {
    echo "   ✗ ERROR: Usuario no encontrado\n";
}

// 4. Verificar permisos del rol 'administrador'
echo "\n4. Permisos del rol 'administrador':\n";
$result = $conn->query("SELECT p.nombre FROM rol_permiso rp JOIN permiso p ON rp.permiso_id = p.id WHERE rp.rol = 'administrador' ORDER BY p.nombre");
if ($result->num_rows > 0) {
    echo "   ✓ Permisos asignados:\n";
    while ($perm = $result->fetch_assoc()) {
        echo "     - " . $perm['nombre'] . "\n";
    }
} else {
    echo "   ✗ ERROR: No hay permisos asignados al rol administrador\n";
}

// 5. Verificar tabla auditoria
echo "\n5. Verificar tabla 'auditoria':\n";
$result = $conn->query("SHOW COLUMNS FROM auditoria LIKE 'user_agent'");
if ($result->num_rows > 0) {
    echo "   ✓ Campo 'user_agent' existe\n";
} else {
    echo "   ⚠️ Campo 'user_agent' no existe (pero no es crítico)\n";
}

// 6. Resumen
echo "\n=== RESUMEN ===\n";
$errors = 0;
$result_permisos = $conn->query("SELECT COUNT(*) as count FROM permiso");
$result_rolespermisos = $conn->query("SELECT COUNT(*) as count FROM rol_permiso");
$stmt = $conn->prepare("SELECT rol FROM usuario WHERE username = ?");
$stmt->bind_param("s", $username);
$username = 'superadmin';
$stmt->execute();
$result_usuario = $stmt->get_result();

if ($result_permisos->fetch_assoc()['count'] == 0) $errors++;
if ($result_rolespermisos->fetch_assoc()['count'] == 0) $errors++;
if ($result_usuario->num_rows == 0) $errors++;

if ($errors == 0) {
    echo "✓ Todo parece estar correcto. Intenta:\n";
    echo "  1. Cerrar sesión completamente\n";
    echo "  2. Borrar cookies del navegador\n";
    echo "  3. Volver a iniciar sesión con superadmin / admin123\n";
    echo "  4. Recargar la página con Ctrl+F5\n";
} else {
    echo "✗ Se encontraron $errors error(es). Revisa los detalles arriba.\n";
}
?>