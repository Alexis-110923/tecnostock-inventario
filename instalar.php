<?php
// Conexión a MySQL
$host = "localhost";
$usuario = "root";
$clave = "";

try {
    $pdo = new PDO("mysql:host=$host", $usuario, $clave);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Crear base de datos
    $pdo->exec("CREATE DATABASE IF NOT EXISTS tecnostock CHARACTER SET utf8mb4");
    $pdo->exec("USE tecnostock");

    // Crear tablas
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS usuarios (
            id_usuario INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(50), 
            correo VARCHAR(50), 
            clave VARCHAR(255), 
            estado TINYINT DEFAULT 1
        );
        
        CREATE TABLE IF NOT EXISTS categorias (
            id_categoria INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(50), 
            descripcion VARCHAR(100), 
            estado TINYINT DEFAULT 1
        );
        
        CREATE TABLE IF NOT EXISTS productos (
            id_producto INT AUTO_INCREMENT PRIMARY KEY,
            codigo VARCHAR(20) UNIQUE,
            nombre VARCHAR(100), 
            descripcion VARCHAR(255), 
            precio DECIMAL(10,2) CHECK (precio >= 0),
            stock_actual INT CHECK (stock_actual >= 0), 
            stock_minimo INT CHECK (stock_minimo >= 0),
            estado TINYINT DEFAULT 1,
            id_categoria INT,
            FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
        );
        
        CREATE TABLE IF NOT EXISTS movimientos (
            id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
            fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
            tipo ENUM('entrada', 'salida'),
            cantidad INT CHECK (cantidad > 0),
            observacion VARCHAR(255),
            id_producto INT, 
            id_usuario INT,
            FOREIGN KEY (id_producto) REFERENCES productos(id_producto),
            FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        );
    ");

    // Datos de prueba
    $clave_hash = password_hash("1234", PASSWORD_DEFAULT);
    $pdo->exec("INSERT INTO usuarios (nombre, correo, clave) VALUES ('Admin', 'admin@tecno.com', '$clave_hash')");
    $pdo->exec("INSERT INTO categorias (nombre, descripcion) VALUES ('Accesorios', 'Cables y cargadores')");
    $pdo->exec("INSERT INTO productos (codigo, nombre, precio, stock_actual, stock_minimo, id_categoria) VALUES ('ACC001', 'Cable USB Tipo C', 5.99, 5, 10, 1)");

    echo "Base de datos creada correctamente<br>";
    echo "Usuario: admin@tecno.com | Clave: 1234";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>