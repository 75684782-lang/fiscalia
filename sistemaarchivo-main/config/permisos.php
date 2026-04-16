<?php
// Archivo de permisos: funciones para verificar permisos basados en rol

require_once("conexion.php");

/**
 * Verifica si el usuario actual tiene un permiso específico
 * @param string $permiso Nombre del permiso a verificar
 * @return bool True si tiene permiso, false si no
 */
function tienePermiso($permiso) {
    global $conn;
    
    if (!isset($_SESSION['rol'])) {
        return false;
    }
    
    $rol = $_SESSION['rol'];
    
    // Administrador tiene todos los permisos
    if ($rol === 'administrador') {
        return true;
    }
    
    // Consultar permisos en la BD
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM rol_permiso rp 
        JOIN permiso p ON rp.permiso_id = p.id 
        WHERE rp.rol = ? AND p.nombre = ?
    ");
    $stmt->bind_param("ss", $rol, $permiso);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'] > 0;
}

/**
 * Obtiene todos los permisos de un rol
 * @param string $rol Nombre del rol
 * @return array Lista de permisos
 */
function obtenerPermisosRol($rol) {
    global $conn;
    
    $permisos = [];
    
    $stmt = $conn->prepare("
        SELECT p.nombre 
        FROM rol_permiso rp 
        JOIN permiso p ON rp.permiso_id = p.id 
        WHERE rp.rol = ?
    ");
    $stmt->bind_param("s", $rol);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $permisos[] = $row['nombre'];
    }
    
    return $permisos;
}

/**
 * Registra una acción en la auditoría
 * @param string $tabla Nombre de la tabla afectada
 * @param string $operacion Tipo de operación (INSERT, UPDATE, DELETE, etc.)
 * @param int $registro_id ID del registro afectado (opcional)
 * @param array $valores_anteriores Valores antes del cambio (opcional)
 * @param array $valores_nuevos Valores después del cambio (opcional)
 */
function registrarAuditoria($tabla, $operacion, $registro_id = null, $valores_anteriores = null, $valores_nuevos = null) {
    global $conn;
    
    $usuario_id = $_SESSION['usuario_id'] ?? 0;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $valores_ant_json = $valores_anteriores ? json_encode($valores_anteriores) : null;
    $valores_nue_json = $valores_nuevos ? json_encode($valores_nuevos) : null;
    
    $stmt = $conn->prepare("
        INSERT INTO auditoria (usuario_id, tabla, operacion, registro_id, valores_anteriores, valores_nuevos, ip_address, user_agent) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ississss", $usuario_id, $tabla, $operacion, $registro_id, $valores_ant_json, $valores_nue_json, $ip, $user_agent);
    $stmt->execute();
}
?>