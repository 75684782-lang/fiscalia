<?php

// Mostrar mensajes
if (isset($_SESSION['mensaje'])) {
    echo '<div class="alert alert-' . ($_SESSION['tipo_mensaje'] ?? 'info') . '">';
    echo $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
    unset($_SESSION['tipo_mensaje']);
    echo '</div>';
}
?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Registrar Carpeta Fiscal</h1>
            <p>Ingrese los datos de la carpeta o importe desde Excel</p>
        </div>
        <a href="<?php echo ruta('carpeta_importar'); ?>" class="btn btn-secondary" style="height: fit-content;">Importar Masivo</a>
    </div>
</div>

<!-- REGISTRO MANUAL -->
<div class="card">
    <h3>📝 Registro Manual</h3>

    <form action="<?php echo BASE_URL; ?>controllers/CarpetaController.php" method="POST" class="form-registro">

        <div class="form-group">
            <label for="numero">Número de Carpeta *</label>
            <input type="text" id="numero" name="numero" placeholder="Ej: 2024-001" required pattern="[0-9\-]+" title="Solo números y guiones">
        </div>

        <div class="form-group">
            <label for="imputado">Imputado *</label>
            <input type="text" id="imputado" name="imputado" placeholder="Nombre completo" required>
        </div>

        <div class="form-group">
            <label for="delito">Delito *</label>
            <input type="text" id="delito" name="delito" placeholder="Tipo de delito" required>
        </div>

        <div class="form-group">
            <label for="agraviado">Agraviado *</label>
            <input type="text" id="agraviado" name="agraviado" placeholder="Nombre del agraviado" required>
        </div>

        <div class="form-group">
            <label for="estado">Estado *</label>
            <select id="estado" name="estado" required>
                <option value="">-- Seleccionar --</option>
                <option value="Activo">Activo</option>
                <option value="Archivado">Archivado</option>
                <option value="Resuelto">Resuelto</option>
            </select>
        </div>

        <div class="form-group">
            <label for="ubicacion">Ubicación *</label>
            <input type="text" id="ubicacion" name="ubicacion" placeholder="Ej: Estante A1" required>
        </div>

        <div class="form-actions">
            <button type="submit" name="guardar" class="btn btn-primary">✅ Guardar Carpeta</button>
            <a href="<?php echo ruta('carpeta_listar'); ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>

</div>

<script>
document.getElementById('file-input').addEventListener('change', function() {
    const fileName = this.files[0]?.name || 'Ningún archivo seleccionado';
    document.getElementById('file-name').textContent = fileName;
});
</script>

<style>
.page-header {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e0e0e0;
}

.page-header h1 {
    margin: 0 0 10px 0;
    color: #333;
}

.page-header p {
    margin: 0;
    color: #666;
}

.form-registro {
    display: grid;
    gap: 15px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 5px;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.form-group input,
.form-group select {
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.file-input-wrapper {
    position: relative;
    margin-bottom: 10px;
}

#file-input {
    display: none;
}

.file-label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 30px;
    border: 2px dashed #007bff;
    border-radius: 4px;
    background: #f8f9ff;
    cursor: pointer;
    transition: all 0.3s;
}

.file-label:hover {
    background: #eef2ff;
    border-color: #0056b3;
}

.file-label span:first-child {
    font-weight: 600;
    color: #007bff;
}

.file-label span:last-child {
    color: #666;
    font-size: 13px;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
}
</style>
