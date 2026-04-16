-- Estructura mejorada de la base de datos para Sistema de Archivo
-- phpMyAdmin SQL Dump version 5.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ========================================
-- 1. TABLA: usuario (Multiusuario + Roles)
-- ========================================
DROP TABLE IF EXISTS `auditoria`;
DROP TABLE IF EXISTS `devolucion`;
DROP TABLE IF EXISTS `detalle_prestamo`;
DROP TABLE IF EXISTS `prestamo`;
DROP TABLE IF EXISTS `carpeta_fiscal`;
DROP TABLE IF EXISTS `estado_carpeta`;
DROP TABLE IF EXISTS `dependencia`;
DROP TABLE IF EXISTS `usuario`;

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100),
  `rol` varchar(50) DEFAULT 'usuario',
  `estado` varchar(20) DEFAULT 'activo',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `usuario` (`id`, `username`, `password`, `email`, `rol`, `estado`) VALUES
(1, 'admin', '1234', 'admin@sistema.com', 'administrador', 'activo'),
(2, 'usuario1', '1234', 'usuario1@sistema.com', 'usuario', 'activo');

-- ========================================
-- 2. TABLA: dependencia
-- ========================================
CREATE TABLE `dependencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `telefono` varchar(20),
  `email` varchar(100),
  `estado` varchar(20) DEFAULT 'activo',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `dependencia` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Fiscalía Penal 1', 'Fiscalía Especializada en Delitos Penales Grupo 1'),
(2, 'Fiscalía Penal 2', 'Fiscalía Especializada en Delitos Penales Grupo 2'),
(3, 'Fiscalía Civil', 'Fiscalía Especializada en Asuntos Civiles');

-- ========================================
-- 3. TABLA: estado_carpeta
-- ========================================
CREATE TABLE `estado_carpeta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `estado_carpeta` (`nombre`, `descripcion`) VALUES
('Activo', 'Carpeta en proceso activo'),
('Archivado', 'Carpeta archivada'),
('Proceso', 'En trámite judicial'),
('Sentenciado', 'Con sentencia emitida');

-- ========================================
-- 4. TABLA: carpeta_fiscal (Requerimiento 1)
-- ========================================
CREATE TABLE `carpeta_fiscal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_carpeta` varchar(50) NOT NULL,
  `imputado` varchar(100) NOT NULL,
  `delito` varchar(100) NOT NULL,
  `agraviado` varchar(100) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `observaciones` text,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  `usuario_creacion_id` int(11),
  `fecha_ultima_modificacion` datetime ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_carpeta` (`numero_carpeta`),
  FOREIGN KEY (`usuario_creacion_id`) REFERENCES `usuario`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `carpeta_fiscal` (`id`, `numero_carpeta`, `imputado`, `delito`, `agraviado`, `estado`, `ubicacion`, `usuario_creacion_id`) VALUES
(1, '001', 'Juan Pérez', 'Robo a mano armada', 'Carlos López', 'Proceso', 'Estante A-01', 1),
(2, '002', 'José García', 'Robo', 'Julio Morales', 'Archivado', 'Jr. Lima 123', 1),
(3, '003', 'Pedro Rodríguez', 'Hurto', 'María González', 'Archivado', 'Chiclayo', 1);

-- ========================================
-- 5. TABLA: prestamo (Requerimiento 4)
-- ========================================
CREATE TABLE `prestamo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_guia` varchar(50) NOT NULL,
  `dependencia_id` int(11) NOT NULL,
  `usuario_id` int(11),
  `fecha_prestamo` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `dias_prestamo` int(3) DEFAULT 7,
  `estado` varchar(50) DEFAULT 'PENDIENTE',
  `observaciones` text,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_guia` (`numero_guia`),
  FOREIGN KEY (`dependencia_id`) REFERENCES `dependencia`(`id`),
  FOREIGN KEY (`usuario_id`) REFERENCES `usuario`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `prestamo` (`id`, `numero_guia`, `dependencia_id`, `usuario_id`, `fecha_prestamo`, `fecha_vencimiento`, `dias_prestamo`, `estado`) VALUES
(1, 'PREST-001', 2, 1, '2026-04-07', '2026-04-14', 7, 'PENDIENTE');

-- ========================================
-- 6. TABLA: detalle_prestamo
-- ========================================
CREATE TABLE `detalle_prestamo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prestamo_id` int(11) NOT NULL,
  `carpeta_id` int(11) NOT NULL,
  `estado` varchar(50) DEFAULT 'PRESTADA',
  `fecha_asignacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `prestamo_id` (`prestamo_id`),
  KEY `carpeta_id` (`carpeta_id`),
  FOREIGN KEY (`prestamo_id`) REFERENCES `prestamo`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`carpeta_id`) REFERENCES `carpeta_fiscal`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `detalle_prestamo` (`id`, `prestamo_id`, `carpeta_id`, `estado`) VALUES
(1, 1, 1, 'PRESTADA');

-- ========================================
-- 7. TABLA: devolucion (Requerimiento 6)
-- ========================================
CREATE TABLE `devolucion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prestamo_id` int(11) NOT NULL,
  `fecha_devolucion` date,
  `fecha_programada` date,
  `estado` varchar(50) DEFAULT 'PENDIENTE',
  `dias_vencimiento` int(3) DEFAULT 0,
  `multa` decimal(10,2) DEFAULT 0.00,
  `observaciones` text,
  `usuario_notificacion_id` int(11),
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`prestamo_id`) REFERENCES `prestamo`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`usuario_notificacion_id`) REFERENCES `usuario`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- 8. TABLA: auditoria (Trazabilidad - Requerimiento no funcional 4)
-- ========================================
CREATE TABLE `auditoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `tabla` varchar(50) NOT NULL,
  `operacion` varchar(20) NOT NULL,
  `registro_id` int(11),
  `valores_anteriores` longtext,
  `valores_nuevos` longtext,
  `ip_address` varchar(50),
  `fecha_operacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`usuario_id`) REFERENCES `usuario`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- AUTO_INCREMENT
-- ========================================
ALTER TABLE `usuario` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `dependencia` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `carpeta_fiscal` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `prestamo` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `detalle_prestamo` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `devolucion` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `auditoria` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
