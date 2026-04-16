-- Crear tabla auditoria si no existe
CREATE TABLE IF NOT EXISTS `auditoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11),
  `tabla` varchar(100),
  `operacion` varchar(50),
  `registro_id` int(11),
  `valores_anteriores` JSON,
  `valores_nuevos` JSON,
  `ip_address` varchar(50),
  `fecha_operacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  FOREIGN KEY (`usuario_id`) REFERENCES `usuario`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
