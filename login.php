<?php
session_start();
include 'conexion.php';

$error_msg = '';

if (isset($_SESSION['usuario_id']) && isset($_SESSION['rol_id'])) {
    if ($_SESSION['rol_id'] == 1) {
        header("Location: dashboard.php");
        exit;
    } elseif ($_SESSION['rol_id'] == 2) {
        header("Location: panel_medico.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = $_POST['clave'] ?? '';
    $rol_id = $_POST['rol_id'] ?? '';

    // Solo permitir rol 1 (Admin) o 2 (Medico)
    if (!$usuario || !$clave || !in_array($rol_id, ['1', '2'])) {
        $error_msg = 'Complete todos los campos correctamente.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ? AND rol_id = ? AND estado = 'Activo'");
            $stmt->execute([$usuario, $rol_id]);

            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($clave, $user['clave'])) {
                    $_SESSION['usuario_id'] = $user['id'];
                    $_SESSION['rol_id'] = $user['rol_id'];
                    $_SESSION['nombre'] = $user['nombre'];

                    if ($user['rol_id'] == 1) {
                        header("Location: dashboard.php");
                    } else {
                        header("Location: panel_medico.php");
                    }
                    exit;
                } else {
                    $error_msg = 'Usuario o contraseña incorrectos.';
                }
            } else {
                $error_msg = 'Usuario o contraseña incorrectos.';
            }
        } catch (PDOException $e) {
            error_log("Error login médico/admin: " . $e->getMessage());
            $error_msg = 'Error de sistema, intente más tarde.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Login Médico / Administrador</title>
<style>
body { font-family: Arial, sans-serif; background: #eef2f7; display: flex; justify-content: center; align-items: center; height: 100vh; margin:0; }
.login-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px #ccc; width: 360px; }
h2 { text-align: center; margin-bottom: 20px; }
.error { color: red; text-align:center; margin-bottom: 15px; }
input, select, button { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 6px; border: 1px solid #ccc; font-size: 16px; box-sizing: border-box; }
button { background: #007bff; color: white; font-weight: bold; border: none; cursor: pointer; }
button:hover { background: #0056b3; }
</style>
</head>
<body>

<div class="login-box">
    <h2>Iniciar Sesión Médico / Administrador</h2>

    <?php if ($error_msg): ?>
    <div class="error"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" name="usuario" placeholder="Usuario" required autofocus>
        <input type="password" name="clave" placeholder="Contraseña" required>
        <select name="rol_id" required>
            <option value="">Seleccione Rol</option>
            <option value="1">Administrador</option>
            <option value="2">Médico</option>
        </select>
        <button type="submit">Ingresar</button>
    </form>
</div>

</body>
</html>
