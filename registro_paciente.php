<?php
include 'conexion.php';

$errores = [];
$nombre = $dni = $fecha_nacimiento = $sexo = $telefono = $correo = $direccion = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $dni = trim($_POST['dni']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $sexo = $_POST['sexo'];
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $direccion = trim($_POST['direccion']);

    if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
    if (empty($dni)) $errores[] = "El DNI es obligatorio.";
    if (!in_array($sexo, ['M', 'F'])) $errores[] = "Seleccione un sexo válido.";
    if ($correo && !filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = "Correo inválido.";

    $stmt = $pdo->prepare("SELECT id FROM pacientes WHERE dni = ?");
    $stmt->execute([$dni]);
    if ($stmt->rowCount() > 0) $errores[] = "Este DNI ya está registrado.";

    if (empty($errores)) {
        $stmt = $pdo->prepare("INSERT INTO pacientes (nombre, dni, fecha_nacimiento, sexo, telefono, correo, direccion)
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $dni, $fecha_nacimiento, $sexo, $telefono, $correo, $direccion]);
        header("Location: inicio.php?ok=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Paciente</title>
    <link rel="stylesheet" href="estilos/style.css">
    <style>
        .registro-container {
            width: 400px;
            margin: 40px auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .registro-container h2 {
            text-align: center;
        }
        .registro-container label {
            display: block;
            margin: 10px 0 5px;
        }
        .registro-container input,
        .registro-container select,
        .registro-container textarea {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .registro-container button {
            margin-top: 15px;
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
        }
        .registro-container .error {
            color: red;
        }
    </style>
</head>
<body>
<div class="registro-container">
    <h2>Registro de Paciente</h2>

    <?php if (!empty($errores)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errores as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="registro_paciente.php">
        <label>Nombre completo:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($nombre) ?>" required>

        <label>DNI:</label>
        <input type="text" name="dni" value="<?= htmlspecialchars($dni) ?>" required>

        <label>Fecha de nacimiento:</label>
        <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($fecha_nacimiento) ?>">

        <label>Sexo:</label>
        <select name="sexo" required>
            <option value="">--Seleccione--</option>
            <option value="M" <?= $sexo == 'M' ? 'selected' : '' ?>>Masculino</option>
            <option value="F" <?= $sexo == 'F' ? 'selected' : '' ?>>Femenino</option>
        </select>

        <label>Teléfono:</label>
        <input type="text" name="telefono" value="<?= htmlspecialchars($telefono) ?>">

        <label>Correo:</label>
        <input type="email" name="correo" value="<?= htmlspecialchars($correo) ?>">

        <label>Dirección:</label>
        <textarea name="direccion"><?= htmlspecialchars($direccion) ?></textarea>

        <button type="submit">Registrarse</button>
    </form>
    <br>
    <a href="inicio.php">← Volver al inicio</a>
</div>
</body>
</html>
