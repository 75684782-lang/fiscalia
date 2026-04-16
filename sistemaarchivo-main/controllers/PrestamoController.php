<?php
session_start();
require_once("../config/conexion.php");
require_once("../config/rutas.php");
require_once("../models/Prestamo.php");

// Validar autenticación
if (!isset($_SESSION['usuario'])) {
    header("Location: " . ruta('login'));
    exit;
}

// Obtener ID del usuario actual
$usuario_id = $_SESSION['usuario_id'] ?? 1; // Ya vinculado con el usuario logueado en tiempo real

$prestamo = new Prestamo($conn);

// ====== GUARDAR PRÉSTAMO ======
if (isset($_POST['guardar'])) {
    
    // Validar inputs
    $dependencia = intval($_POST['dependencia'] ?? 0);
    $carpetas = $_POST['carpetas'] ?? [];
    $dias = intval($_POST['dias'] ?? 7);
    
    if ($dependencia == 0) {
        $_SESSION['mensaje'] = 'Error: Debe seleccionar una dependencia';
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: " . ruta('prestamo_registrar'));
        exit;
    }
    
    if (empty($carpetas)) {
        $_SESSION['mensaje'] = 'Error: Debe seleccionar al menos una carpeta';
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: " . ruta('prestamo_registrar'));
        exit;
    }
    
    if ($dias < 1 || $dias > 365) {
        $_SESSION['mensaje'] = 'Error: Los días de préstamo deben estar entre 1 y 365';
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: " . ruta('prestamo_registrar'));
        exit;
    }
    
    // Registrar préstamo
    $resultado = $prestamo->registrar($dependencia, $usuario_id, $carpetas, $dias);
    
    if ($resultado['success']) {
        $_SESSION['mensaje'] = "✓ " . $resultado['message'] . " - Guía: " . $resultado['numero_guia'];
        $_SESSION['tipo_mensaje'] = 'exito';
        $_SESSION['numero_guia'] = $resultado['numero_guia'];
        $_SESSION['fecha_vencimiento'] = $resultado['fecha_vencimiento'];
        header("Location: " . ruta('prestamo_listar'));
    } else {
        $_SESSION['mensaje'] = "Error: " . $resultado['message'];
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: " . ruta('prestamo_registrar'));
    }
    exit;
}

// ====== REGISTRAR DEVOLUCIÓN ======
if (isset($_POST['devolver'])) {
    $prestamo_id = intval($_POST['prestamo_id'] ?? 0);
    
    if ($prestamo_id == 0) {
        $_SESSION['mensaje'] = 'Error: Préstamo no válido';
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: " . ruta('prestamo_listar'));
        exit;
    }
    
    $resultado = $prestamo->registrar_devolucion($prestamo_id, $usuario_id);
    
    if ($resultado['success']) {
        $dias_venc = $resultado['dias_vencimiento'];
        $mensaje = "Devolución registrada";
        if ($dias_venc > 0) {
            $mensaje .= " ⚠ Con $dias_venc días de vencimiento";
        }
        $_SESSION['mensaje'] = $mensaje;
        $_SESSION['tipo_mensaje'] = $dias_venc > 0 ? 'advertencia' : 'exito';
    } else {
        $_SESSION['mensaje'] = "Error: " . $resultado['message'];
        $_SESSION['tipo_mensaje'] = 'error';
    }
    
    header("Location: " . ruta('prestamo_listar'));
    exit;
}

?>
