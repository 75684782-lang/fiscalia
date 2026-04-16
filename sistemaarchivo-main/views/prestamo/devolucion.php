<?php
include(APP_PATH . VIEWS_LAYOUTS . "header.php");

$prestamo_id = intval($router->obtener_parametro('id', 0));

if ($prestamo_id == 0) {
    Router::redirigir('prestamo_listar');
}

// Obtener datos del préstamo
$sql = "SELECT p.*, d.nombre as dependencia, COUNT(dp.id) as total_carpetas
        FROM prestamo p
        JOIN dependencia d ON p.dependencia_id = d.id
        LEFT JOIN detalle_prestamo dp ON p.id = dp.prestamo_id
        WHERE p.id = $prestamo_id
        GROUP BY p.id";

$res = $conn->query($sql);
if (!$res || $res->num_rows == 0) {
    $_SESSION['mensaje'] = 'Error: Préstamo no encontrado';
    $_SESSION['tipo_mensaje'] = 'error';
    Router::redirigir('prestamo_listar');
}

$prestamo = $res->fetch_assoc();

// Obtener carpetas del préstamo
$sql_carpetas = "SELECT cf.* FROM carpeta_fiscal cf
                 INNER JOIN detalle_prestamo dp ON cf.id = dp.carpeta_id
                 WHERE dp.prestamo_id = $prestamo_id";
$carpetas = $conn->query($sql_carpetas);

// Calcular días de vencimiento
$fecha_vencimiento = new DateTime($prestamo['fecha_vencimiento']);
$fecha_hoy = new DateTime();
$dias_vencimiento = $fecha_hoy->diff($fecha_vencimiento)->days;
$esta_vencido = $fecha_hoy > $fecha_vencimiento;

?>

<div class="container">

    <h1>📄 Nota de Devolución de Carpetas Fiscales</h1>

    <div class="card">
        <h2>Datos del Préstamo</h2>

        <p><strong>Guía Préstamo:</strong> <?php echo htmlspecialchars($prestamo['numero_guia']); ?></p>
        <p><strong>Dependencia:</strong> <?php echo htmlspecialchars($prestamo['dependencia']); ?></p>
        <p><strong>Fecha Préstamo:</strong> <?php echo date('d/m/Y', strtotime($prestamo['fecha_prestamo'])); ?></p>
        <p><strong>Fecha Vencimiento:</strong> <span style="<?php echo $esta_vencido ? 'color: red; font-weight: bold;' : ''; ?>">
            <?php echo date('d/m/Y', strtotime($prestamo['fecha_vencimiento'])); ?>
        </span></p>
        <p><strong>Total Carpetas:</strong> <?php echo $prestamo['total_carpetas']; ?></p>

        <?php if ($esta_vencido) { ?>
        <hr>
        <p style="color:red; font-weight: bold;">
            ⚠️ CARPETAS VENCIDAS - Se solicita devolución inmediata
        </p>
        <?php } ?>

    </div>

    <div class="card">
        <h2>Carpetas en Préstamo</h2>
        <table>
            <tr>
                <th>N°</th>
                <th>Número</th>
                <th>Imputado</th>
                <th>Delito</th>
                <th>Agraviado</th>
                <th>Ubicación</th>
            </tr>
            <?php
            $i = 1;
            if ($carpetas && $carpetas->num_rows > 0) {
                while ($carpeta = $carpetas->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $i . "</td>";
                    echo "<td>" . htmlspecialchars($carpeta['numero_carpeta']) . "</td>";
                    echo "<td>" . htmlspecialchars($carpeta['imputado']) . "</td>";
                    echo "<td>" . htmlspecialchars($carpeta['delito']) . "</td>";
                    echo "<td>" . htmlspecialchars($carpeta['agraviado']) . "</td>";
                    echo "<td>" . htmlspecialchars($carpeta['ubicacion']) . "</td>";
                    echo "</tr>";
                    $i++;
                }
            }
            ?>
        </table>
    </div>

    <?php if ($esta_vencido) { ?>
    <div class="card">
        <form action="<?php echo ruta(''); ?>controllers/PrestamoController.php" method="POST">
            <input type="hidden" name="prestamo_id" value="<?php echo $prestamo_id; ?>">
            <p><strong>Registrar devolución:</strong></p>
            <button type="submit" name="devolver" class="btn btn-success" onclick="return confirm('¿Confirmar devolución?')">
                ✓ Registrar Devolución
            </button>
            <a href="<?php echo ruta('prestamo_listar'); ?>" class="btn btn-secondary">Volver</a>
        </form>
    </div>
    <?php } else { ?>
    <div class="card">
        <a href="<?php echo ruta('prestamo_listar'); ?>" class="btn btn-secondary">Volver</a>
    </div>
    <?php } ?>

</div>

<?php include(APP_PATH . VIEWS_LAYOUTS . "footer.php"); ?>
