<?php
// Crear usuario administrador adicional
require_once("config/conexion.php");

$username = 'superadmin';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$email = 'superadmin@sistema.com';
$rol = 'administrador';
$estado = 'activo';

// Verificar si ya existe
$stmt = $conn->prepare("SELECT id FROM usuario WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo "El usuario '$username' ya existe.\n";
    exit;
}

// Insertar usuario
$stmt = $conn->prepare("INSERT INTO usuario (username, password, email, rol, estado) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $password, $email, $rol, $estado);

if ($stmt->execute()) {
    echo "Usuario creado exitosamente:\n";
    echo "Usuario: $username\n";
    echo "Contraseña: admin123\n";
    echo "Email: $email\n";
    echo "Rol: $rol\n";
} else {
    echo "Error al crear usuario: " . $conn->error . "\n";
}
?>