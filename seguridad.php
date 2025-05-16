<?php
// Inicia sesión solo si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica que el usuario esté autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

// Incluye la conexión a la base de datos
include 'conexion.php';

// Obtiene variables de sesión de forma segura
$nombre = $_SESSION['nombre'] ?? 'Usuario';
$rol_id = $_SESSION['rol_id'] ?? null;
?>