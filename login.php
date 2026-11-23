<?php
session_start();
require 'conexion.php';

$error = "";

// Si ya está logueado, ir al panel
if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

// Procesar login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);
    $clave = $_POST['clave'];
    
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ? AND estado = 1");
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch();
    
    if ($usuario && password_verify($clave, $usuario['clave'])) {
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre'] = $usuario['nombre'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Datos incorrectos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">TecnoStock</h3>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label>Correo</label>
                            <input type="email" name="correo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Clave</label>
                            <input type="password" name="clave" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </form>
                    
                    <div class="alert alert-info mt-3 small">
                        <b>Prueba:</b> admin@tecno.com / 1234
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>