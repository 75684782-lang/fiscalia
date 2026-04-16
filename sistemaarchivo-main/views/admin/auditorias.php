<div class="page-header">
    <h1>Registro de Auditorías</h1>
    <p>Historial completo de operaciones del sistema</p>
</div>

<!-- Filtros -->
<div class="card">
    <h3>Filtros de Búsqueda</h3>
    <form method="GET" action="<?php echo ruta('admin_auditorias'); ?>" class="form-inline">
        <input type="hidden" name="page" value="admin_auditorias">

        <div class="form-group">
            <label>Usuario:</label>
            <select name="usuario" class="form-control">
                <option value="">Todos</option>
                <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                    <option value="<?php echo $usuario['id']; ?>" <?php echo ($filtro_usuario == $usuario['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($usuario['username']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Tabla:</label>
            <select name="tabla" class="form-control">
                <option value="">Todas</option>
                <?php while ($tabla = $tablas->fetch_assoc()): ?>
                    <option value="<?php echo $tabla['tabla']; ?>" <?php echo ($filtro_tabla == $tabla['tabla']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($tabla['tabla']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Operación:</label>
            <select name="operacion" class="form-control">
                <option value="">Todas</option>
                <?php while ($operacion = $operaciones->fetch_assoc()): ?>
                    <option value="<?php echo $operacion['operacion']; ?>" <?php echo ($filtro_operacion == $operacion['operacion']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($operacion['operacion']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Desde:</label>
            <input type="date" name="fecha_desde" value="<?php echo $filtro_fecha_desde; ?>" class="form-control">
        </div>

        <div class="form-group">
            <label>Hasta:</label>
            <input type="date" name="fecha_hasta" value="<?php echo $filtro_fecha_hasta; ?>" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Filtrar</button>
        <a href="<?php echo ruta('admin_auditorias'); ?>" class="btn btn-secondary">Limpiar</a>
    </form>
</div>

<!-- Resultados -->
<div class="card">
    <h3>Registros de Auditoría (<?php echo $total_registros; ?> total)</h3>

    <?php if ($auditorias && $auditorias->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="result-table">
                <thead>
                    <tr>
                        <th>Fecha/Hora</th>
                        <th>Usuario</th>
                        <th>Tabla</th>
                        <th>Operación</th>
                        <th>Registro ID</th>
                        <th>IP</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($auditoria = $auditorias->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($auditoria['fecha_operacion'])); ?></td>
                            <td><?php echo htmlspecialchars($auditoria['username'] ?: 'Sistema'); ?></td>
                            <td><?php echo htmlspecialchars($auditoria['tabla']); ?></td>
                            <td>
                                <span class="badge badge-<?php
                                    echo match($auditoria['operacion']) {
                                        'INSERT' => 'success',
                                        'UPDATE' => 'warning',
                                        'DELETE' => 'danger',
                                        'LOGIN' => 'info',
                                        'LOGOUT' => 'secondary',
                                        default => 'primary'
                                    };
                                ?>">
                                    <?php echo htmlspecialchars($auditoria['operacion']); ?>
                                </span>
                            </td>
                            <td><?php echo $auditoria['registro_id'] ?: '-'; ?></td>
                            <td><?php echo htmlspecialchars($auditoria['ip_address']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="mostrarDetalles(<?php echo $auditoria['id']; ?>)">
                                    Ver Detalles
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <?php if ($total_paginas > 1): ?>
            <div class="pagination">
                <?php if ($pagina > 1): ?>
                    <a href="<?php echo ruta('admin_auditorias', array_merge($_GET, ['pagina' => $pagina - 1])); ?>" class="btn btn-secondary">Anterior</a>
                <?php endif; ?>

                <?php for ($i = max(1, $pagina - 2); $i <= min($total_paginas, $pagina + 2); $i++): ?>
                    <a href="<?php echo ruta('admin_auditorias', array_merge($_GET, ['pagina' => $i])); ?>"
                       class="btn <?php echo ($i == $pagina) ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($pagina < $total_paginas): ?>
                    <a href="<?php echo ruta('admin_auditorias', array_merge($_GET, ['pagina' => $pagina + 1])); ?>" class="btn btn-secondary">Siguiente</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <p class="text-muted">No se encontraron registros de auditoría con los filtros aplicados.</p>
    <?php endif; ?>
</div>

<!-- Modal para detalles -->
<div id="modalDetalles" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal()">&times;</span>
        <h3>Detalles de la Auditoría</h3>
        <div id="contenidoDetalles"></div>
    </div>
</div>

<script>
function mostrarDetalles(id) {
    // Aquí podrías hacer una petición AJAX para obtener los detalles completos
    // Por simplicidad, mostramos un mensaje
    document.getElementById('contenidoDetalles').innerHTML = '<p>Cargando detalles...</p>';
    document.getElementById('modalDetalles').style.display = 'block';

    // Simular carga
    setTimeout(() => {
        document.getElementById('contenidoDetalles').innerHTML = `
            <p><strong>ID de Auditoría:</strong> ${id}</p>
            <p>Para ver los valores anteriores/nuevos completos, se requiere implementación de AJAX.</p>
        `;
    }, 500);
}

function cerrarModal() {
    document.getElementById('modalDetalles').style.display = 'none';
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('modalDetalles');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 5px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: black;
}

.pagination {
    text-align: center;
    margin-top: 20px;
}

.pagination .btn {
    margin: 0 2px;
}

.form-inline {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: end;
}

.form-group {
    display: flex;
    flex-direction: column;
    min-width: 150px;
}

.form-group label {
    margin-bottom: 5px;
    font-weight: bold;
}
</style>