<div class="page-header">
    <h1>Asignación de Roles</h1>
    <p>Gestiona los roles de los usuarios del sistema</p>
</div>

<!-- Lista de Usuarios -->
<div class="card">
    <h3>Usuarios del Sistema</h3>

    <?php if ($usuarios && $usuarios->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="result-table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Rol Actual</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['username']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td>
                                <span class="badge badge-<?php
                                    echo match($usuario['rol']) {
                                        'administrador' => 'danger',
                                        'moderador' => 'warning',
                                        'usuario' => 'primary',
                                        'visor' => 'info',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <?php echo htmlspecialchars($usuario['rol']); ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="abrirModalAsignar(<?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['username']); ?>', '<?php echo htmlspecialchars($usuario['rol']); ?>')">
                                    Cambiar Rol
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted">No se encontraron usuarios.</p>
    <?php endif; ?>
</div>

<!-- Modal para asignar rol -->
<div id="modalAsignar" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="cerrarModalAsignar()">&times;</span>
        <h3>Asignar Rol</h3>
        <form method="POST" action="<?php echo ruta('admin_asignar_roles'); ?>">
            <input type="hidden" name="page" value="admin_asignar_roles">
            <input type="hidden" name="asignar_rol" value="1">
            <input type="hidden" name="usuario_id" id="usuario_id_modal">

            <div class="form-group">
                <label for="usuario_info">Usuario:</label>
                <p id="usuario_info" style="font-weight: bold; margin: 0;"></p>
            </div>

            <div class="form-group">
                <label for="rol_actual">Rol Actual:</label>
                <p id="rol_actual" style="margin: 0;"></p>
            </div>

            <div class="form-group">
                <label for="nuevo_rol">Nuevo Rol:</label>
                <select name="rol" id="nuevo_rol" class="form-control" required>
                    <option value="administrador">Administrador - Acceso completo</option>
                    <option value="moderador">Moderador - Gestión de carpetas y préstamos</option>
                    <option value="usuario">Usuario - Acceso básico</option>
                    <option value="visor">Visor - Solo lectura</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Asignar Rol</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarModalAsignar()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Información de Roles -->
<div class="card">
    <h3>Información de Roles</h3>
    <div class="roles-info">
        <div class="rol-item">
            <h4><span class="badge badge-danger">Administrador</span></h4>
            <p>Acceso completo a todas las funciones del sistema, incluyendo administración de usuarios y auditorías.</p>
        </div>

        <div class="rol-item">
            <h4><span class="badge badge-warning">Moderador</span></h4>
            <p>Puede gestionar carpetas (crear, editar, importar), préstamos y ver reportes. No puede administrar usuarios.</p>
        </div>

        <div class="rol-item">
            <h4><span class="badge badge-primary">Usuario</span></h4>
            <p>Acceso básico para ver carpetas, crear préstamos y registrar devoluciones.</p>
        </div>

        <div class="rol-item">
            <h4><span class="badge badge-info">Visor</span></h4>
            <p>Solo puede ver carpetas y préstamos. Sin permisos de modificación.</p>
        </div>
    </div>
</div>

<script>
function abrirModalAsignar(id, username, rolActual) {
    document.getElementById('usuario_id_modal').value = id;
    document.getElementById('usuario_info').textContent = username;
    document.getElementById('rol_actual').textContent = rolActual;
    document.getElementById('nuevo_rol').value = rolActual;
    document.getElementById('modalAsignar').style.display = 'block';
}

function cerrarModalAsignar() {
    document.getElementById('modalAsignar').style.display = 'none';
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('modalAsignar');
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
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 90%;
    max-width: 500px;
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

.form-actions {
    text-align: right;
    margin-top: 20px;
}

.form-actions .btn {
    margin-left: 10px;
}

.roles-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.rol-item {
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 15px;
    background: #f9f9f9;
}

.rol-item h4 {
    margin-top: 0;
    margin-bottom: 10px;
}

.rol-item p {
    margin: 0;
    color: #666;
}
</style>