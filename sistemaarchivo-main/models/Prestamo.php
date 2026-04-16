<?php
/**
 * Modelo Prestamo
 * Gestiona operaciones de préstamos de carpetas
 */
class Prestamo {
    private $conn;
    
    public function __construct($conexion) {
        $this->conn = $conexion;
    }
    
    /**
     * Generar número de guía único
     */
    private function generar_numero_guia() {
        $prefijo = "PREST-";
        $numero = rand(10000, 99999);
        
        // Validar que sea único
        while (true) {
            $sql = "SELECT id FROM prestamo WHERE numero_guia = '$prefijo$numero'";
            $resultado = $this->conn->query($sql);
            
            if ($resultado->num_rows == 0) {
                break;
            }
            $numero = rand(10000, 99999);
        }
        
        return $prefijo . $numero;
    }
    
    /**
     * Registrar nuevo préstamo
     */
    public function registrar($dependencia_id, $usuario_id, $carpetas_ids, $dias_prestamo = 7) {
        // Validaciones
        if (empty($dependencia_id) || empty($carpetas_ids)) {
            return ['success' => false, 'message' => 'Dependencia y carpetas son obligatorias'];
        }
        
        $dependencia_id = intval($dependencia_id);
        $usuario_id = intval($usuario_id);
        $dias_prestamo = intval($dias_prestamo);
        
        // Validar que la dependencia existe
        $verificar_dep = "SELECT id FROM dependencia WHERE id = $dependencia_id AND estado = 'activo'";
        if (!$this->conn->query($verificar_dep)->fetch_assoc()) {
            return ['success' => false, 'message' => 'Dependencia inválida'];
        }
        
        // Generar número de guía
        $numero_guia = $this->generar_numero_guia();
        $fecha_prestamo = date("Y-m-d");
        $fecha_vencimiento = date("Y-m-d", strtotime("+$dias_prestamo days"));
        
        // Iniciar transacción
        $this->conn->begin_transaction();
        
        try {
            // Insertar préstamo
            $sql = "INSERT INTO prestamo (numero_guia, dependencia_id, usuario_id, fecha_prestamo, fecha_vencimiento, dias_prestamo, estado)
                    VALUES ('$numero_guia', $dependencia_id, $usuario_id, '$fecha_prestamo', '$fecha_vencimiento', $dias_prestamo, 'PENDIENTE')";
            
            if (!$this->conn->query($sql)) {
                throw new Exception("Error al crear préstamo: " . $this->conn->error);
            }
            
            $prestamo_id = $this->conn->insert_id;
            
            // Insertar detalles de préstamo
            foreach ($carpetas_ids as $carpeta_id) {
                $carpeta_id = intval($carpeta_id);
                
                // Validar que la carpeta existe
                $verificar_carp = "SELECT id FROM carpeta_fiscal WHERE id = $carpeta_id";
                if (!$this->conn->query($verificar_carp)->fetch_assoc()) {
                    throw new Exception("Carpeta ID $carpeta_id no existe");
                }
                
                $sql_detalle = "INSERT INTO detalle_prestamo (prestamo_id, carpeta_id, estado)
                               VALUES ($prestamo_id, $carpeta_id, 'PRESTADA')";
                
                if (!$this->conn->query($sql_detalle)) {
                    throw new Exception("Error al asignar carpeta: " . $this->conn->error);
                }
            }
            
            // Registrar auditoría
            $this->registrar_auditoria($usuario_id, 'prestamo', 'INSERT', $prestamo_id, null, 
                                      ['numero_guia' => $numero_guia, 'dependencia_id' => $dependencia_id]);
            
            $this->conn->commit();
            
            return [
                'success' => true,
                'message' => "Préstamo registrado exitosamente",
                'numero_guia' => $numero_guia,
                'fecha_vencimiento' => $fecha_vencimiento,
                'prestamo_id' => $prestamo_id
            ];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Obtener préstamo por número de guía
     */
    public function obtener_por_guia($numero_guia) {
        $numero_guia = $this->conn->real_escape_string($numero_guia);
        $sql = "SELECT p.*, d.nombre as dependencia, u.username as usuario
                FROM prestamo p
                LEFT JOIN dependencia d ON p.dependencia_id = d.id
                LEFT JOIN usuario u ON p.usuario_id = u.id
                WHERE p.numero_guia = '$numero_guia'";
        
        $resultado = $this->conn->query($sql);
        return $resultado ? $resultado->fetch_assoc() : null;
    }
    
    /**
     * Obtener todos los préstamos
     */
    public function obtener_todos($filtro = null) {
        $sql = "SELECT p.*, d.nombre as dependencia, u.username as usuario
                FROM prestamo p
                LEFT JOIN dependencia d ON p.dependencia_id = d.id
                LEFT JOIN usuario u ON p.usuario_id = u.id";
        
        if ($filtro == 'vencidos') {
            $sql .= " WHERE p.fecha_vencimiento < CURDATE() AND p.estado = 'PENDIENTE'";
        } elseif ($filtro == 'proximos') {
            $sql .= " WHERE p.fecha_vencimiento <= CURDATE()+3 AND p.fecha_vencimiento >= CURDATE() AND p.estado = 'PENDIENTE'";
        }
        
        $sql .= " ORDER BY p.fecha_vencimiento ASC";
        return $this->conn->query($sql);
    }
    
    /**
     * Obtener préstamos por dependencia
     */
    public function obtener_por_dependencia($dependencia_id) {
        $dependencia_id = intval($dependencia_id);
        $sql = "SELECT p.*, COUNT(dp.id) as total_carpetas, d.nombre as dependencia
                FROM prestamo p
                LEFT JOIN detalle_prestamo dp ON p.id = dp.prestamo_id
                JOIN dependencia d ON p.dependencia_id = d.id
                WHERE p.dependencia_id = $dependencia_id
                GROUP BY p.id
                ORDER BY p.fecha_prestamo DESC";
        return $this->conn->query($sql);
    }
    
    /**
     * Obtener carpetas de un préstamo
     */
    public function obtener_carpetas_prestamo($prestamo_id) {
        $prestamo_id = intval($prestamo_id);
        $sql = "SELECT cf.* FROM carpeta_fiscal cf
                INNER JOIN detalle_prestamo dp ON cf.id = dp.carpeta_id
                WHERE dp.prestamo_id = $prestamo_id";
        return $this->conn->query($sql);
    }
    
    /**
     * Verificar préstamos vencidos y generar notificaciones
     */
    public function generar_notificaciones_vencimiento() {
        $hoy = date("Y-m-d");
        
        // Obtener préstamos vencidos sin devolución registrada
        $sql = "SELECT p.id, p.numero_guia, p.fecha_vencimiento, 
                DATEDIFF('$hoy', p.fecha_vencimiento) as dias_vencimiento,
                d.id as dependencia_id, d.nombre as dependencia
                FROM prestamo p
                JOIN dependencia d ON p.dependencia_id = d.id
                WHERE p.fecha_vencimiento < '$hoy' 
                AND p.estado = 'PENDIENTE'
                AND NOT EXISTS (SELECT 1 FROM devolucion WHERE prestamo_id = p.id)";
        
        $resultado = $this->conn->query($sql);
        $notificaciones = [];
        
        if ($resultado && $resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                // Crear registro de devolución vencida
                $dias_venc = $row['dias_vencimiento'];
                $prestamo_id = $row['id'];
                
                // Verificar si ya existe
                $verificar = "SELECT id FROM devolucion WHERE prestamo_id = $prestamo_id";
                if (!$this->conn->query($verificar)->fetch_assoc()) {
                    $sql_dev = "INSERT INTO devolucion (prestamo_id, fecha_programada, estado, dias_vencimiento)
                               VALUES ($prestamo_id, '{$row['fecha_vencimiento']}', 'VENCIDA', $dias_venc)";
                    $this->conn->query($sql_dev);
                }
                
                $notificaciones[] = $row;
            }
        }
        
        return $notificaciones;
    }
    
    /**
     * Registrar devolución
     */
    public function registrar_devolucion($prestamo_id, $usuario_id) {
        $prestamo_id = intval($prestamo_id);
        $usuario_id = intval($usuario_id);
        $fecha_hoy = date("Y-m-d");
        
        // Obtener préstamo
        $sql_prest = "SELECT * FROM prestamo WHERE id = $prestamo_id";
        $prestamo = $this->conn->query($sql_prest)->fetch_assoc();
        
        if (!$prestamo) {
            return ['success' => false, 'message' => 'Préstamo no existe'];
        }
        
        // Calcular días de vencimiento
        $dias_venc = max(0, (strtotime($fecha_hoy) - strtotime($prestamo['fecha_vencimiento'])) / (60 * 60 * 24));
        
        // Actualizar devolucion
        $sql_dev = "UPDATE devolucion SET fecha_devolucion = '$fecha_hoy', estado = 'DEVUELTO', usuario_notificacion_id = $usuario_id
                   WHERE prestamo_id = $prestamo_id";
        $this->conn->query($sql_dev);
        
        // Actualizar préstamo
        $sql = "UPDATE prestamo SET estado = 'DEVUELTO' WHERE id = $prestamo_id";
        
        if ($this->conn->query($sql)) {
            $this->registrar_auditoria($usuario_id, 'prestamo', 'DEVOLUCION', $prestamo_id, $prestamo, null);
            return ['success' => true, 'message' => 'Devolución registrada', 'dias_vencimiento' => $dias_venc];
        }
        
        return ['success' => false, 'message' => 'Error al registrar devolución'];
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
        
        $valores_ant = $valores_ant ? $this->conn->real_escape_string($valores_ant) : null;
        $valores_nuev = $valores_nuev ? $this->conn->real_escape_string($valores_nuev) : null;
        $ip = $this->conn->real_escape_string($ip);
        
        $sql = "INSERT INTO auditoria (usuario_id, tabla, operacion, registro_id, valores_anteriores, valores_nuevos, ip_address)
                VALUES ($usuario_id, '$tabla', '$operacion', $registro_id, " . 
                ($valores_ant ? "'$valores_ant'" : "NULL") . ", " .
                ($valores_nuev ? "'$valores_nuev'" : "NULL") . ", '$ip')";
        
        $this->conn->query($sql);
    }
}
?>
