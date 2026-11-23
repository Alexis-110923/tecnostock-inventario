<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

// Obtener todos los movimientos con los datos relacionados
$sql = "SELECT m.*, p.nombre as producto, u.nombre as usuario 
        FROM movimientos m
        LEFT JOIN productos p ON m.id_producto = p.id_producto
        LEFT JOIN usuarios u ON m.id_usuario = u.id_usuario
        ORDER BY m.fecha_hora DESC";
$stmt = $pdo->query($sql);
$movimientos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Movimientos - TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand">TecnoStock - Historial</span>
        <a href="index.php" class="btn btn-outline-light btn-sm">Volver al panel</a>
    </div>
</nav>

<div class="container mt-4">
    <h4>Historial de Movimientos</h4>
    <p class="text-muted">Aquí se registran todas las entradas y salidas con fecha, tipo, cantidad, producto y usuario responsable.</p>
    
    <table class="table table-striped table-hover bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>Fecha y Hora</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Producto</th>
                <th>Usuario Responsable</th>
                <th>Observación</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($movimientos) == 0): ?>
                <tr>
                    <td colspan="6" class="text-center">No hay movimientos registrados aún</td>
                </tr>
            <?php else: ?>
                <?php foreach ($movimientos as $m): ?>
                    <tr>
                        <td><?= $m['fecha_hora'] ?></td>
                        <td>
                            <?php if ($m['tipo'] == 'entrada'): ?>
                                <span class="badge bg-success">Entrada</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Salida</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $m['cantidad'] ?></td>
                        <td><?= $m['producto'] ?></td>
                        <td><?= $m['usuario'] ?></td>
                        <td><?= $m['observacion'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>