<?php
// Reporte: Préstamos y carpetas por dependencia
$sql = "SELECT d.id, d.nombre,
        COUNT(DISTINCT p.id) as total_prestamos,
        SUM(CASE WHEN p.estado = 'PENDIENTE' THEN 1 ELSE 0 END) as prestamos_pendientes,
        SUM(CASE WHEN p.estado = 'DEVUELTO' THEN 1 ELSE 0 END) as prestamos_devueltos,
        COUNT(dp.id) as total_carpetas,
        MAX(p.fecha_prestamo) as ultima_fecha
        FROM dependencia d
        LEFT JOIN prestamo p ON d.id = p.dependencia_id
        LEFT JOIN detalle_prestamo dp ON p.id = dp.prestamo_id
        GROUP BY d.id, d.nombre
        ORDER BY total_prestamos DESC";

$res = $conn->query($sql);

// Estadísticas generales
$sql_general = "SELECT COUNT(*) as total_prestamosp, 
                       SUM(CASE WHEN estado = 'PENDIENTE' THEN 1 ELSE 0 END) as pendientes,
                       SUM(CASE WHEN estado = 'DEVUELTO' THEN 1 ELSE 0 END) as devueltos,
                       COUNT(DISTINCT dependencia_id) as total_dependencias
                FROM prestamo";
$stats = $conn->query($sql_general)->fetch_assoc();
?>

<div class="page-header">
    <h1>Reporte: Préstamos por Dependencia</h1>
    <p>Estadísticas de préstamos y carpetas asignadas (Generado: <?php echo date('d/m/Y H:i'); ?>)</p>
</div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--info) 0%, #0284c7 100%);">🏢</div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $stats['total_dependencias']; ?></div>
                <div class="stat-label">Dependencias Activas</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);">⏳</div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $stats['pendientes']; ?></div>
                <div class="stat-label">Préstamos Pendientes</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--success) 0%, #059669 100%);">✓</div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $stats['devueltos']; ?></div>
                <div class="stat-label">Préstamos Completados</div>
            </div>
        </div>
    </div>

    <div class="card" style="overflow-x: auto;">
        <table class="result-table">
            <thead>
                <tr>
                    <th>Dependencia</th>
                    <th class="text-center">Total Préstamos</th>
                    <th class="text-center">Pendientes</th>
                    <th class="text-center">Devueltos</th>
                    <th class="text-center">Total Carpetas</th>
                    <th>Última Actividad</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($res && $res->num_rows > 0) {
                    while($r = $res->fetch_assoc()) { 
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($r['nombre']); ?></strong></td>
                    <td class="text-center"><strong><?php echo $r['total_prestamos']; ?></strong></td>
                    <td class="text-center">
                        <?php echo $r['prestamos_pendientes'] > 0 
                            ? '<span class="badge badge-pendiente">' . $r['prestamos_pendientes'] . '</span>' 
                            : '<span class="text-muted">0</span>'; ?>
                    </td>
                    <td class="text-center">
                        <?php echo $r['prestamos_devueltos'] > 0 
                            ? '<span class="badge badge-devuelto">' . $r['prestamos_devueltos'] . '</span>' 
                            : '<span class="text-muted">0</span>'; ?>
                    </td>
                    <td class="text-center"><?php echo $r['total_carpetas'] ?? 0; ?></td>
                    <td><?php echo $r['ultima_fecha'] ? date('d/m/Y', strtotime($r['ultima_fecha'])) : '<span class="text-muted">N/A</span>'; ?></td>
                    <td class="text-center">
                        <a href="<?php echo ruta('prestamo_listar'); ?>" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">Ver Préstamos</a>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center text-muted p-4'>No hay datos registrados para mostrar</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="card mt-4">
        <h3 class="mb-2">Resumen Ejecutivo</h3>
        <p class="mb-1 text-muted">De un total de <strong><?php echo $stats['total_prestamosp']; ?></strong> operaciones registradas en el sistema:</p>
        <ul class="mt-2">
            <li style="border: none; padding: 0.2rem 0;">✓ Se ha completado el <strong><?php echo $stats['devueltos']; ?></strong> préstamos (Devueltos).</li>
            <li style="border: none; padding: 0.2rem 0;">⏳ Actualmente existen <strong class="text-warning"><?php echo $stats['pendientes']; ?></strong> solicitudes pendientes de retorno.</li>
        </ul>
    </div>
