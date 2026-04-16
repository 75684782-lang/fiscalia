<?php
session_start();
require_once("../config/conexion.php");
require_once("../config/rutas.php");
require_once("../config/permisos.php");

// Validar autenticación
if (!isset($_SESSION['usuario'])) {
    header("Location: " . ruta('login'));
    exit;
}

// ====== CREAR USUARIO ======
if (isset($_POST['crear_usuario'])) {
    if (!tienePermiso('admin_usuarios')) {
        $_SESSION['mensaje'] = 'No tienes permisos para crear usuarios';
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: " . ruta('admin_usuarios'));
        exit;
    }

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $rol = trim($_POST['rol']);
    $estado = trim($_POST['estado']);

    if (empty($username) || empty($password) || empty($email) || empty($rol)) {
        $_SESSION['mensaje'] = 'Todos los campos son obligatorios';
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: " . ruta('admin_usuarios'));
        exit;
    }

    $stmt = $conn->prepare("SELECT id FROM usuario WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['mensaje'] = 'El nombre de usuario ya existe';
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: " . ruta('admin_usuarios'));
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuario (username, password, email, rol, estado) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $password_hash, $email, $rol, $estado);

    if ($stmt->execute()) {
        $nuevo_id = $conn->insert_id;
        registrarAuditoria('usuario', 'INSERT', $nuevo_id, null, compact('username', 'email', 'rol', 'estado'));
        $_SESSION['mensaje'] = 'Usuario creado correctamente';
        $_SESSION['tipo_mensaje'] = 'exito';
    } else {
        $_SESSION['mensaje'] = 'Error al crear usuario';
        $_SESSION['tipo_mensaje'] = 'error';
    }

    header("Location: " . ruta('admin_usuarios'));
    exit;
}

// ====== EDITAR USUARIO ======
if (isset($_POST['editar_usuario'])) {
    if (!tienePermiso('admin_usuarios')) {
        $_SESSION['mensaje'] = 'No tienes permisos para editar usuarios';
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: " . ruta('admin_usuarios'));
        exit;
    }

    $id = intval($_POST['id']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $rol = trim($_POST['rol']);
    $estado = trim($_POST['estado']);
    $cambiar_password = isset($_POST['cambiar_password']) && !empty($_POST['password']);

    $stmt = $conn->prepare("SELECT username, email, rol, estado FROM usuario WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $anterior = $stmt->get_result()->fetch_assoc();

    if (empty($username) || empty($email) || empty($rol)) {
        $_SESSION['mensaje'] = 'Los campos obligatorios no pueden estar vacíos';
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: " . ruta('admin_usuarios'));
        exit;
    }

    $stmt = $conn->prepare("SELECT id FROM usuario WHERE username = ? AND id != ?");
    $stmt->bind_param("si", $username, $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['mensaje'] = 'El nombre de usuario ya existe';
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: " . ruta('admin_usuarios'));
        exit;
    }

    if ($cambiar_password) {
        $password_hash = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuario SET username = ?, password = ?, email = ?, rol = ?, estado = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $username, $password_hash, $email, $rol, $estado, $id);
    } else {
        $stmt = $conn->prepare("UPDATE usuario SET username = ?, email = ?, rol = ?, estado = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $username, $email, $rol, $estado, $id);
    }

    if ($stmt->execute()) {
        $nuevos_datos = compact('username', 'email', 'rol', 'estado');
        if ($cambiar_password) $nuevos_datos['password'] = '[CAMBIADA]';
        registrarAuditoria('usuario', 'UPDATE', $id, $anterior, $nuevos_datos);
        $_SESSION['mensaje'] = 'Usuario actualizado correctamente';
        $_SESSION['tipo_mensaje'] = 'exito';
    } else {
        $_SESSION['mensaje'] = 'Error al actualizar usuario';
        $_SESSION['tipo_mensaje'] = 'error';
    }

    header("Location: " . ruta('admin_usuarios'));
    exit;
}

// ====== ELIMINAR USUARIO ======
if (isset($_GET['eliminar_usuario'])) {
    if (!tienePermiso('admin_usuarios')) {
        $_SESSION['mensaje'] = 'No tienes permisos para eliminar usuarios';
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: " . ruta('admin_usuarios'));
        exit;
    }

    $id = intval($_GET['eliminar_usuario']);

    if ($id == $_SESSION['usuario_id']) {
        $_SESSION['mensaje'] = 'No puedes eliminar tu propio usuario';
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: " . ruta('admin_usuarios'));
        exit;
    }

    $stmt = $conn->prepare("SELECT username, email, rol, estado FROM usuario WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $anterior = $stmt->get_result()->fetch_assoc();

    $stmt = $conn->prepare("DELETE FROM usuario WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        registrarAuditoria('usuario', 'DELETE', $id, $anterior, null);
        $_SESSION['mensaje'] = 'Usuario eliminado correctamente';
        $_SESSION['tipo_mensaje'] = 'exito';
    } else {
        $_SESSION['mensaje'] = 'Error al eliminar usuario';
        $_SESSION['tipo_mensaje'] = 'error';
    }

    header("Location: " . ruta('admin_usuarios'));
    exit;
}

// ====== ASIGNAR ROLES ======
if (isset($_POST['asignar_rol'])) {
    if (!tienePermiso('admin_roles')) {
        $_SESSION['mensaje'] = 'No tienes permisos para asignar roles';
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: " . ruta('admin_asignar_roles'));
        exit;
    }

    $usuario_id = intval($_POST['usuario_id']);
    $nuevo_rol = $conn->real_escape_string($_POST['rol']);

    $roles_validos = ['administrador', 'moderador', 'usuario', 'visor'];
    if (!in_array($nuevo_rol, $roles_validos)) {
        $_SESSION['mensaje'] = 'Rol no válido';
        $_SESSION['tipo_mensaje'] = 'error';
        header("Location: " . ruta('admin_asignar_roles'));
        exit;
    }

    $stmt = $conn->prepare("SELECT rol FROM usuario WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    $rol_anterior = $usuario['rol'];

    $stmt = $conn->prepare("UPDATE usuario SET rol = ? WHERE id = ?");
    $stmt->bind_param("si", $nuevo_rol, $usuario_id);
    if ($stmt->execute()) {
        registrarAuditoria('usuario', 'UPDATE_ROL', $usuario_id, ['rol' => $rol_anterior], ['rol' => $nuevo_rol]);
        $_SESSION['mensaje'] = 'Rol asignado correctamente';
        $_SESSION['tipo_mensaje'] = 'exito';
    } else {
        $_SESSION['mensaje'] = 'Error al asignar rol';
        $_SESSION['tipo_mensaje'] = 'error';
    }

    header("Location: " . ruta('admin_asignar_roles'));
    exit;
}

// Si no hay una acción POST o GET, no hacer nada aquí
// El index.php se encargará de cargar las vistas
?>