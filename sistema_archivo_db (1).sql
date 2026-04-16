-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-04-2026 a las 04:34:11
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema_archivo_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tabla` varchar(50) NOT NULL,
  `operacion` varchar(20) NOT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `valores_anteriores` longtext DEFAULT NULL,
  `valores_nuevos` longtext DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `fecha_operacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`id`, `usuario_id`, `tabla`, `operacion`, `registro_id`, `valores_anteriores`, `valores_nuevos`, `ip_address`, `fecha_operacion`) VALUES
(1, 1, 'carpeta_fiscal', 'INSERT', 4, NULL, '{\"numero\":\"30-11\",\"imputado\":\"sdgdgsdg\",\"delito\":\"sdgsdg\",\"agraviado\":\"sdgsgdsgd\",\"estado\":\"Activo\",\"ubicacion\":\"a1\"}', '::1', '2026-04-14 20:21:24'),
(2, 1, 'usuario', 'LOGIN', NULL, NULL, NULL, '::1', '2026-04-14 20:26:23'),
(3, 1, 'usuario', 'LOGIN', NULL, NULL, NULL, '::1', '2026-04-14 20:32:03'),
(4, 1, 'usuario', 'LOGIN', NULL, NULL, NULL, '::1', '2026-04-14 20:37:39'),
(5, 1, 'usuario', 'LOGIN', NULL, NULL, NULL, '::1', '2026-04-14 20:56:28'),
(6, 1, 'usuario', 'LOGIN', NULL, NULL, NULL, '::1', '2026-04-14 21:04:49'),
(7, 1, 'usuario', 'LOGIN', NULL, NULL, NULL, '::1', '2026-04-14 21:22:09'),
(8, 1, 'usuario', 'LOGIN', NULL, NULL, NULL, '::1', '2026-04-14 21:24:13'),
(9, 1, 'usuario', 'LOGIN', NULL, NULL, NULL, '::1', '2026-04-14 21:27:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carpeta_fiscal`
--

CREATE TABLE `carpeta_fiscal` (
  `id` int(11) NOT NULL,
  `numero_carpeta` varchar(50) NOT NULL,
  `imputado` varchar(100) NOT NULL,
  `delito` varchar(100) NOT NULL,
  `agraviado` varchar(100) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `usuario_creacion_id` int(11) DEFAULT NULL,
  `fecha_ultima_modificacion` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carpeta_fiscal`
--

INSERT INTO `carpeta_fiscal` (`id`, `numero_carpeta`, `imputado`, `delito`, `agraviado`, `estado`, `ubicacion`, `observaciones`, `fecha_registro`, `usuario_creacion_id`, `fecha_ultima_modificacion`) VALUES
(1, '001', 'Juan Pérez', 'Robo a mano armada', 'Carlos López', 'Proceso', 'Estante A-01', NULL, '2026-04-15 01:13:59', 1, NULL),
(2, '002', 'José García', 'Robo', 'Julio Morales', 'Archivado', 'Jr. Lima 123', NULL, '2026-04-15 01:13:59', 1, NULL),
(3, '003', 'Pedro Rodríguez', 'Hurto', 'María González', 'Archivado', 'Chiclayo', NULL, '2026-04-15 01:13:59', 1, NULL),
(4, '30-11', 'sdgdgsdg', 'sdgsdg', 'sdgsgdsgd', 'Activo', 'a1', NULL, '2026-04-14 20:21:24', 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dependencia`
--

CREATE TABLE `dependencia` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'activo',
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `dependencia`
--

INSERT INTO `dependencia` (`id`, `nombre`, `descripcion`, `telefono`, `email`, `estado`, `fecha_creacion`) VALUES
(1, 'Fiscalía Penal 1', 'Fiscalía Especializada en Delitos Penales Grupo 1', NULL, NULL, 'activo', '2026-04-15 01:13:59'),
(2, 'Fiscalía Penal 2', 'Fiscalía Especializada en Delitos Penales Grupo 2', NULL, NULL, 'activo', '2026-04-15 01:13:59'),
(3, 'Fiscalía Civil', 'Fiscalía Especializada en Asuntos Civiles', NULL, NULL, 'activo', '2026-04-15 01:13:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_prestamo`
--

CREATE TABLE `detalle_prestamo` (
  `id` int(11) NOT NULL,
  `prestamo_id` int(11) NOT NULL,
  `carpeta_id` int(11) NOT NULL,
  `estado` varchar(50) DEFAULT 'PRESTADA',
  `fecha_asignacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_prestamo`
--

INSERT INTO `detalle_prestamo` (`id`, `prestamo_id`, `carpeta_id`, `estado`, `fecha_asignacion`) VALUES
(1, 1, 1, 'PRESTADA', '2026-04-15 01:13:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devolucion`
--

CREATE TABLE `devolucion` (
  `id` int(11) NOT NULL,
  `prestamo_id` int(11) NOT NULL,
  `fecha_devolucion` date DEFAULT NULL,
  `fecha_programada` date DEFAULT NULL,
  `estado` varchar(50) DEFAULT 'PENDIENTE',
  `dias_vencimiento` int(3) DEFAULT 0,
  `multa` decimal(10,2) DEFAULT 0.00,
  `observaciones` text DEFAULT NULL,
  `usuario_notificacion_id` int(11) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_carpeta`
--

CREATE TABLE `estado_carpeta` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado_carpeta`
--

INSERT INTO `estado_carpeta` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Activo', 'Carpeta en proceso activo'),
(2, 'Archivado', 'Carpeta archivada'),
(3, 'Proceso', 'En trámite judicial'),
(4, 'Sentenciado', 'Con sentencia emitida');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamo`
--

CREATE TABLE `prestamo` (
  `id` int(11) NOT NULL,
  `numero_guia` varchar(50) NOT NULL,
  `dependencia_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `fecha_prestamo` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `dias_prestamo` int(3) DEFAULT 7,
  `estado` varchar(50) DEFAULT 'PENDIENTE',
  `observaciones` text DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prestamo`
--

INSERT INTO `prestamo` (`id`, `numero_guia`, `dependencia_id`, `usuario_id`, `fecha_prestamo`, `fecha_vencimiento`, `dias_prestamo`, `estado`, `observaciones`, `fecha_creacion`) VALUES
(1, 'PREST-001', 2, 1, '2026-04-07', '2026-04-14', 7, 'PENDIENTE', NULL, '2026-04-15 01:13:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `rol` varchar(50) DEFAULT 'usuario',
  `estado` varchar(20) DEFAULT 'activo',
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `username`, `password`, `email`, `rol`, `estado`, `fecha_creacion`) VALUES
(1, 'admin', '1234', 'admin@sistema.com', 'administrador', 'activo', '2026-04-15 01:13:59'),
(2, 'usuario1', '1234', 'usuario1@sistema.com', 'usuario', 'activo', '2026-04-15 01:13:59');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `carpeta_fiscal`
--
ALTER TABLE `carpeta_fiscal`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_carpeta` (`numero_carpeta`),
  ADD KEY `usuario_creacion_id` (`usuario_creacion_id`);

--
-- Indices de la tabla `dependencia`
--
ALTER TABLE `dependencia`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_prestamo`
--
ALTER TABLE `detalle_prestamo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prestamo_id` (`prestamo_id`),
  ADD KEY `carpeta_id` (`carpeta_id`);

--
-- Indices de la tabla `devolucion`
--
ALTER TABLE `devolucion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prestamo_id` (`prestamo_id`),
  ADD KEY `usuario_notificacion_id` (`usuario_notificacion_id`);

--
-- Indices de la tabla `estado_carpeta`
--
ALTER TABLE `estado_carpeta`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `prestamo`
--
ALTER TABLE `prestamo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_guia` (`numero_guia`),
  ADD KEY `dependencia_id` (`dependencia_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `carpeta_fiscal`
--
ALTER TABLE `carpeta_fiscal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `dependencia`
--
ALTER TABLE `dependencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `detalle_prestamo`
--
ALTER TABLE `detalle_prestamo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `devolucion`
--
ALTER TABLE `devolucion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estado_carpeta`
--
ALTER TABLE `estado_carpeta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `prestamo`
--
ALTER TABLE `prestamo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `auditoria_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `carpeta_fiscal`
--
ALTER TABLE `carpeta_fiscal`
  ADD CONSTRAINT `carpeta_fiscal_ibfk_1` FOREIGN KEY (`usuario_creacion_id`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `detalle_prestamo`
--
ALTER TABLE `detalle_prestamo`
  ADD CONSTRAINT `detalle_prestamo_ibfk_1` FOREIGN KEY (`prestamo_id`) REFERENCES `prestamo` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_prestamo_ibfk_2` FOREIGN KEY (`carpeta_id`) REFERENCES `carpeta_fiscal` (`id`);

--
-- Filtros para la tabla `devolucion`
--
ALTER TABLE `devolucion`
  ADD CONSTRAINT `devolucion_ibfk_1` FOREIGN KEY (`prestamo_id`) REFERENCES `prestamo` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `devolucion_ibfk_2` FOREIGN KEY (`usuario_notificacion_id`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `prestamo`
--
ALTER TABLE `prestamo`
  ADD CONSTRAINT `prestamo_ibfk_1` FOREIGN KEY (`dependencia_id`) REFERENCES `dependencia` (`id`),
  ADD CONSTRAINT `prestamo_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
