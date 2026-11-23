<?php
session_start();
require 'conexion.php';

// Si no está logueado, mandar al login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

// Búsqueda
$buscar = isset($_GET['buscar']) ? $_GET['buscar'] : "";
$sql = "SELECT p.*, c.nombre as categoria FROM productos p 
        LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
        WHERE p.estado = 1 AND (p.nombre LIKE ? OR p.codigo LIKE ? OR c.nombre LIKE ?)";
$stmt = $pdo->prepare($sql);
$termino = "%$buscar%";
$stmt->execute([$termino, $termino, $termino]);
$productos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel - TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand">TecnoStock - <?= $_SESSION['nombre'] ?></span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Salir</a>
    </div>
</nav>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-8">
            <form class="d-flex" method="GET">
                <input type="text" name="buscar" class="form-control me-2" 
                       placeholder="Buscar por nombre, código o categoría..." 
                       value="<?= htmlspecialchars($buscar) ?>">
                <button class="btn btn-primary">Buscar</button>
            </form>
        </div>
        <div class="col-md-4 text-end">
            <a href="movimiento.php" class="btn btn-success">Registrar Movimiento</a>
<a href="historial.php" class="btn btn-info">Ver Historial</a>
            <a href="producto_form.php" class="btn btn-secondary">Nuevo Producto</a>
        </div>
    </div>

    <table class="table table-striped table-hover bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productos as $p): 
                $alerta = ($p['stock_actual'] < $p['stock_minimo']) ? "table-danger" : "";
                $badge = ($p['stock_actual'] < $p['stock_minimo']) ? "<span class='badge bg-danger'>Stock Bajo</span>" : "";
            ?>
            <tr class="<?= $alerta ?>">
                <td><?= $p['codigo'] ?></td>
                <td><?= $p['nombre'] ?> <?= $badge ?></td>
                <td><?= $p['categoria'] ?></td>
                <td>$<?= number_format($p['precio'], 0, ',', '.') ?></td>
                <td><?= $p['stock_actual'] ?> / Mín: <?= $p['stock_minimo'] ?></td>
                <td>
                    <a href="producto_form.php?id=<?= $p['id_producto'] ?>" class="btn btn-sm btn-warning">Editar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
