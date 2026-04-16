<?php
// Dashboard principal
global $conn;
require_once("../config/permisos.php");

if (!isset($_SESSION['usuario'])) {
    header('Location: ' . ruta('login'));
    exit;
}

// Obtener estadísticas (conexión cargada por router.php)

$stats = array(
    'carpetas' => 0,
    'prestamos_activos' => 0,
    'prestamos_vencidos' => 0,
    'devoluciones' => 0
);

$sql = "SELECT COUNT(*) as total FROM carpeta_fiscal";
$result = $conn->query($sql);
if ($result) $stats['carpetas'] = $result->fetch_assoc()['total'];

$sql = "SELECT COUNT(*) as total FROM prestamo WHERE estado = 'PENDIENTE'";
$result = $conn->query($sql);
if ($result) $stats['prestamos_activos'] = $result->fetch_assoc()['total'];

$sql = "SELECT COUNT(*) as total FROM prestamo WHERE estado = 'PENDIENTE' AND fecha_vencimiento < NOW()";
$result = $conn->query($sql);
if ($result) $stats['prestamos_vencidos'] = $result->fetch_assoc()['total'];

$sql = "SELECT COUNT(*) as total FROM devolucion";
$result = $conn->query($sql);
if ($result) $stats['devoluciones'] = $result->fetch_assoc()['total'];
?>

<div class="page-header">
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?></h1>
    <p>Panel de Control - <?php echo date('d/m/Y H:i'); ?></p>
</div>

<!-- Alertas -->
<?php if ($stats['prestamos_vencidos'] > 0): ?>
<div class="alert alert-danger">
    <strong>Atención:</strong> Hay <?php echo $stats['prestamos_vencidos']; ?> préstamo(s) vencido(s) que requieren atención inmediata.
    <a href="<?php echo ruta('reporte_vencidos'); ?>" class="btn btn-danger btn-sm" style="float: right; margin-top: -5px;">Ver Reporte</a>
</div>
<?php endif; ?>

<!-- Estadísticas en Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);">F</div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $stats['carpetas']; ?></div>
            <div class="stat-label">Carpetas Registradas</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">P</div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $stats['prestamos_activos']; ?></div>
            <div class="stat-label">Préstamos Activos</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">V</div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $stats['prestamos_vencidos']; ?></div>
            <div class="stat-label">Préstamos Vencidos</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">D</div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $stats['devoluciones']; ?></div>
            <div class="stat-label">Devoluciones</div>
        </div>
    </div>
</div>

<!-- Acciones Rápidas -->
<div class="card">
    <h3>Acciones Rápidas</h3>
    <div class="stats-grid" style="margin-top: 1rem;">
        <?php if (tienePermiso('crear_carpetas')): ?>
        <a href="<?php echo ruta('carpeta_registrar'); ?>" class="btn btn-primary" style="text-align: center; text-decoration: none;">
            Registrar Carpeta
        </a>
        <?php endif; ?>

        <?php if (tienePermiso('prestar_carpetas')): ?>
        <a href="<?php echo ruta('prestamo_registrar'); ?>" class="btn btn-primary" style="text-align: center; text-decoration: none;">
            Crear Préstamo
        </a>
        <?php endif; ?>

        <?php if (tienePermiso('ver_carpetas')): ?>
        <a href="<?php echo ruta('carpeta_buscar'); ?>" class="btn btn-primary" style="text-align: center; text-decoration: none;">
            Buscar Carpeta
        </a>
        <?php endif; ?>

        <?php if (tienePermiso('ver_reportes')): ?>
        <a href="<?php echo ruta('reporte_dependencia'); ?>" class="btn btn-primary" style="text-align: center; text-decoration: none;">
            Ver Reportes
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Últimas Devoluciones -->
<div class="card">
    <h3>Últimas Devoluciones</h3>
    <?php 
    $sql = "SELECT p.numero_guia, d.fecha_devolucion, COUNT(dp.id) as carpetas 
            FROM devolucion d 
            LEFT JOIN prestamo p ON d.prestamo_id = p.id 
            LEFT JOIN detalle_prestamo dp ON p.id = dp.prestamo_id 
            GROUP BY d.id 
            ORDER BY d.fecha_devolucion DESC 
            LIMIT 5";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo '<table class="result-table"><thead><tr><th>Fecha</th><th>Guía</th><th>Carpetas</th></tr></thead><tbody>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . date('d/m/Y H:i', strtotime($row['fecha_devolucion'])) . '</td>';
            echo '<td><strong>' . htmlspecialchars($row['numero_guia']) . '</strong></td>';
            echo '<td><span class="badge badge-activo">' . $row['carpetas'] . '</span></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p class="text-muted">No hay devoluciones registradas</p>';
    }
    ?>
</div>
