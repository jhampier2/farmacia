<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Sistema Clínica - Inicio</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .inicio-container {
            background: white;
            padding: 40px 50px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            width: 350px;
            text-align: center;
        }
        .inicio-container h1 {
            margin-bottom: 30px;
            color: #333;
        }
        .btn {
            display: block;
            margin: 15px 0;
            padding: 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 8px rgba(0,123,255,0.3);
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="inicio-container">
    <h1>Bienvenido a la Clínica Nova</h1>

    <a href="registro_paciente.php" class="btn">Registrarse como Paciente</a>
    <a href="login_paciente.php" class="btn">Iniciar Sesión como Paciente</a>
    <a href="login.php" class="btn">Iniciar Sesión Médico / Administrador</a>
    <a href="registro_usuario.php" class="btn">Registrarse Médico / Administrador</a>
</div>

</body>
</html>
