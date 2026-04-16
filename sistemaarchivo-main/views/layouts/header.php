<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Archivo Fiscal</title>
    <?php 
    if (!defined('PUBLIC_CSS')) {
        require_once(dirname(dirname(dirname(__FILE__))) . '/config/rutas.php');
        require_once(dirname(dirname(dirname(__FILE__))) . '/config/permisos.php');
    }
    ?>
    <link rel="stylesheet" href="<?php echo PUBLIC_CSS; ?>estilos.css">
</head>
<body>

<header class="header-fixed">
    <div class="header-top">
        <div class="header-logo">
            <h1>Sistema de Archivo Fiscal</h1>
        </div>
        <div class="header-user" id="user-info">
            <?php if (isset($_SESSION['usuario'])): ?>
                <span><?php echo htmlspecialchars($_SESSION['usuario']); ?> (<?php echo htmlspecialchars($_SESSION['rol']); ?>)</span>
                <a href="<?php echo ruta('logout'); ?>" class="btn-logout">Cerrar Sesión</a>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (isset($_SESSION['usuario'])): ?>
    <nav class="nav-sidebar">
        <div class="nav-title">MÓDULOS</div>
        <ul>
            <li><a href="<?php echo ruta('dashboard'); ?>" class="nav-item">Dashboard</a></li>
            <li class="nav-section">
                <strong>Carpetas</strong>
                <ul>
                    <li><a href="<?php echo ruta('carpeta_registrar'); ?>" class="nav-subitem">Registrar</a></li>
                    <li><a href="<?php echo ruta('carpeta_listar'); ?>" class="nav-subitem">Listar</a></li>
                    <li><a href="<?php echo ruta('carpeta_buscar'); ?>" class="nav-subitem">Buscar</a></li>
                    <li><a href="<?php echo ruta('carpeta_importar'); ?>" class="nav-subitem">Importar Excel</a></li>
                </ul>
            </li>
            <li class="nav-section">
                <strong>Préstamos</strong>
                <ul>
                    <li><a href="<?php echo ruta('prestamo_registrar'); ?>" class="nav-subitem">Registrar</a></li>
                    <li><a href="<?php echo ruta('prestamo_listar'); ?>" class="nav-subitem">Listar</a></li>
                </ul>
            </li>
            <li class="nav-section">
                <strong>Reportes</strong>
                <ul>
                    <li><a href="<?php echo ruta('reporte_vencidos'); ?>" class="nav-subitem">Vencidos</a></li>
                    <li><a href="<?php echo ruta('reporte_dependencia'); ?>" class="nav-subitem">Por Dependencia</a></li>
                </ul>
            </li>
            <?php if (isset($_SESSION['usuario'])): ?>
            <li class="nav-section">
                <strong>Administración</strong>
                <ul>
                    <?php if (tienePermiso('ver_auditoria')): ?>
                    <li><a href="<?php echo ruta('admin_auditorias'); ?>" class="nav-subitem">Auditorías</a></li>
                    <?php endif; ?>
                    <?php if (tienePermiso('admin_usuarios')): ?>
                    <li><a href="<?php echo ruta('admin_usuarios'); ?>" class="nav-subitem">Gestionar Usuarios</a></li>
                    <?php endif; ?>
                    <?php if (tienePermiso('admin_roles')): ?>
                    <li><a href="<?php echo ruta('admin_asignar_roles'); ?>" class="nav-subitem">Asignar Roles</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
</header>

<div class="container" <?php echo isset($_SESSION['usuario']) ? 'style="margin-left: 260px; margin-top: 60px;"' : ''; ?>>
