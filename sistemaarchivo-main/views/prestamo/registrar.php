<?php

// Mostrar mensajes
if (isset($_SESSION['mensaje'])) {
    echo '<div class="alert alert-' . ($_SESSION['tipo_mensaje'] ?? 'info') . '">';
    echo $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
    unset($_SESSION['tipo_mensaje']);
    echo '</div>';
}

// obtener carpetas disponibles
$carpetas = $conn->query("SELECT * FROM carpeta_fiscal WHERE id NOT IN (SELECT carpeta_id FROM detalle_prestamo WHERE prestamo_id IN (SELECT id FROM prestamo WHERE estado='PENDIENTE'))");

// obtener dependencias
$deps = $conn->query("SELECT * FROM dependencia ORDER BY nombre");
?>

<div class="page-header">
    <h1>📦 Registrar Préstamo</h1>
    <p>Solicite el préstamo de una o varias carpetas fiscales</p>
</div>

<div class="card">
    <form action="<?php echo BASE_URL; ?>controllers/PrestamoController.php" method="POST" class="form-registro">

        <div class="form-group">
            <label for="dependencia">Dependencia Solicitante *</label>
            <select id="dependencia" name="dependencia" required>
                <option value="">-- Seleccionar Dependencia --</option>
                <?php while($d = $deps->fetch_assoc()) { ?>
                    <option value="<?php echo $d['id']; ?>">
                        🏢 <?php echo htmlspecialchars($d['nombre']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Carpetas a Prestar *</label>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; max-height: 300px; overflow-y: auto;">
                <?php 
                $count = 0;
                $carpetas->data_seek(0);
                if ($carpetas->num_rows > 0) {
                    while($c = $carpetas->fetch_assoc()) { 
                        $count++;
                ?>
                    <div style="margin-bottom: 10px; padding: 10px; background: white; border-radius: 4px;">
                        <input type="checkbox" id="carpeta_<?php echo $c['id']; ?>" name="carpetas[]" value="<?php echo $c['id']; ?>">
                        <label for="carpeta_<?php echo $c['id']; ?>" style="display: inline; margin: 0;">
                            <strong><?php echo htmlspecialchars($c['numero_carpeta']); ?></strong> - 
                            <?php echo htmlspecialchars($c['imputado']); ?>
                            <br>
                            <small style="color: #666;">📍 <?php echo htmlspecialchars($c['ubicacion']); ?></small>
                        </label>
                    </div>
                <?php 
                    }
                } else {
                    echo '<p style="color: #999; text-align: center;">No hay carpetas disponibles para prestar</p>';
                }
                ?>
            </div>
            <small style="color: #666;">Total disponibles: <strong><?php echo $count; ?></strong></small>
        </div>

        <div class="form-group">
            <label for="dias">Días de Préstamo *</label>
            <input type="number" id="dias" name="dias" value="7" min="1" max="365" required title="Entre 1 y 365 días">
            <small style="color: #666;">Rango permitido: 1 a 365 días</small>
        </div>

        <div class="form-actions">
            <button type="submit" name="guardar" class="btn btn-primary">✅ Generar Préstamo</button>
            <a href="<?php echo ruta('prestamo_listar'); ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<style>
.form-grupo label {
    margin-bottom: 10px;
}
</style>
