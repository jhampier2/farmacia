<?php
include 'conexion.php';

$errores = [];
$nombre = $usuario = $correo = $rol_id = "";
$roles = [];

try {
    // Cargar roles disponibles
    $roles = $pdo->query("SELECT id, nombre FROM roles ORDER BY nombre ASC")->fetchAll();
} catch (Exception $e) {
    $errores[] = "Error al cargar roles: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $usuario = trim($_POST['usuario']);
    $clave = $_POST['clave'];
    $clave2 = $_POST['clave2'];
    $correo = trim($_POST['correo']);
    $rol_id = $_POST['rol_id'];

    if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
    if (empty($usuario)) $errores[] = "El usuario es obligatorio.";
    if (empty($clave)) $errores[] = "La contraseña es obligatoria.";
    if ($clave !== $clave2) $errores[] = "Las contraseñas no coinciden.";
    if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = "Correo inválido.";

    // Validar que rol_id exista en los roles cargados
    $rol_ids_disponibles = array_column($roles, 'id');
    if (!in_array($rol_id, $rol_ids_disponibles)) {
        $errores[] = "Rol seleccionado no es válido.";
    }

    // Verificar que el usuario no exista
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);
    if ($stmt->rowCount() > 0) {
        $errores[] = "El nombre de usuario ya está en uso.";
    }

    // Si todo es válido, insertar usuario
    if (empty($errores)) {
        $claveHash = password_hash($clave, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, usuario, clave, correo, rol_id, estado)
                               VALUES (?, ?, ?, ?, ?, 'Activo')");
        $stmt->execute([$nombre, $usuario, $claveHash, $correo, $rol_id]);
        $usuario_id = $pdo->lastInsertId();

        // Si el rol es Médico, agregar a tabla medicos
        if ($rol_id == 2) {
            $especialidad_id = 1; // predeterminado
            $cmp = 'CMP-' . rand(1000, 9999);
            $stmt = $pdo->prepare("INSERT INTO medicos (usuario_id, especialidad_id, cmp) VALUES (?, ?, ?)");
            $stmt->execute([$usuario_id, $especialidad_id, $cmp]);
        }

        header("Location: registro_usuario.php?ok=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .form-container {
            width: 400px;
            margin: 40px auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
            color: #555;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 14px;
        }
        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error, .success, .warning {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            text-align: center;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Registrar Usuario</h2>

    <?php if (!empty($errores)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errores as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['ok'])): ?>
        <div class="success">✅ Usuario registrado correctamente.</div>
    <?php endif; ?>

    <?php if (count($roles) === 0): ?>
        <div class="warning">
            ⚠ No hay roles disponibles. Agrega registros a la tabla <strong>roles</strong>.
        </div>
    <?php endif; ?>

    <form method="POST" action="registro_usuario.php" novalidate>
        <label>Nombre completo:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($nombre) ?>" required>

        <label>Usuario (login):</label>
        <input type="text" name="usuario" value="<?= htmlspecialchars($usuario) ?>" required>

        <label>Correo electrónico:</label>
        <input type="email" name="correo" value="<?= htmlspecialchars($correo) ?>" required>

        <label>Rol:</label>
        <select name="rol_id" required>
            <option value="">--Seleccione--</option>
            <?php foreach ($roles as $rol): ?>
                <option value="<?= $rol['id'] ?>" <?= $rol_id == $rol['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($rol['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Contraseña:</label>
        <input type="password" name="clave" required>

        <label>Confirmar Contraseña:</label>
        <input type="password" name="clave2" required>

        <button type="submit">Registrar</button>
        <a href="index.php">Iniciar session</a>
    </form>
</div>

</body>
</html>
