<?php
session_start();
require_once("../config/conexion.php");
require_once("../config/rutas.php");
require_once("../config/permisos.php");
require_once("../models/Carpeta.php");

// Validar autenticación
if (!isset($_SESSION['usuario'])) {
    header("Location: " . ruta('login'));
    exit;
}

// Obtener ID del usuario actual
$usuario_id = $_SESSION['usuario_id'] ?? 1; // Ya vinculado con el usuario logueado en tiempo real

$carpeta = new Carpeta($conn);

// ====== GUARDAR CARPETA ======
if (isset($_POST['guardar'])) {
    if (!tienePermiso('crear_carpetas')) {
        $_SESSION['mensaje'] = 'Error: No tienes permisos para crear carpetas';
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: " . ruta('carpeta_registrar'));
        exit;
    }
    
    $numero = trim($_POST['numero'] ?? '');
    $imputado = trim($_POST['imputado'] ?? '');
    $delito = trim($_POST['delito'] ?? '');
    $agraviado = trim($_POST['agraviado'] ?? '');
    $estado = trim($_POST['estado'] ?? '');
    $ubicacion = trim($_POST['ubicacion'] ?? '');
    
    $resultado = $carpeta->registrar($numero, $imputado, $delito, $agraviado, $estado, $ubicacion, $usuario_id);
    
    if ($resultado['success']) {
        $_SESSION['mensaje'] = $resultado['message'];
        $_SESSION['tipo_mensaje'] = 'exito';
        header("Location: " . ruta('carpeta_listar'));
    } else {
        $_SESSION['mensaje'] = $resultado['message'];
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: " . ruta('carpeta_registrar'));
    }
    exit;
}

// ====== IMPORTAR DESDE EXCEL ======
if (isset($_POST['importar'])) {
    if (!tienePermiso('crear_carpetas')) {
        $_SESSION['mensaje'] = 'Error: No tienes permisos para importar carpetas';
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: " . ruta('carpeta_registrar'));
        exit;
    }
    
    if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] != 0) {
        $_SESSION['mensaje'] = 'Error: Archivo no válido';
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: " . ruta('carpeta_registrar'));
        exit;
    }
    
    $archivo = $_FILES['archivo']['tmp_name'];
    
    // Leer archivo CSV (Excel guardado como CSV)
    if (($gestor = fopen($archivo, "r")) !== FALSE) {
        $contador = 0;
        $errores = 0;
        
        while (($datos = fgetcsv($gestor, 1000, "|")) !== FALSE) {
            if (count($datos) < 6) continue;
            
            $numero = trim($datos[0]);
            $imputado = trim($datos[1]);
            $delito = trim($datos[2]);
            $agraviado = trim($datos[3]);
            $estado = trim($datos[4]);
            $ubicacion = trim($datos[5]);
            
            $resultado = $carpeta->registrar($numero, $imputado, $delito, $agraviado, $estado, $ubicacion, $usuario_id);
            
            if ($resultado['success']) {
                $contador++;
            } else {
                $errores++;
            }
        }
        fclose($gestor);
        
        $_SESSION['mensaje'] = "Importación completada: $contador carpetas registradas, $errores errores";
        $_SESSION['tipo_mensaje'] = $errores == 0 ? 'exito' : 'advertencia';
    } else {
        $_SESSION['mensaje'] = 'Error al leer el archivo';
        $_SESSION['tipo_mensaje'] = 'error';
    }
    
    header("Location: " . ruta('carpeta_listar'));
    exit;
}

?>
