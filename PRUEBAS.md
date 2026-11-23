# Casos de Prueba

## Prueba 1: Flujo normal - Entrada de stock
- **Paso**: Login → Registrar Movimiento → Entrada de 20 unidades al Cable USB
- **Resultado esperado**: Stock pasa de 5 a 25, la alerta roja desaparece
- **Resultado obtenido**: ✅ OK

## Prueba 2: Caso de error - Salida mayor al stock
- **Paso**: Registrar Salida de 100 unidades del Cable USB (stock actual: 25)
- **Resultado esperado**: Mensaje de error "No hay suficiente stock. Stock actual: 25"
- **Resultado obtenido**: ✅ OK

## Prueba 3: Caso límite - Código duplicado
- **Paso**: Crear producto con código "ACC001" (ya existe)
- **Resultado esperado**: Error "El código ya existe"
- **Resultado obtenido**: ✅ OK

## Prueba 4: Validación JS - Precio negativo
- **Paso**: Ingresar precio -5 en formulario de producto
- **Resultado esperado**: Alerta JS impide el envío del formulario
- **Resultado obtenido**: ✅ OK

## Prueba 5: Borrado lógico
- **Paso**: Desactivar un producto desde el formulario de edición
- **Resultado esperado**: Desaparece del listado, pero queda en la BD con estado=0
- **Resultado obtenido**: ✅ OK

## Prueba 6: Login con datos incorrectos
- **Paso**: Intentar login con correo o clave incorrecta
- **Resultado esperado**: Mensaje "Datos incorrectos"
- **Resultado obtenido**: ✅ OK

## Prueba 7: Búsqueda de productos
- **Paso**: Buscar "cable" en el panel principal
- **Resultado esperado**: Solo aparecen productos que contienen "cable" en nombre, código o categoría
- **Resultado obtenido**: ✅ OK

## Prueba 8: Alerta de stock bajo
- **Paso**: Crear producto con stock_actual=5 y stock_minimo=10
- **Resultado esperado**: Aparece en fila roja con badge "Stock Bajo"
- **Resultado obtenido**: ✅ OK