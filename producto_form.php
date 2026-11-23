<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$mensaje = "";
$producto = [
    'id_producto' => '',
    'codigo' => '',
    'nombre' => '',
    'descripcion' => '',
    'precio' => '',
    'stock_actual' => '',
    'stock_minimo' => '',
    'id_categoria' => ''
];
$modo = "Crear";

// Si viene un ID, es modo editar
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id_producto = ?");
    $stmt->execute([$_GET['id']]);
    $producto = $stmt->fetch();
    $modo = "Editar";
}

// Obtener categorías
$categorias = $pdo->query("SELECT * FROM categorias WHERE estado = 1")->fetchAll();

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'] ?? '';
    
    // Desactivar producto
    if ($accion == 'desactivar') {
        $nuevo_codigo = $_POST['codigo'] . '_DESACTIVADO_' . time();
        $stmt = $pdo->prepare("UPDATE productos SET estado = 0, codigo = ? WHERE id_producto = ?");
        $stmt->execute([$nuevo_codigo, $_POST['id_producto']]);
        header("Location: index.php");
        exit;
    }
    
    $codigo = trim($_POST['codigo']);
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = $_POST['precio'];
    $stock_actual = $_POST['stock_actual'];
    $stock_minimo = $_POST['stock_minimo'];
    $id_categoria = $_POST['id_categoria'];
    $id_producto = $_POST['id_producto'] ?? null;

    // Validación: no negativos
    if ($precio < 0 || $stock_actual < 0 || $stock_minimo < 0) {
        $mensaje = "<div class='alert alert-danger'>Los valores no pueden ser negativos</div>";
    } else {
        // Validación: código único
        $sql_check = "SELECT id_producto FROM productos WHERE codigo = ? AND estado = 1";
        $params = [$codigo];
        if ($id_producto) {
            $sql_check .= " AND id_producto != ?";
            $params[] = $id_producto;
        }
        $stmt = $pdo->prepare($sql_check);
        $stmt->execute($params);
        
        if ($stmt->fetch()) {
            $mensaje = "<div class='alert alert-danger'>El código ya existe</div>";
        } else {
            if ($id_producto) {
                // EDITAR
                $stmt = $pdo->prepare("UPDATE productos SET codigo=?, nombre=?, descripcion=?, precio=?, stock_actual=?, stock_minimo=?, id_categoria=? WHERE id_producto=?");
                $stmt->execute([$codigo, $nombre, $descripcion, $precio, $stock_actual, $stock_minimo, $id_categoria, $id_producto]);
                $mensaje = "<div class='alert alert-success'>Producto actualizado</div>";
            } else {
                // CREAR
                $stmt = $pdo->prepare("INSERT INTO productos (codigo, nombre, descripcion, precio, stock_actual, stock_minimo, id_categoria) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$codigo, $nombre, $descripcion, $precio, $stock_actual, $stock_minimo, $id_categoria]);
                $mensaje = "<div class='alert alert-success'>Producto creado</div>";
                $producto = ['id_producto'=>'', 'codigo'=>'', 'nombre'=>'', 'descripcion'=>'', 'precio'=>'', 'stock_actual'=>'', 'stock_minimo'=>'', 'id_categoria'=>''];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $modo ?> Producto - TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <a href="index.php" class="btn btn-secondary mb-3">← Volver</a>
    
    <div class="card shadow p-4">
        <h4><?= $modo ?> Producto</h4>
        <?= $mensaje ?>
        
        <form method="POST" id="formProducto" onsubmit="return validarProducto()">
            <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Código *</label>
                    <input type="text" name="codigo" id="codigo" class="form-control" 
                           value="<?= $producto['codigo'] ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Nombre *</label>
                    <input type="text" name="nombre" class="form-control" 
                           value="<?= $producto['nombre'] ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label>Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3"><?= $producto['descripcion'] ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Precio (en pesos chilenos) *</label>
                    <input type="number" name="precio" id="precio" class="form-control" 
                           step="1" min="0" value="<?= $producto['precio'] ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Stock Actual *</label>
                    <input type="number" name="stock_actual" id="stock_actual" class="form-control" 
                           min="0" value="<?= $producto['stock_actual'] ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Stock Mínimo *</label>
                    <input type="number" name="stock_minimo" id="stock_minimo" class="form-control" 
                           min="0" value="<?= $producto['stock_minimo'] ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label>Categoría *</label>
                <select name="id_categoria" class="form-select" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?= $c['id_categoria'] ?>" 
                            <?= ($producto['id_categoria']==$c['id_categoria'])?'selected':'' ?>>
                            <?= $c['nombre'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Guardar</button>
            
            <?php if ($producto['id_producto']): ?>
                <button type="submit" name="accion" value="desactivar" class="btn btn-danger ms-2"
                        onclick="return confirm('¿Seguro que deseas desactivar este producto?')">
                    Desactivar producto
                </button>
            <?php endif; ?>
        </form>
    </div>
</div>

<script>
function validarProducto() {
    const precio = parseFloat(document.getElementById('precio').value);
    const stockA = parseInt(document.getElementById('stock_actual').value);
    const stockM = parseInt(document.getElementById('stock_minimo').value);
    const codigo = document.getElementById('codigo').value.trim();
    
    if (!codigo) { 
        alert('El código es obligatorio'); 
        return false; 
    }
    if (precio < 0) { 
        alert('El precio no puede ser negativo'); 
        return false; 
    }
    if (stockA < 0) { 
        alert('El stock actual no puede ser negativo'); 
        return false; 
    }
    if (stockM < 0) { 
        alert('El stock mínimo no puede ser negativo'); 
        return false; 
    }
    return true;
}
</script>
</body>
</html>