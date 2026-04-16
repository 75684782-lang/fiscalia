<?php 
?>

<div class="page-header">
    <h1>Buscar Carpeta Fiscal</h1>
    <p>Consulte la ubicación actual de la carpeta</p>
</div>

<div class="card">
    <form method="GET" class="form-buscar">
        <input type="hidden" name="page" value="carpeta_buscar">
        
        <div class="input-group">
            <input type="text" name="buscar" placeholder="Ingrese número de carpeta... Ej: 2024-001" 
                   value="<?php echo htmlspecialchars($_GET['buscar'] ?? ''); ?>" required autofocus>
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
        
        <div class="search-options">
            <a href="<?php echo ruta('carpeta_listar'); ?>" class="btn btn-secondary">Ver todas las carpetas</a>
            <a href="<?php echo ruta('carpeta_registrar'); ?>" class="btn btn-secondary">Registrar nueva</a>
        </div>
    </form>
</div>

<?php
$busqueda_realizada = isset($_GET['buscar']);
$resultado = null;

if ($busqueda_realizada) {
    $numero = $conn->real_escape_string(trim($_GET['buscar']));
    
    if (empty($numero)) {
        echo "<div class='card alert alert-warning'>⚠️ Por favor ingrese un número de carpeta</div>";
    } else {
        $sql = "SELECT * FROM carpeta_fiscal WHERE numero_carpeta LIKE '%$numero%' OR imputado LIKE '%$numero%'";
        $res = $conn->query($sql);
        
        if ($res && $res->num_rows > 0) {
            echo "<div class='card resultado'>";
            echo "<h3>✅ Carpeta(s) Encontrada(s) (" . $res->num_rows . ")</h3>";
            echo "<div class='table-responsive'>";
            echo "<table class='result-table'>";
            echo "<thead><tr>";
            echo "<th>Número</th>";
            echo "<th>Imputado</th>";
            echo "<th>Delito</th>";
            echo "<th>Agraviado</th>";
            echo "<th>Estado</th>";
            echo "<th style='color: #2196F3;'>📍 Ubicación</th>";
            echo "</tr></thead><tbody>";
            
            while ($row = $res->fetch_assoc()) {
                echo "<tr>";
                echo "<td><strong>" . htmlspecialchars($row['numero_carpeta']) . "</strong></td>";
                echo "<td>" . htmlspecialchars($row['imputado']) . "</td>";
                echo "<td>" . htmlspecialchars($row['delito']) . "</td>";
                echo "<td>" . htmlspecialchars($row['agraviado']) . "</td>";
                echo "<td><span class='badge badge-" . strtolower($row['estado']) . "'>" . htmlspecialchars($row['estado']) . "</span></td>";
                echo "<td><strong style='color: #2196F3; font-size: 16px;'>📍 " . htmlspecialchars($row['ubicacion']) . "</strong></td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
            echo "</div>";
            echo "</div>";
            
        } else {
            echo "<div class='card alert alert-danger'>";
            echo "<h2 style='margin: 0 0 10px 0;'>❌ No ubicado</h2>";
            echo "<p>No se encontró carpeta con número: <strong>\"" . htmlspecialchars($numero) . "\"</strong></p>";
            echo "<p style='color: #666; margin-bottom: 15px;'>Verifique el número e intente nuevamente, o contáctenos si necesita asistencia.</p>";
            echo "<a href='" . ruta('carpeta_registrar') . "' class='btn btn-primary' style='display: inline-block;'>Registrar Nueva Carpeta</a>";
            echo "</div>";
        }
    }
}
?>

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

.form-buscar {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.input-group {
    display: flex;
    gap: 10px;
}

.input-group input {
    flex: 1;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.input-group input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.search-options {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.resultado h3 {
    color: #28a745;
    margin: 0 0 20px 0;
}

.table-responsive {
    overflow-x: auto;
}

.result-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.result-table thead {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.result-table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #333;
}

.result-table td {
    padding: 12px;
    border-bottom: 1px solid #dee2e6;
}

.result-table tbody tr:hover {
    background: #f8f9fa;
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-activo {
    background: #d4edda;
    color: #155724;
}

.badge-archivado {
    background: #fff3cd;
    color: #856404;
}

.badge-resuelto {
    background: #d1ecf1;
    color: #0c5460;
}

.alert {
    padding: 20px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-danger {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.alert-danger h2 {
    color: #721c24;
}

.alert-danger p {
    color: #721c24;
}
</style>
