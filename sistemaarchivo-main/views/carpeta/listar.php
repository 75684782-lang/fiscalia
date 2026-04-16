<?php

$page = intval($router->obtener_parametro('p', 1));
$limit = 20;
$offset = max(0, ($page - 1) * $limit);

$buscar = $router->obtener_parametro('buscar', "");

if ($buscar) {
    $buscar = $conn->real_escape_string($buscar);
    $sql_count = "SELECT COUNT(*) as total FROM carpeta_fiscal 
                  WHERE numero_carpeta LIKE '%$buscar%' OR imputado LIKE '%$buscar%' OR delito LIKE '%$buscar%'";
    $sql = "SELECT * FROM carpeta_fiscal 
            WHERE numero_carpeta LIKE '%$buscar%' OR imputado LIKE '%$buscar%' OR delito LIKE '%$buscar%'
            ORDER BY numero_carpeta DESC LIMIT $limit OFFSET $offset";
} else {
    $sql_count = "SELECT COUNT(*) as total FROM carpeta_fiscal";
    $sql = "SELECT * FROM carpeta_fiscal ORDER BY numero_carpeta DESC LIMIT $limit OFFSET $offset";
}

$count_result = $conn->query($sql_count);
$total = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total / $limit);

$result = $conn->query($sql);
?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Carpetas Fiscales</h1>
            <p>Total registradas: <strong><?php echo $total; ?></strong></p>
        </div>
        <a href="<?php echo ruta('carpeta_registrar'); ?>" class="btn btn-primary" style="height: fit-content;">Registrar Nueva</a>
    </div>
</div>

<!-- BUSCADOR -->
<div class="card">
    <form method="GET" class="form-buscar">
        <input type="hidden" name="page" value="carpeta_listar">
        <div class="input-group" style="margin: 0;">
            <input type="text" name="buscar" placeholder="Buscar por número, imputado o delito..." 
                   value="<?php echo htmlspecialchars($buscar); ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
    </form>
</div>

<!-- RESULTADOS -->
<div class="card">
    <?php if ($result && $result->num_rows > 0) { ?>
        <div style="overflow-x: auto;">
            <table class="result-table">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Imputado</th>
                        <th>Delito</th>
                        <th>Agraviado</th>
                        <th>Estado</th>
                        <th style="color: #2196F3;">📍 Ubicación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['numero_carpeta']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['imputado']); ?></td>
                        <td><?php echo htmlspecialchars($row['delito']); ?></td>
                        <td><?php echo htmlspecialchars($row['agraviado']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo strtolower($row['estado']); ?>">
                                <?php echo htmlspecialchars($row['estado']); ?>
                            </span>
                        </td>
                        <td><strong style="color: #2196F3;">📍 <?php echo htmlspecialchars($row['ubicacion']); ?></strong></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- PAGINACIÓN -->
        <?php if ($total_pages > 1) { ?>
        <div style="display: flex; justify-content: center; gap: 5px; margin-top: 20px;">
            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                     <a href="<?php echo ruta('carpeta_listar', array('p' => $i, 'buscar' => $buscar)); ?>" 
                         class="btn btn-<?php echo ($page == $i) ? 'primary' : 'secondary'; ?>" 
                   style="padding: 8px 12px; font-size: 12px;">
                    <?php echo $i; ?>
                </a>
            <?php } ?>
        </div>
        <?php } ?>

    <?php } else { ?>
        <div style="text-align: center; padding: 40px;">
            <p style="font-size: 18px; color: #999;">No se encontraron carpetas</p>
            <a href="<?php echo ruta('carpeta_registrar'); ?>" class="btn btn-primary">Registrar Primera Carpeta</a>
        </div>
    <?php } ?>
</div>

<style>
.input-group {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.input-group input {
    flex: 1;
    margin: 0;
}

.input-group button {
    margin: 0;
}
</style>
