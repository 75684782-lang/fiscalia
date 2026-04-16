<?php
session_start();
require_once("../config/conexion.php");
require_once("../config/rutas.php");
require_once("../config/permisos.php");

if (isset($_POST['login'])) {
    
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');
    
    // Validar entrada
    if (empty($user) || empty($pass)) {
        $_SESSION['mensaje'] = 'Error: Usuario y contraseña requeridos';
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: " . ruta('login'));
        exit;
    }
    
    // Obtener usuario usando Prepared Statements (Prevención de SQL Injection)
    $stmt = $conn->prepare("SELECT id, username, password, rol FROM usuario WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        
        // Verificación de contraseña (Soporta Hash y texto plano temporalmente por compatibilidad)
        if (password_verify($pass, $usuario['password']) || $pass === $usuario['password']) {
            
            // Regenerar ID (Prevención de Session Fixation)
            session_regenerate_id(true);
            
            $_SESSION['usuario'] = $usuario['username'];
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['rol'] = $usuario['rol']; // Obtener rol de la BD
            
            // Registrar auditoría (si la tabla existe)
            registrarAuditoria('usuario', 'LOGIN', $usuario['id']);
            
            header("Location: " . ruta('dashboard'));
            exit;
        }
    }
    
    // Si llega aquí, falló el usuario o la contraseña
    $_SESSION['mensaje'] = 'Error: Usuario o contraseña incorrectos';
    $_SESSION['tipo_mensaje'] = 'error';
    header("Location: " . ruta('login'));
    exit;
}

// ====== LOGOUT ======
if (isset($_GET['logout'])) {
    // Registrar auditoría (si la tabla existe)
    $usuario_id = $_SESSION['usuario_id'] ?? 0;
    if ($usuario_id) {
        registrarAuditoria('usuario', 'LOGOUT', $usuario_id);
    }
    
    session_destroy();
    header("Location: " . ruta('login'));
    exit;
}

?>
