<?php

$sql = "SELECT p.*, d.nombre, 
        DATEDIFF(p.fecha_vencimiento, CURDATE()) as dias_restantes,
        COUNT(dp.id) as total_carpetas
        FROM prestamo p
        LEFT JOIN detalle_prestamo dp ON p.id = dp.prestamo_id
        JOIN dependencia d ON p.dependencia_id = d.id
        GROUP BY p.id
        ORDER BY p.fecha_vencimiento ASC";

$res = $conn->query($sql);

// Agrupar alertas
$vencidos = [];
$proximos = [];

if ($res && $res->num_rows > 0) {
    $res->data_seek(0);
    while ($row = $res->fetch_assoc()) {
        if ($row['estado'] == 'PENDIENTE') {
            if ($row['dias_restantes'] < 0) {
                $vencidos[] = $row;
            } elseif ($row['dias_restantes'] <= 3) {
                $proximos[] = $row;
            }
        }
    }
}
?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>📦 Préstamos</h1>
            <p>Gestión y seguimiento de préstamos de carpetas</p>
        </div>
        <a href="<?php echo ruta('prestamo_registrar'); ?>" class="btn btn-primary" style="height: fit-content;">+ Nuevo Préstamo</a>
    </div>
</div>

<!-- ALERTAS DE VENCIMIENTO -->
<?php if (!empty($vencidos)) { ?>
<div class="card alert alert-danger">
    <h3 style="margin-top: 0;">❌ <?php echo count($vencidos); ?> Préstamo(s) VENCIDO(S)</h3>
    <p style="margin-bottom: 10px;">Requiere atención inmediata para registro de devoluciones.</p>
    <ul style="margin: 0;">
        <?php foreach ($vencidos as $v) { ?>
        <li style="margin-bottom: 8px;">
            <strong><?php echo htmlspecialchars($v['numero_guia']); ?></strong> 
            (<?php echo htmlspecialchars($v['nombre']); ?>) 
            - <span style="font-weight: bold;">Vencida hace <?php echo abs($v['dias_restantes']); ?> días</span>
            <a href="<?php echo ruta('prestamo_devolucion', array('id' => $v['id'])); ?>" class="btn btn-small" style="margin-left: 10px;">Registrar Devolución</a>
        </li>
        <?php } ?>
    </ul>
</div>
<?php } ?>

<?php if (!empty($proximos)) { ?>
<div class="card alert alert-warning">
    <h3 style="margin-top: 0;">⏰ <?php echo count($proximos); ?> Préstamo(s) próximo(s) a vencer</h3>
    <ul style="margin: 0;">
        <?php foreach ($proximos as $p) { ?>
        <li style="margin-bottom: 8px;">
            <strong><?php echo htmlspecialchars($p['numero_guia']); ?></strong> 
            (<?php echo htmlspecialchars($p['nombre']); ?>) 
            - Vence en <strong><?php echo $p['dias_restantes']; ?> días</strong>
        </li>
        <?php } ?>
    </ul>
</div>
<?php } ?>

<!-- TABLA DE PRÉSTAMOS -->
<div class="card">
    <div style="overflow-x: auto;">
        <table class="result-table">
            <thead>
                <tr>
                    <th>Guía</th>
                    <th>Dependencia</th>
                    <th>Fecha Préstamo</th>
                    <th>Vencimiento</th>
                    <th>Carpetas</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($res && $res->num_rows > 0) {
                    $res->data_seek(0);
                    while ($r = $res->fetch_assoc()) {
                        $dias_restantes = $r['dias_restantes'];
                        $estado_clase = '';
                        $estado_texto = '';
                        
                        if ($r['estado'] == 'DEVUELTO') {
                            $estado_clase = 'badge-success';
                            $estado_texto = '✅ DEVUELTO';
                        } elseif ($r['estado'] == 'PENDIENTE' && $dias_restantes < 0) {
                            $estado_clase = 'badge-danger';
                            $estado_texto = '❌ VENCIDO';
                        } elseif ($r['estado'] == 'PENDIENTE' && $dias_restantes <= 3) {
                            $estado_clase = 'badge-warning';
                            $estado_texto = '⏰ POR VENCER';
                        } else {
                            $estado_clase = 'badge-info';
                            $estado_texto = '⏳ PENDIENTE';
                        }
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($r['numero_guia']); ?></strong></td>
                    <td><?php echo htmlspecialchars($r['nombre']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($r['fecha_prestamo'])); ?></td>
                    <td style="<?php echo $dias_restantes < 0 ? 'color: #dc3545; font-weight: bold;' : ''; ?>">
                        <?php echo date('d/m/Y', strtotime($r['fecha_vencimiento'])); ?>
                        <?php if ($r['estado'] == 'PENDIENTE') { 
                            echo $dias_restantes >= 0 ? " (+$dias_restantes d)" : " (" . abs($dias_restantes) . "d)"; 
                        } ?>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge badge-info"><?php echo $r['total_carpetas']; ?> carpeta(s)</span>
                    </td>
                    <td>
                        <span class="badge <?php echo $estado_clase; ?>">
                            <?php echo $estado_texto; ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo ruta('prestamo_devolucion', array('id' => $r['id'])); ?>" class="btn btn-small btn-primary">Ver</a>
                        <?php if ($r['estado'] == 'PENDIENTE') { ?>
                            <a href="<?php echo ruta('prestamo_devolucion', array('id' => $r['id'])); ?>#devolver" class="btn btn-small btn-danger">Devolver</a>
                        <?php } ?>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #999;">
                        📭 No hay préstamos registrados
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.badge-info {
    background: #17a2b8;
    color: white;
    padding: 6px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
}
</style>
