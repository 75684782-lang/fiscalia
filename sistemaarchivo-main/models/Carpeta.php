<?php
/**
 * Modelo Carpeta
 * Gestiona operaciones de carpetas fiscales
 */
class Carpeta {
    private $conn;
    
    public function __construct($conexion) {
        $this->conn = $conexion;
    }
    
    /**
     * Registrar nueva carpeta
     */
    public function registrar($numero, $imputado, $delito, $agraviado, $estado, $ubicacion, $usuario_id) {
        // Validar campos obligatorios
        if (empty($numero) || empty($imputado) || empty($delito) || empty($agraviado) || empty($estado) || empty($ubicacion)) {
            return ['success' => false, 'message' => 'Todos los campos son obligatorios'];
        }
        
        // Validar duplicado
        $numero = $this->conn->real_escape_string($numero);
        $verificar = "SELECT id FROM carpeta_fiscal WHERE numero_carpeta = '$numero'";
        $resultado = $this->conn->query($verificar);
        
        if ($resultado->num_rows > 0) {
            return ['success' => false, 'message' => 'Error: Número de carpeta ya existe'];
        }
        
        // Escapar datos
        $imputado = $this->conn->real_escape_string($imputado);
        $delito = $this->conn->real_escape_string($delito);
        $agraviado = $this->conn->real_escape_string($agraviado);
        $estado = $this->conn->real_escape_string($estado);
        $ubicacion = $this->conn->real_escape_string($ubicacion);
        
        // Insertar
        $sql = "INSERT INTO carpeta_fiscal 
                (numero_carpeta, imputado, delito, agraviado, estado, ubicacion, usuario_creacion_id)
                VALUES ('$numero', '$imputado', '$delito', '$agraviado', '$estado', '$ubicacion', $usuario_id)";
        
        if ($this->conn->query($sql)) {
            $this->registrar_auditoria($usuario_id, 'carpeta_fiscal', 'INSERT', $this->conn->insert_id, null, compact('numero', 'imputado', 'delito', 'agraviado', 'estado', 'ubicacion'));
            return ['success' => true, 'message' => 'Carpeta registrada correctamente', 'id' => $this->conn->insert_id];
        } else {
            return ['success' => false, 'message' => 'Error al registrar: ' . $this->conn->error];
        }
    }
    
    /**
     * Obtener carpeta por número
     */
    public function obtener_por_numero($numero) {
        $numero = $this->conn->real_escape_string($numero);
        $sql = "SELECT * FROM carpeta_fiscal WHERE numero_carpeta = '$numero'";
        $resultado = $this->conn->query($sql);
        
        if ($resultado && $resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        return null;
    }
    
    /**
     * Obtener todas las carpetas
     */
    public function obtener_todas($limite = null) {
        $sql = "SELECT * FROM carpeta_fiscal ORDER BY fecha_registro DESC";
        if ($limite) {
            $sql .= " LIMIT " . intval($limite);
        }
        return $this->conn->query($sql);
    }
    
    /**
     * Búsqueda avanzada
     */
    public function buscar($criterio, $tipo = 'numero') {
        $criterio = $this->conn->real_escape_string($criterio);
        
        $campos_validos = ['numero_carpeta', 'imputado', 'delito', 'agraviado', 'estado', 'ubicacion'];
        $tipo = in_array($tipo, $campos_validos) ? $tipo : 'numero_carpeta';
        
        $sql = "SELECT * FROM carpeta_fiscal WHERE $tipo LIKE '%$criterio%' ORDER BY fecha_registro DESC";
        return $this->conn->query($sql);
    }
    
    /**
     * Actualizar carpeta
     */
    public function actualizar($id, $numero, $imputado, $delito, $agraviado, $estado, $ubicacion, $usuario_id) {
        $id = intval($id);
        
        // Obtener valores anteriores
        $anterior = $this->obtener_por_id($id);
        
        // Escapar datos
        $numero = $this->conn->real_escape_string($numero);
        $imputado = $this->conn->real_escape_string($imputado);
        $delito = $this->conn->real_escape_string($delito);
        $agraviado = $this->conn->real_escape_string($agraviado);
        $estado = $this->conn->real_escape_string($estado);
        $ubicacion = $this->conn->real_escape_string($ubicacion);
        
        $sql = "UPDATE carpeta_fiscal 
                SET numero_carpeta = '$numero', imputado = '$imputado', delito = '$delito', 
                    agraviado = '$agraviado', estado = '$estado', ubicacion = '$ubicacion'
                WHERE id = $id";
        
        if ($this->conn->query($sql)) {
            $this->registrar_auditoria($usuario_id, 'carpeta_fiscal', 'UPDATE', $id, $anterior, compact('numero', 'imputado', 'delito', 'agraviado', 'estado', 'ubicacion'));
            return ['success' => true, 'message' => 'Carpeta actualizada'];
        }
        return ['success' => false, 'message' => 'Error al actualizar'];
    }
    
    /**
     * Eliminar carpeta
     */
    public function eliminar($id, $usuario_id) {
        $id = intval($id);
        $anterior = $this->obtener_por_id($id);
        
        $sql = "DELETE FROM carpeta_fiscal WHERE id = $id";
        if ($this->conn->query($sql)) {
            $this->registrar_auditoria($usuario_id, 'carpeta_fiscal', 'DELETE', $id, $anterior, null);
            return ['success' => true, 'message' => 'Carpeta eliminada'];
        }
        return ['success' => false, 'message' => 'Error al eliminar'];
    }
    
    /**
     * Obtener carpeta por ID
     */
    private function obtener_por_id($id) {
        $id = intval($id);
        $sql = "SELECT * FROM carpeta_fiscal WHERE id = $id";
        $resultado = $this->conn->query($sql);
        return $resultado ? $resultado->fetch_assoc() : null;
    }
    
    /**
     * Registrar auditoría
     */
    private function registrar_auditoria($usuario_id, $tabla, $operacion, $registro_id, $valores_ant, $valores_nuev) {
        $usuario_id = intval($usuario_id);
        $registro_id = intval($registro_id);
        $valores_ant = $valores_ant ? json_encode($valores_ant) : null;
        $valores_nuev = $valores_nuev ? json_encode($valores_nuev) : null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $valores_ant = $valores_ant ? $this->conn->real_escape_string($valores_ant) : null;
        $valores_nuev = $valores_nuev ? $this->conn->real_escape_string($valores_nuev) : null;
        $ip = $this->conn->real_escape_string($ip);
        $user_agent = $this->conn->real_escape_string($user_agent);
        
        $sql = "INSERT INTO auditoria (usuario_id, tabla, operacion, registro_id, valores_anteriores, valores_nuevos, ip_address, user_agent)
                VALUES ($usuario_id, '$tabla', '$operacion', $registro_id, " . 
                ($valores_ant ? "'$valores_ant'" : "NULL") . ", " .
                ($valores_nuev ? "'$valores_nuev'" : "NULL") . ", '$ip', '$user_agent')";
        
        $this->conn->query($sql);
    }
}
?>
