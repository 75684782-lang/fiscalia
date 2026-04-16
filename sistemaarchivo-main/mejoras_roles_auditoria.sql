-- Mejoras: Roles con acciones y auditoría expandida
-- Fecha: 15 de abril de 2026

-- Agregar más roles a la tabla usuario (ejemplos)
INSERT INTO `usuario` (`username`, `password`, `email`, `rol`, `estado`) VALUES
('moderador', '1234', 'moderador@sistema.com', 'moderador', 'activo'),
('visor', '1234', 'visor@sistema.com', 'visor', 'activo');

-- Crear tabla de permisos
CREATE TABLE IF NOT EXISTS `permiso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertar permisos básicos
INSERT INTO `permiso` (`nombre`, `descripcion`) VALUES
('ver_carpetas', 'Puede ver la lista de carpetas'),
('crear_carpetas', 'Puede crear nuevas carpetas'),
('editar_carpetas', 'Puede editar carpetas existentes'),
('eliminar_carpetas', 'Puede eliminar carpetas'),
('prestar_carpetas', 'Puede prestar carpetas'),
('devolver_carpetas', 'Puede registrar devoluciones'),
('ver_prestamos', 'Puede ver préstamos'),
('ver_reportes', 'Puede ver reportes'),
('admin_usuarios', 'Puede administrar usuarios'),
('admin_roles', 'Puede administrar roles y permisos'),
('ver_auditoria', 'Puede ver registros de auditoría');

-- Crear tabla rol_permiso
CREATE TABLE IF NOT EXISTS `rol_permiso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rol` varchar(50) NOT NULL,
  `permiso_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`permiso_id`) REFERENCES `permiso`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Asignar permisos a roles
-- Administrador: todos los permisos
INSERT INTO `rol_permiso` (`rol`, `permiso_id`) SELECT 'administrador', id FROM `permiso`;

-- Moderador: ver, crear, editar, prestar, devolver, ver prestamos, ver reportes
INSERT INTO `rol_permiso` (`rol`, `permiso_id`) 
SELECT 'moderador', id FROM `permiso` WHERE nombre IN ('ver_carpetas', 'crear_carpetas', 'editar_carpetas', 'prestar_carpetas', 'devolver_carpetas', 'ver_prestamos', 'ver_reportes');

-- Usuario: ver carpetas, prestar, devolver, ver prestamos
INSERT INTO `rol_permiso` (`rol`, `permiso_id`) 
SELECT 'usuario', id FROM `permiso` WHERE nombre IN ('ver_carpetas', 'prestar_carpetas', 'devolver_carpetas', 'ver_prestamos');

-- Visor: solo ver carpetas y prestamos
INSERT INTO `rol_permiso` (`rol`, `permiso_id`) 
SELECT 'visor', id FROM `permiso` WHERE nombre IN ('ver_carpetas', 'ver_prestamos');

-- Expandir auditoría: agregar más campos si es necesario
-- Ya tiene JSON, pero podemos agregar user_agent, etc.
ALTER TABLE `auditoria` ADD COLUMN IF NOT EXISTS `user_agent` TEXT AFTER `ip_address`;

-- Agregar índices para mejor rendimiento en auditoría
CREATE INDEX IF NOT EXISTS idx_auditoria_fecha ON `auditoria` (`fecha_operacion`);
CREATE INDEX IF NOT EXISTS idx_auditoria_usuario ON `auditoria` (`usuario_id`);
CREATE INDEX IF NOT EXISTS idx_auditoria_tabla ON `auditoria` (`tabla`);