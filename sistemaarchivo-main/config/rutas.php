<?php
/**
 * Archivo de constantes para rutas centralizadas
 * Facilita el mantenimiento y cambios de URLs
 */

// URL base de la aplicación
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/sistemaarchivo-main/');
define('APP_PATH', dirname(dirname(__FILE__)) . '/');

// Rutas de vistas
define('VIEWS_CARPETA', 'views/carpeta/');
define('VIEWS_PRESTAMO', 'views/prestamo/');
define('VIEWS_REPORTES', 'views/prestamo/reportes/');
define('VIEWS_LAYOUTS', 'views/layouts/');

// Rutas de controladores
define('CONTROLLERS_PATH', APP_PATH . 'controllers/');
define('MODELS_PATH', APP_PATH . 'models/');

// Rutas de recursos públicos
define('PUBLIC_CSS', BASE_URL . 'public/css/');
define('PUBLIC_JS', BASE_URL . 'public/js/');

// Funciones auxiliares para generar rutas
function ruta($pagina = null, $parametros = array()) {
    $url = BASE_URL . 'index.php';
    
    if ($pagina) {
        $url .= '?page=' . urlencode($pagina);
        
        foreach ($parametros as $clave => $valor) {
            $url .= '&' . urlencode($clave) . '=' . urlencode($valor);
        }
    }
    
    return $url;
}

function ruta_vista($ruta) {
    return BASE_URL . $ruta;
}

// Mapeo de páginas a vistas
$rutas_disponibles = array(
    // Dashboard
    'dashboard' => 'views/dashboard.php',
    'inicio' => 'views/dashboard.php',
    
    // Carpetas
    'carpeta_registrar' => VIEWS_CARPETA . 'registrar.php',
    'carpeta_listar' => VIEWS_CARPETA . 'listar.php',
    'carpeta_buscar' => VIEWS_CARPETA . 'buscar.php',
    'carpeta_importar' => VIEWS_CARPETA . 'importar.php',
    
    // Préstamos
    'prestamo_registrar' => VIEWS_PRESTAMO . 'registrar.php',
    'prestamo_listar' => VIEWS_PRESTAMO . 'listar.php',
    'prestamo_devolucion' => VIEWS_PRESTAMO . 'devolucion.php',
    
    // Reportes
    'reporte_dependencia' => VIEWS_REPORTES . 'prestamos_dependencia.php',
    'reporte_vencidos' => VIEWS_REPORTES . 'vencidos.php',
    
    // Administración
    'admin_auditorias' => 'views/admin/auditorias.php',
    'admin_asignar_roles' => 'views/admin/asignar_roles.php',
    'admin_usuarios' => 'views/admin/usuarios.php',
    
    // Autenticación
    'login' => 'login.php',
    'logout' => 'logout.php',
);

?>
