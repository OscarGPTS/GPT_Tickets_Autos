# API de Despachador - Documentación

## Base URL
```
http://tu-dominio.com/api/dispatcher
```

## Endpoints Disponibles

### 1. Login / Verificación de Usuario
Verifica el usuario por OAuth2 y retorna todos los tickets disponibles con sus checklists.

**Endpoint:** `POST /api/dispatcher/login`

**Request Body:**
```json
{
    "email": "despachador@empresa.com",
    "name": "Juan Pérez"
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Usuario autenticado correctamente",
    "data": {
        "user": {
            "id": 1,
            "name": "Juan Pérez",
            "email": "despachador@empresa.com",
            "phone": "1234567890",
            "department": "Logística",
            "avatar": "https://..."
        },
        "tickets": [
            {
                "id": 1,
                "folio": "2025-0001",
                "status": "aprobado",
                "destination": "Ciudad de México",
                "purpose": "Entrega de documentos",
                "passenger_count": 2,
                "additional_notes": "Llevar documentos importantes",
                "requested_date": "2025-12-15",
                "requested_time_start": "09:00",
                "requested_time_end": "17:00",
                "conductor_name": "Carlos Ruiz",
                "conductor_phone": "5551234567",
                "approved_at": "2025-12-12 10:30:00",
                "checkout_at": null,
                "checkin_at": null,
                "completed_at": null,
                "user": {
                    "id": 2,
                    "name": "María García",
                    "email": "maria@empresa.com",
                    "phone": "5559876543"
                },
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
                },
                "checkout_checklist": {
                    "id": null,
                    "exists": false,
                    "tipo_inspeccion": "salida",
                    "folio": "2025-0001",
                    "fecha": null,
                    "destino": "Ciudad de México",
                    "modelo": "Camry",
                    "placas": "ABC-123-XYZ",
                    "marca": "Toyota",
                    "hora_salida": null,
                    "hora_entrada": null,
                    "kilometraje_inicial": null,
                    "kilometraje_final": null,
                    "nivel_combustible_inicial": null,
                    "nivel_combustible_final": null,
                    "llantas": {
                        "llanta_delantera_derecha": null,
                        "llanta_delantera_izquierda": null,
                        "llanta_delantera_vida": null,
                        "llanta_trasera_derecha": null,
                        "llanta_trasera_izquierda": null,
                        "llanta_trasera_vida": null,
                        "llanta_refaccion": null,
                        "presion_adecuada": null
                    },
                    "frontal": {...},
                    "luces": {...},
                    "seguridad": {...},
                    "interior": {...},
                    "motor": {...},
                    "herramienta": {...},
                    "calcomanias": {...},
                    "observaciones": {...}
                },
                "checkin_checklist": {
                    "id": null,
                    "exists": false,
                    ...
                }
            }
        ]
    }
}
```

**Response Error (404):**
```json
{
    "success": false,
    "message": "Usuario no encontrado o inactivo"
}
```

**Response Error (403):**
```json
{
    "success": false,
    "message": "El usuario no tiene permisos de despachador"
}
```

---

### 2. Crear/Actualizar Checklist de Salida (Checkout)
Crea o actualiza el checklist de salida de un vehículo.

**Endpoint:** `POST /api/dispatcher/checklist/checkout`

**Request Body:**
```json
{
    "ticket_id": 1,
    "fecha": "2025-12-15",
    "hora_salida": "09:00",
    "kilometraje_inicial": 15000.50,
    "nivel_combustible_inicial": "3/4",
    "destino": "Ciudad de México",
    
    // Llantas
    "llanta_delantera_derecha": true,
    "llanta_delantera_izquierda": true,
    "llanta_delantera_vida": true,
    "llanta_trasera_derecha": true,
    "llanta_trasera_izquierda": true,
    "llanta_trasera_vida": true,
    "llanta_refaccion": true,
    "presion_adecuada": true,
    
    // Frontal
    "parabrisas": true,
    "cofre": true,
    "parrilla": true,
    "defensas": true,
    "molduras": true,
    "placa": true,
    "salpicadera": true,
    "antena": true,
    
    // Luces
    "intermitentes": true,
    "direccional_derecha": true,
    "direccional_izquierda": true,
    "luz_stop": true,
    "faros": true,
    "luces_altas": true,
    "luz_interior": true,
    "calaveras_buen_estado": true,
    
    // Seguridad
    "mata_chispas": true,
    "alarma": true,
    "extintor": true,
    "botiquin": true,
    "tarjeta_circulacion": true,
    "licencia_conducir_vigente": true,
    "poliza_seguro": true,
    "triangulo_emergencia": true,
    
    // Interior
    "tablero_indicadores": true,
    "switch_encendido": true,
    "controles_ac": true,
    "defroster": true,
    "radio": true,
    "volante": true,
    "bolsas_aire": true,
    "cinturon_seguridad": true,
    "coderas": true,
    "espejo_interior": true,
    "freno_mano": true,
    "encendedor": true,
    "guantera": true,
    "manijas_interiores": true,
    "seguros": true,
    "asientos": true,
    "tapetes_delanteros_traseros": true,
    
    // Motor
    "nivel_aceite_motor": true,
    "nivel_anticongelante": true,
    "nivel_liquido_frenos": true,
    "bateria": true,
    "bayoneta_aceite_motor": true,
    "tapones": true,
    "bocina_claxon": true,
    "radiador": true,
    
    // Herramienta
    "gato": true,
    "llave_ruedas": true,
    "cables_pasa_corriente": true,
    "caja_bolsa_herramientas": true,
    "dado_birlo_seguridad": true,
    
    // Calcomanías
    "calcomanias_permisos": true,
    "calcomania_velocidad_maxima": true,
    
    // Observaciones
    "mantenimiento_preventivo": "Sin observaciones",
    "mantenimiento_correctivo": "Sin observaciones",
    "condicion_carroceria_imagen": "url_imagen",
    "responsable_recibo_uso": "Carlos Ruiz",
    "responsable_entrega": "Juan Pérez"
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Checklist de salida creado correctamente",
    "data": {
        "checklist": {
            "id": 1,
            "ticket_id": 1,
            "tipo_inspeccion": "salida",
            ...
        },
        "ticket": {
            "id": 1,
            "folio": "2025-0001",
            "status": "en_progreso",
            "checkout_at": "2025-12-15 09:00:00",
            ...
        }
    }
}
```

**Response Error (400):**
```json
{
    "success": false,
    "message": "El ticket debe estar aprobado para crear un checklist"
}
```

---

### 3. Crear/Actualizar Checklist de Entrada (Checkin)
Crea o actualiza el checklist de entrada de un vehículo.

**Endpoint:** `POST /api/dispatcher/checklist/checkin`

**Request Body:**
```json
{
    "ticket_id": 1,
    "fecha": "2025-12-15",
    "hora_entrada": "17:30",
    "kilometraje_final": 15150.75,
    "nivel_combustible_final": "1/2",
    
    // Todos los campos del checklist igual que checkout
    "llanta_delantera_derecha": true,
    "llanta_delantera_izquierda": true,
    ...
    
    "responsable_recibo_uso": "Carlos Ruiz",
    "responsable_entrega": "Juan Pérez"
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Checklist de entrada creado correctamente",
    "data": {
        "checklist": {
            "id": 2,
            "ticket_id": 1,
            "tipo_inspeccion": "entrada",
            ...
        },
        "ticket": {
            "id": 1,
            "folio": "2025-0001",
            "status": "completado",
            "checkin_at": "2025-12-15 17:30:00",
            "completed_at": "2025-12-15 17:30:00",
            ...
        }
    }
}
```

**Response Error (400):**
```json
{
    "success": false,
    "message": "El ticket debe estar en progreso para crear un checklist de entrada"
}
```

**Response Error (400):**
```json
{
    "success": false,
    "message": "Debe existir un checklist de salida antes de crear uno de entrada"
}
```

---

### 4. Obtener Detalle de un Ticket
Obtiene la información completa de un ticket específico.

**Endpoint:** `GET /api/dispatcher/ticket/{id}`

**Response Success (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "folio": "2025-0001",
        "status": "en_progreso",
        ...
        "checkout_checklist": {...},
        "checkin_checklist": {...}
    }
}
```

**Response Error (404):**
```json
{
    "success": false,
    "message": "Ticket no encontrado"
}
```

---

## Flujo de Trabajo

1. **Login**: El usuario se autentica con OAuth2 en Google, la app móvil envía el email y nombre a `/login`
2. **Obtener Tickets**: El endpoint de login retorna todos los tickets asignados al despachador con sus checklists (vacíos si no existen)
3. **Checkout**: Cuando el vehículo sale, se crea/actualiza el checklist de salida en `/checklist/checkout`
4. **Checkin**: Cuando el vehículo regresa, se crea/actualiza el checklist de entrada en `/checklist/checkin`

## Estados del Ticket

- `pendiente`: Ticket recién creado
- `aprobado`: Ticket aprobado, listo para checkout
- `rechazado`: Ticket rechazado por encargado
- `en_curso`: Checkout realizado, esperando checkin
- `finalizado`: Checkin realizado, esperando calificación
- `completado`: Calificado por usuario (final)

## Notas Importantes

1. Los checklists se retornan vacíos (con `exists: false`) si no existen aún
2. Todos los campos boolean pueden enviarse como `true`/`false` o como strings `"true"`/`"false"`
3. El checkout cambia el estado del ticket a `en_curso`
4. El checkin cambia el estado del ticket a `finalizado`
5. No se puede crear un checkin sin haber creado primero un checkout
6. Los checklists están organizados en secciones para facilitar su uso en la app móvil
7. Los campos `requisicion` y `cliente` no existen en la tabla tickets
8. El campo `completed_at` solo se actualiza cuando el usuario califica el servicio (status=completado)

## Ejemplo de Uso en Flutter/React Native

```javascript
// 1. Login
const loginResponse = await fetch('http://tu-dominio.com/api/dispatcher/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        email: user.email,
        name: user.name
    })
});

const { data } = await loginResponse.json();
const tickets = data.tickets;

// 2. Crear checkout
const checkoutResponse = await fetch('http://tu-dominio.com/api/dispatcher/checklist/checkout', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        ticket_id: tickets[0].id,
        fecha: '2025-12-15',
        hora_salida: '09:00',
        kilometraje_inicial: 15000.50,
        nivel_combustible_inicial: '3/4',
        // ... más campos
    })
});

// 3. Crear checkin
const checkinResponse = await fetch('http://tu-dominio.com/api/dispatcher/checklist/checkin', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        ticket_id: tickets[0].id,
        fecha: '2025-12-15',
        hora_entrada: '17:30',
        kilometraje_final: 15150.75,
        nivel_combustible_final: '1/2',
        // ... más campos
    })
});
```

## Códigos de Estado HTTP

- `200`: Éxito
- `400`: Error de validación o lógica de negocio
- `403`: Sin permisos
- `404`: Recurso no encontrado
- `422`: Datos de validación incorrectos
- `500`: Error del servidor
