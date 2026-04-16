<div class="page-header">
    <h1>Gestión de Usuarios</h1>
    <p>Administra usuarios, roles y permisos del sistema</p>
</div>

<!-- Mensajes -->
<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?php echo $_SESSION['tipo_mensaje'] === 'exito' ? 'success' : 'danger'; ?>">
        <?php echo htmlspecialchars($_SESSION['mensaje']); ?>
        <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
    </div>
<?php endif; ?>

<!-- Botón Crear Usuario -->
<div class="card">
    <div style="margin-bottom: 20px;">
        <button class="btn btn-primary" onclick="abrirModalCrear()">Crear Nuevo Usuario</button>
    </div>

    <!-- Lista de Usuarios -->
    <h3>Usuarios del Sistema</h3>

    <?php if ($usuarios && $usuarios->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="result-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Fecha Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $usuario['id']; ?></td>
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
                                <span class="badge badge-<?php echo $usuario['estado'] === 'activo' ? 'success' : 'danger'; ?>">
                                    <?php echo htmlspecialchars($usuario['estado']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($usuario['fecha_creacion'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="abrirModalEditar(<?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['username']); ?>', '<?php echo htmlspecialchars($usuario['email']); ?>', '<?php echo htmlspecialchars($usuario['rol']); ?>', '<?php echo htmlspecialchars($usuario['estado']); ?>')">
                                    Editar
                                </button>
                                <?php if ($usuario['id'] != $_SESSION['usuario_id']): ?>
                                    <a href="<?php echo ruta('admin_usuarios', ['eliminar_usuario' => $usuario['id']]); ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                                        Eliminar
                                    </a>
                                <?php endif; ?>
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

<!-- Modal Crear Usuario -->
<div id="modalCrear" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="cerrarModalCrear()">&times;</span>
        <h3>Crear Nuevo Usuario</h3>
        <form method="POST" action="<?php echo ruta('admin_usuarios'); ?>">
            <input type="hidden" name="page" value="admin_usuarios">
            <input type="hidden" name="crear_usuario" value="1">

            <div class="form-group">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="rol">Rol:</label>
                <select name="rol" id="rol" class="form-control" required>
                    <option value="visor">Visor - Solo lectura</option>
                    <option value="usuario">Usuario - Acceso básico</option>
                    <option value="moderador">Moderador - Gestión de carpetas</option>
                    <option value="administrador">Administrador - Acceso completo</option>
                </select>
            </div>

            <div class="form-group">
                <label for="estado">Estado:</label>
                <select name="estado" id="estado" class="form-control" required>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Crear Usuario</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarModalCrear()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div id="modalEditar" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="cerrarModalEditar()">&times;</span>
        <h3>Editar Usuario</h3>
        <form method="POST" action="<?php echo ruta('admin_usuarios'); ?>">
            <input type="hidden" name="page" value="admin_usuarios">
            <input type="hidden" name="editar_usuario" value="1">
            <input type="hidden" name="id" id="edit_id">

            <div class="form-group">
                <label for="edit_username">Nombre de Usuario:</label>
                <input type="text" name="username" id="edit_username" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="edit_password">Nueva Contraseña (opcional):</label>
                <input type="password" name="password" id="edit_password" class="form-control">
                <small class="text-muted">Deja vacío para mantener la contraseña actual</small>
                <br>
                <input type="checkbox" name="cambiar_password" id="cambiar_password" onchange="togglePassword()">
                <label for="cambiar_password">Cambiar contraseña</label>
            </div>

            <div class="form-group">
                <label for="edit_email">Email:</label>
                <input type="email" name="email" id="edit_email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="edit_rol">Rol:</label>
                <select name="rol" id="edit_rol" class="form-control" required>
                    <option value="visor">Visor - Solo lectura</option>
                    <option value="usuario">Usuario - Acceso básico</option>
                    <option value="moderador">Moderador - Gestión de carpetas</option>
                    <option value="administrador">Administrador - Acceso completo</option>
                </select>
            </div>

            <div class="form-group">
                <label for="edit_estado">Estado:</label>
                <select name="estado" id="edit_estado" class="form-control" required>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarModalEditar()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalCrear() {
    document.getElementById('modalCrear').style.display = 'block';
}

function cerrarModalCrear() {
    document.getElementById('modalCrear').style.display = 'none';
}

function abrirModalEditar(id, username, email, rol, estado) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_rol').value = rol;
    document.getElementById('edit_estado').value = estado;
    document.getElementById('edit_password').value = '';
    document.getElementById('cambiar_password').checked = false;
    document.getElementById('edit_password').disabled = true;
    document.getElementById('modalEditar').style.display = 'block';
}

function cerrarModalEditar() {
    document.getElementById('modalEditar').style.display = 'none';
}

function togglePassword() {
    const checkbox = document.getElementById('cambiar_password');
    const passwordField = document.getElementById('edit_password');
    passwordField.disabled = !checkbox.checked;
    if (!checkbox.checked) {
        passwordField.value = '';
    }
}

// Cerrar modales al hacer clic fuera
window.onclick = function(event) {
    const modalCrear = document.getElementById('modalCrear');
    const modalEditar = document.getElementById('modalEditar');
    if (event.target == modalCrear) {
        modalCrear.style.display = 'none';
    }
    if (event.target == modalEditar) {
        modalEditar.style.display = 'none';
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
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 90%;
    max-width: 600px;
    border-radius: 5px;
    max-height: 90%;
    overflow-y: auto;
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

.text-muted {
    color: #666;
    font-size: 0.9em;
}
</style>