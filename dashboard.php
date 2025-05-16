<?php
// Iniciar sesión solo si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validar sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

// Obtener rol de sesión
$rol_id = $_SESSION['rol_id'] ?? null;
$nombre = $_SESSION['nombre'] ?? 'Usuario';

// Permitir acceso solo a administrador (rol_id = 1)
if ($rol_id != 1) {
    echo "Acceso no autorizado. Tu rol es: " . htmlspecialchars($rol_id);
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Panel de Administración</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f7;
            margin: 0; padding: 0;
        }
        header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
        }
        nav {
            background-color: white;
            padding: 15px;
            display: flex;
            justify-content: center;
            gap: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        nav a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        nav a:hover {
            background-color: #d0e7ff;
        }
        main {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .bienvenida {
            font-size: 20px;
            margin-bottom: 20px;
        }
        footer {
            text-align: center;
            color: #666;
            margin: 40px 0 20px;
        }
        .logout-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #dc3545;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
        }
        .logout-btn:hover {
            background: #b02a37;
        }
    </style>
</head>
<body>

<header>Panel de Administración</header>

<nav>
    <a href="modulos/usuarios/listar.php">Usuarios</a>
    <a href="modulos/pacientes/listar.php">Pacientes</a>
    <a href="modulos/medicos/listar.php">Médicos</a>
    <a href="modulos/horarios/listar.php">Horarios</a>
    <a href="modulos/citas/listar.php">Citas</a>
</nav>

<a href="logout.php" class="logout-btn">Cerrar sesión</a>

<main>
    <div class="bienvenida">
        👋 Bienvenido, <strong><?= htmlspecialchars($nombre) ?></strong>
    </div>
    <p>Desde este panel puedes administrar todas las funcionalidades del sistema:</p>
    <ul style="list-style: none; font-size: 18px; padding-left: 0;">
        <li>✔ Gestión de usuarios (administradores y médicos)</li>
        <li>✔ Gestión de pacientes</li>
        <li>✔ Gestión de médicos y sus especialidades</li>
        <li>✔ Asignación y gestión de horarios</li>
        <li>✔ Control y seguimiento de citas médicas</li>
    </ul>
</main>

<footer>
    &copy; <?= date('Y') ?> Clínica - Todos los derechos reservados
</footer>

</body>
</html>
