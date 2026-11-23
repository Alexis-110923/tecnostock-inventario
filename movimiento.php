<?php
session_start();
require 'conexion.php';

// Si no está logueado, mandar al login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$mensaje = "";
$productos = $pdo->query("SELECT id_producto, nombre, stock_actual FROM productos WHERE estado = 1")->fetchAll();

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_producto = $_POST['id_producto'];
    $tipo = $_POST['tipo'];
    $cantidad = (int)$_POST['cantidad'];
    $obs = trim($_POST['observacion']);
    $id_usuario = $_SESSION['id_usuario'];

    // Validación PHP: cantidad positiva
    if ($cantidad <= 0) {
        $mensaje = "<div class='alert alert-danger'>La cantidad debe ser mayor a cero</div>";
    } else {
        // Obtener stock actual
        $stmt = $pdo->prepare("SELECT stock_actual FROM productos WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        $stock_actual = $stmt->fetchColumn();

        // Regla de negocio: una salida no puede dejar el stock bajo cero
        if ($tipo == 'salida' && $cantidad > $stock_actual) {
            $mensaje = "<div class='alert alert-danger'>No hay suficiente stock. Stock actual: $stock_actual</div>";
        } else {
            // 1. Registrar movimiento
            $stmt = $pdo->prepare("INSERT INTO movimientos (tipo, cantidad, observacion, id_producto, id_usuario) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$tipo, $cantidad, $obs, $id_producto, $id_usuario]);

            // 2. Actualizar stock automáticamente
            $nuevo_stock = ($tipo == 'entrada') ? $stock_actual + $cantidad : $stock_actual - $cantidad;
            $stmt = $pdo->prepare("UPDATE productos SET stock_actual = ? WHERE id_producto = ?");
            $stmt->execute([$nuevo_stock, $id_producto]);

            $mensaje = "<div class='alert alert-success'>Movimiento registrado. Nuevo stock: $nuevo_stock</div>";
            
            // Refrescar la lista de productos
            $productos = $pdo->query("SELECT id_producto, nombre, stock_actual FROM productos WHERE estado = 1")->fetchAll();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Movimientos - TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <a href="index.php" class="btn btn-secondary mb-3">← Volver</a>
    
    <div class="card shadow p-4">
        <h4>Registrar Entrada / Salida</h4>
        <?= $mensaje ?>
        
        <form method="POST" onsubmit="return validarMovimiento()">
            <div class="mb-3">
                <label>Producto *</label>
                <select name="id_producto" class="form-select" required>
                    <option value="">Seleccione un producto...</option>
                    <?php foreach ($productos as $p): ?>
                        <option value="<?= $p['id_producto'] ?>">
                            <?= $p['nombre'] ?> (Stock actual: <?= $p['stock_actual'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Tipo de Movimiento *</label>
                    <select name="tipo" class="form-select" required>
                        <option value="entrada">Entrada (Aumenta stock)</option>
                        <option value="salida">Salida (Disminuye stock)</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Cantidad *</label>
                    <input type="number" name="cantidad" id="cantidad" class="form-control" min="1" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label>Observación</label>
                <input type="text" name="observacion" class="form-control" placeholder="Ej: Compra a proveedor, Venta a cliente...">
            </div>
            
            <button type="submit" class="btn btn-primary">Guardar Movimiento</button>
        </form>
    </div>
</div>

<!-- Validación JavaScript -->
<script>
function validarMovimiento() {
    const cantidad = parseInt(document.getElementById('cantidad').value);
    
    if (!cantidad || cantidad <= 0) {
        alert('La cantidad debe ser mayor a cero');
        return false;
    }
    
    return true;
}
</script>
</body>
</html>