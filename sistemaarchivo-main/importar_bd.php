<?php
$host = "localhost";
$user = "root";
$pass = "";

// Crear conexión sin seleccionar BD
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Crear BD
$sql_create_db = "CREATE DATABASE IF NOT EXISTS sistema_archivo_db";
if (!$conn->query($sql_create_db)) {
    die("Error creando BD: " . $conn->error);
}

// Seleccionar BD
$conn->select_db("sistema_archivo_db");

// Remover todas las tablas existentes
$conn->query("SET FOREIGN_KEY_CHECKS=0");
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $conn->query("DROP TABLE IF EXISTS `" . $row[0] . "`");
}
$conn->query("SET FOREIGN_KEY_CHECKS=1");

// Leer y ejecutar el SQL
$sql_content = file_get_contents(__DIR__ . "/sistema_archivo_db.sql");

// Procesar el SQL de forma más robusta
$lines = explode("\n", $sql_content);
$query = "";
$executed = 0;
$errors = 0;

foreach ($lines as $line) {
    $line = trim($line);
    
    // Saltar líneas vacías y comentarios
    if (empty($line) || substr($line, 0, 2) === '--' || substr($line, 0, 1) === '#') {
        continue;
    }
    
    // Saltar comentarios /* */
    if (substr($line, 0, 2) === '/*' && strpos($line, '*/') === false) {
        continue;
    }
    
    $query .= $line . " ";
    
    // Si la línea termina en ; es el fin de un query
    if (substr(rtrim($line), -1) === ';') {
        $query = trim($query);
        $query = substr($query, 0, -1); // Remover el último ;
        
        if (!empty($query)) {
            if (!$conn->query($query)) {
                echo "Error: " . $conn->error . "<br>";
                $errors++;
            } else {
                $executed++;
            }
        }
        $query = "";
    }
}

echo "<h2 style='color:green;'>Importación completada</h2>";
echo "Queries ejecutadas: " . $executed . "<br>";
echo "Errores: " . $errors . "<br>";
echo "<a href='index.php'>Volver</a>";

$conn->close();
?>
