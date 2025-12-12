# Campos Corregidos en el API - Base de Datos

## ✅ Correcciones Aplicadas

### 1. Tabla `tickets`
**Campos que NO existen y fueron eliminados:**
- ❌ `requisicion` - No existe en la tabla
- ❌ `cliente` - No existe en la tabla
- ❌ `completed_at` - Solo se actualiza con calificación del usuario

**Campos correctos agregados:**
- ✅ `passenger_count` - Número de pasajeros
- ✅ `additional_notes` - Notas adicionales

**Status corregidos:**
- ❌ `en_progreso` → ✅ `en_curso`
- ✅ `finalizado` (en lugar de `completado` en checkin)

Los estados correctos son:
1. `pendiente` - Ticket creado
2. `aprobado` - Aprobado por encargado
3. `rechazado` - Rechazado
4. `en_curso` - Checkout realizado (vehículo salió)
5. `finalizado` - Checkin realizado (vehículo regresó)
6. `completado` - Usuario calificó el servicio

---

### 2. Tabla `vehicles`
**Campo corregido:**
- ❌ `vehicle_number` → ✅ `internal_code`

**Campos adicionales agregados:**
- ✅ `color` - Color del vehículo
- ✅ `vehicle_type` - Tipo: sedan, suv, pickup, van, camioneta

---

### 3. Tabla `driver_licenses`
**Campos corregidos:**
- ❌ `full_name` - No existe directamente
- ❌ `expiration_date` → ✅ `expiry_date`

**Solución para `full_name`:**
Se obtiene de dos formas:
1. De la relación `user.name` (si existe)
2. Del campo `conductor_name` del ticket (si no tiene user asociado)

**Campos correctos:**
- ✅ `license_number` - Número de licencia
- ✅ `license_type` - Tipo: A, B, C, D, E
- ✅ `expiry_date` - Fecha de expiración
- ✅ `full_name` - Obtenido de user o conductor_name

---

### 4. Tabla `checklists`
**Todos los campos validados:**
- ✅ `tipo_inspeccion` - 'salida' o 'entrada'
- ✅ `folio` - Tipo bigInteger (no string con guion)
- ✅ `kilometraje_inicial` - Decimal(20,2)
- ✅ `kilometraje_final` - Decimal(20,2)
- ✅ Todos los campos boolean existen

---

## 🔄 Cambios en el API

### Endpoint: POST /api/dispatcher/login
**Response actualizado:**
```json
{
    "id": 1,
    "folio": "2025-0001",
    "status": "aprobado",
    "destination": "Ciudad de México",
    "purpose": "Entrega de documentos",
    "passenger_count": 2,
    "additional_notes": "Notas adicionales",
    "vehicle": {
        "id": 1,
        "brand": "Toyota",
        "model": "Camry",
        "year": 2023,
        "plates": "ABC-123-XYZ",
        "internal_code": "GPT-001",
        "color": "Blanco",
        "vehicle_type": "sedan"
    },
    "conductor_license": {
        "id": 1,
        "license_number": "LIC123456",
        "license_type": "B",
        "expiry_date": "2026-12-31",
        "full_name": "Carlos Ruiz"
    }
}
```

### Endpoint: POST /api/dispatcher/checklist/checkout
**Comportamiento:**
- Cambia el status del ticket de `aprobado` → `en_curso`
- Registra `checkout_at` con timestamp actual

### Endpoint: POST /api/dispatcher/checklist/checkin
**Comportamiento:**
- Requiere que el ticket esté en `en_curso`
- Cambia el status del ticket a `finalizado`
- Registra `checkin_at` con timestamp actual
- NO registra `completed_at` (eso es cuando el usuario califica)

---

## 📋 Validaciones Actualizadas

### Checkout:
```php
// Debe estar en: 'aprobado' o 'en_curso'
if (!in_array($ticket->status, ['aprobado', 'en_curso'])) {
    // Error
}
```

### Checkin:
```php
// Debe estar en: 'en_curso'
if ($ticket->status !== 'en_curso') {
    // Error
}
```

---

## 🎯 Testing Recomendado

### 1. Probar Login:
```bash
POST http://192.168.100.62:8000/api/dispatcher/login
{
    "email": "despachador@ejemplo.com",
    "name": "Nombre Despachador"
}
```

### 2. Verificar campos del response:
- ✅ No debe tener `requisicion`
- ✅ No debe tener `cliente`
- ✅ Debe tener `passenger_count`
- ✅ Debe tener `additional_notes`
- ✅ Vehicle debe tener `internal_code` (no `vehicle_number`)
- ✅ conductor_license debe tener `expiry_date` (no `expiration_date`)
- ✅ conductor_license debe tener `license_type`

### 3. Probar flujo completo:
1. Login → Obtener tickets con status `aprobado`
2. Checkout → Status cambia a `en_curso`
3. Checkin → Status cambia a `finalizado`

---

## ⚠️ Importante

- El servidor debe reiniciarse después de estos cambios
- La caché debe limpiarse: `php artisan optimize:clear`
- Todos los campos ahora coinciden 100% con la base de datos
- No habrá más errores de "Column not found"

---

## 📝 Próximos Pasos

Si necesitas agregar más campos:
1. Verifica primero que existan en la migración
2. Agrega el campo en el `with()` del query
3. Mapea el campo en el response
4. Actualiza la documentación
