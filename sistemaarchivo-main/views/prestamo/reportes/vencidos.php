<?php
$hoy = date("Y-m-d");

$sql = "SELECT p.id, p.numero_guia, d.nombre, p.fecha_vencimiento, p.estado,
        DATEDIFF('$hoy', p.fecha_vencimiento) as dias_vencimiento,
        COUNT(dp.id) as total_carpetas
        FROM prestamo p
        JOIN dependencia d ON p.dependencia_id = d.id
        LEFT JOIN detalle_prestamo dp ON p.id = dp.prestamo_id
        WHERE p.fecha_vencimiento < '$hoy' AND p.estado = 'PENDIENTE'
        GROUP BY p.id
        ORDER BY p.fecha_vencimiento ASC";

$res = $conn->query($sql);
$total_vencidas = $res ? $res->num_rows : 0;
?>

<div class="page-header">
    <h1>Reporte: Carpetas Vencidas</h1>
    <p>Listado de préstamos que han excedido su fecha de devolución (Actualizado: <?php echo date('d/m/Y H:i'); ?>)</p>
</div>
    
    <div class="stats-grid mb-4">
        <div class="stat-card" style="border-color: var(--danger);">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--danger) 0%, #991b1b 100%);">
                ⚠
            </div>
            <div class="stat-content">
                <div class="stat-number text-danger"><?php echo $total_vencidas; ?></div>
                <div class="stat-label">Total Préstamos Vencidos</div>
            </div>
        </div>
    </div>

    <?php if ($total_vencidas > 0) { ?>
    
    <div class="card" style="overflow-x: auto;">
        <table class="result-table">
            <thead>
                <tr>
                    <th>Guía</th>
                    <th>Dependencia</th>
                    <th>Fecha Vencimiento</th>
                    <th>Días Vencidos</th>
                    <th class="text-center">Carpetas</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                while($r = $res->fetch_assoc()) { ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($r['numero_guia']); ?></strong></td>
                    <td><?php echo htmlspecialchars($r['nombre']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($r['fecha_vencimiento'])); ?></td>
                    <td class="text-danger">
                        <strong>+<?php echo $r['dias_vencimiento']; ?> días</strong>
                    </td>
                    <td class="text-center"><?php echo $r['total_carpetas']; ?></td>
                    <td class="text-center">
                        <span class="badge badge-sentenciado">VENCIDO</span>
                    </td>
                    <td class="text-center">
                        <a href="<?php echo ruta('prestamo_devolucion', ['id' => $r['id']]); ?>" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">Generar Nota</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="card mt-4">
        <h3 class="mb-2">Acción Requerida</h3>
        <p class="text-muted">Es necesario contactar a las dependencias marcadas para solicitar la devolución inmediata de las carpetas fiscales retenidas.</p>
    </div>

    <?php } else { ?>
    
    <div class="card text-center p-4">
        <div style="font-size: 3rem; margin-bottom: 1rem;">🌟</div>
        <h3 class="text-success mb-2">¡Excelente! No hay préstamos vencidos</h3>
        <p class="text-muted">Todas las carpetas han sido devueltas dentro de los plazos establecidos por las dependencias.</p>
    </div>

    <?php } ?>
