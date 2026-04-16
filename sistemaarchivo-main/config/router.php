<?php
/**
 * Router centralizado
 * Maneja todas las rutas de la aplicación
 */

require_once('rutas.php');
require_once('conexion.php');  // Cargar conexión globalmente

class Router {
    private $rutas;
    private $pagina_actual;
    
    public function __construct() {
        global $rutas_disponibles;
        $this->rutas = $rutas_disponibles;
        $this->pagina_actual = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
    }
    
    /**
     * Obtiene la página actual
     */
    public function obtener_pagina() {
        return $this->pagina_actual;
    }
    
    /**
     * Obtiene la ruta de vista para una página
     */
    public function obtener_ruta($pagina) {
        return isset($this->rutas[$pagina]) ? $this->rutas[$pagina] : null;
    }
    
    /**
     * Obtiene la ruta actual
     */
    public function obtener_ruta_actual() {
        return $this->obtener_ruta($this->pagina_actual);
    }
    
    /**
     * Verifica si la ruta existe
     */
    public function ruta_existe($pagina) {
        return isset($this->rutas[$pagina]);
    }
    
    /**
     * Obtiene todos los parámetros GET excepto page
     */
    public function obtener_parametros() {
        $params = $_GET;
        unset($params['page']);
        return $params;
    }
    
    /**
     * Obtiene un parámetro específico
     */
    public function obtener_parametro($nombre, $default = null) {
        return isset($_GET[$nombre]) ? $_GET[$nombre] : $default;
    }
    
    /**
     * Redirige a una página
     */
    public static function redirigir($pagina, $parametros = array()) {
        $url = ruta($pagina, $parametros);
        header("Location: $url");
        exit;
    }
    
    /**
     * Carga la vista correspondiente
     */
    public function cargar_vista() {
        $ruta_archivo = APP_PATH . $this->obtener_ruta_actual();
        
        if (file_exists($ruta_archivo)) {
            // Exponer variables locales útiles para las vistas (disponibles en scope de la inclusión)
            $router = $this;
            $conn = isset($GLOBALS['conn']) ? $GLOBALS['conn'] : null;

            include($ruta_archivo);
        } else {
            echo "<p style='color:red;'>Error: Página no encontrada (404)</p>";
        }
    }
}

// Crear instancia global del router
$router = new Router();

// Validar autenticación según la página
$paginas_publicas = array('login', 'logout');
$pagina_actual = $router->obtener_pagina();

if (!in_array($pagina_actual, $paginas_publicas)) {
    if (!isset($_SESSION['usuario'])) {
        Router::redirigir('login');
    }
}

?>
