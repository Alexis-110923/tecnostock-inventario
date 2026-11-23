# Análisis del Sistema TecnoStock

## 1. Problema
TecnoStock es un emprendimiento que vende accesorios tecnológicos. Actualmente usan una planilla de Excel para llevar el inventario, lo que les ha causado varios problemas:
- Códigos de productos duplicados
- Errores en las cantidades disponibles
- No tienen claridad sobre el stock real

## 2. Actores del sistema
- **Encargado de inventario**: Es el único usuario del sistema. Se encarga de administrar productos, categorías y registrar los movimientos de stock.

## 3. Alcance
Sistema web local que funciona en XAMPP. Permite:
- Tener un catálogo centralizado de productos
- Controlar entradas y salidas de stock
- Alertar cuando un producto está por agotarse

## 4. Requerimientos funcionales
- RF1: Inicio de sesión con usuario registrado
- RF2: Crear productos (código, nombre, categoría, descripción, precio, stock actual, stock mínimo)
- RF3: Listar todos los productos activos
- RF4: Buscar productos por nombre, código o categoría
- RF5: Editar productos (precio, descripción, categoría, cantidades)
- RF6: Desactivar productos (borrado lógico, no se eliminan)
- RF7: Registrar entradas y salidas de stock
- RF8: Mostrar alerta visual cuando stock_actual < stock_minimo

## 5. Reglas de negocio
- RN1: El código de producto es único (no se puede repetir)
- RN2: Precio, stock y cantidad de movimiento no pueden ser negativos
- RN3: Todo movimiento registra fecha, tipo, cantidad, producto y usuario
- RN4: Una salida no puede dejar el stock bajo cero
- RN5: Los productos se DESACTIVAN, nunca se eliminan físicamente
- RN6: El stock se actualiza automáticamente después de cada movimiento

## 6. Trazabilidad (cómo se implementó cada cosa)
| Requerimiento | Cómo se implementó |
|---|---|
| RF1 | login.php con sesiones PHP y password_hash |
| RF2, RF5, RF6 | producto_form.php |
| RF3, RF4 | index.php con LIKE en SQL |
| RF7, RN4, RN6 | movimiento.php con UPDATE automático |
| RF8, RN2 | Validación en JS + PHP + CHECK en la BD |
| RN1 | UNIQUE en el campo codigo de la tabla productos |
| RN5 | Campo estado TINYINT (1=activo, 0=desactivado) |