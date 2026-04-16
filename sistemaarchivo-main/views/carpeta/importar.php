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
    <h1>📥 Importar Carpetas desde Excel</h1>
    <p>Cargue un archivo Excel con los datos de carpetas para importar masivamente</p>
</div>

<div class="card">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <!-- FORMULARIO DE CARGA -->
        <div>
            <h3>📤 Cargar Archivo</h3>
            <form action="<?php echo BASE_URL; ?>controllers/CarpetaController.php" method="POST" enctype="multipart/form-data" class="form-import">
                <div class="file-input-wrapper">
                    <input type="file" name="archivo" accept=".xlsx,.xls,.csv" id="file-input" required>
                    <label for="file-input" class="file-label">
                        <span>📄 Seleccionar archivo...</span>
                        <span id="file-name">Ningún archivo seleccionado</span>
                    </label>
                </div>
                <button type="submit" name="importar" class="btn btn-primary" style="width: 100%; margin-top: 15px;">Importar Carpetas</button>
            </form>
        </div>

        <!-- INFORMACIÓN -->
        <div>
            <h3>ℹ️ Formato Requerido</h3>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff;">
                <p style="margin: 0 0 15px 0;"><strong>Columnas (en orden):</strong></p>
                <table style="width: 100%; font-size: 13px; margin: 0;">
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 8px 0;"><strong>1</strong></td>
                        <td>numero_carpeta</td>
                        <td style="color: #666;">(Ej: 2024-001)</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 8px 0;"><strong>2</strong></td>
                        <td>imputado</td>
                        <td style="color: #666;">(Nombre completo)</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 8px 0;"><strong>3</strong></td>
                        <td>delito</td>
                        <td style="color: #666;">(Tipo de delito)</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 8px 0;"><strong>4</strong></td>
                        <td>agraviado</td>
                        <td style="color: #666;">(Nombre)</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 8px 0;"><strong>5</strong></td>
                        <td>estado</td>
                        <td style="color: #666;">(Activo, Archivado, Resuelto)</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>6</strong></td>
                        <td>ubicacion</td>
                        <td style="color: #666;">(Ej: Estante A1)</td>
                    </tr>
                </table>
                
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
                    <p style="margin: 5px 0;"><strong>✓ Formatos soportados:</strong></p>
                    <p style="margin: 0; color: #666; font-size: 12px;">Excel (.xlsx, .xls), CSV (.csv)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- EJEMPLO -->
    <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e0e0e0;">
        <h3>📋 Ejemplo de Archivo</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; font-size: 12px; border: 1px solid #ddd;">
                <thead style="background: #f0f0f0;">
                    <tr>
                        <th style="padding: 10px; text-align: left; border-right: 1px solid #ddd;">numero_carpeta</th>
                        <th style="padding: 10px; text-align: left; border-right: 1px solid #ddd;">imputado</th>
                        <th style="padding: 10px; text-align: left; border-right: 1px solid #ddd;">delito</th>
                        <th style="padding: 10px; text-align: left; border-right: 1px solid #ddd;">agraviado</th>
                        <th style="padding: 10px; text-align: left; border-right: 1px solid #ddd;">estado</th>
                        <th style="padding: 10px; text-align: left;">ubicacion</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px; border-right: 1px solid #ddd;">2024-001</td>
                        <td style="padding: 10px; border-right: 1px solid #ddd;">Juan Pérez García</td>
                        <td style="padding: 10px; border-right: 1px solid #ddd;">Robo agravado</td>
                        <td style="padding: 10px; border-right: 1px solid #ddd;">María López</td>
                        <td style="padding: 10px; border-right: 1px solid #ddd;">Activo</td>
                        <td style="padding: 10px;">Estante A1</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px; border-right: 1px solid #ddd;">2024-002</td>
                        <td style="padding: 10px; border-right: 1px solid #ddd;">Carlos Díaz Ruiz</td>
                        <td style="padding: 10px; border-right: 1px solid #ddd;">Tráfico ilícito</td>
                        <td style="padding: 10px; border-right: 1px solid #ddd;">Estado</td>
                        <td style="padding: 10px; border-right: 1px solid #ddd;">Activo</td>
                        <td style="padding: 10px;">Estante B3</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.file-input-wrapper {
    position: relative;
    margin-bottom: 10px;
}

#file-input {
    display: none;
}

.file-label {
    display: flex;
    flex-direction: column;
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

@media (max-width: 768px) {
    .card > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
document.getElementById('file-input').addEventListener('change', function() {
    const fileName = this.files[0]?.name || 'Ningún archivo seleccionado';
    document.getElementById('file-name').textContent = fileName;
});
</script>
