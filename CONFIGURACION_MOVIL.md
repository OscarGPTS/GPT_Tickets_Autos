# Configuración para Pruebas en Dispositivo Móvil

## 🌐 Tu IP Local
**192.168.100.62**

## 📱 URLs para tu App Móvil

### Base URL del API
```
http://192.168.100.62:8000/api/dispatcher
```

### Endpoints Completos:
- **🧪 Test (GET)**: `http://192.168.100.62:8000/api/dispatcher/test`
- **Login**: `http://192.168.100.62:8000/api/dispatcher/login`
- **Checkout**: `http://192.168.100.62:8000/api/dispatcher/checklist/checkout`
- **Checkin**: `http://192.168.100.62:8000/api/dispatcher/checklist/checkin`
- **Detalle Ticket**: `http://192.168.100.62:8000/api/dispatcher/ticket/{id}`

---

## ✅ Pasos para Conectar tu Dispositivo

### 1. Verificar que las rutas API estén registradas (YA CONFIGURADO)
Las rutas ya están configuradas en `bootstrap/app.php`. Puedes verificarlas con:
```powershell
php artisan route:list --path=api
```

### 2. Verificar que estés en la misma red WiFi
- Tu computadora y tu celular deben estar conectados a la **misma red WiFi**
- No funcionará si usas datos móviles en el celular

### 3. Verificar el Firewall de Windows (IMPORTANTE)
Si no puedes conectarte, el firewall puede estar bloqueando:

#### Opción A: Crear regla temporal (Recomendado para desarrollo)
```powershell
# Ejecutar en PowerShell como Administrador
New-NetFirewallRule -DisplayName "Laravel Dev Server" -Direction Inbound -LocalPort 8000 -Protocol TCP -Action Allow
```

#### Opción B: Desactivar temporalmente el firewall (Solo para pruebas)
1. Ir a **Panel de Control → Sistema y Seguridad → Firewall de Windows Defender**
2. Clic en "Activar o desactivar Firewall de Windows Defender"
3. Desactivar para red privada (solo mientras pruebas)
4. **¡No olvides reactivarlo después!**

### 4. Iniciar el Servidor Laravel
```powershell
php artisan serve --host=0.0.0.0 --port=8000
```

El servidor debe mostrar:
```
INFO  Server running on [http://0.0.0.0:8000].
```

### 5. Probar desde tu Navegador Móvil

#### Prueba 1: Página Principal
Abre el navegador de tu celular y visita:
```
http://192.168.100.62:8000
```
Deberías ver la página de inicio de tu aplicación Laravel.

#### Prueba 2: API Test (Recomendado)
Para verificar que el API funciona, visita:
```
http://192.168.100.62:8000/api/dispatcher/test
```
Deberías ver un JSON con información del API y todos los endpoints disponibles.

---

## 🧪 Probar el API desde el Móvil

### Usando Postman o Thunder Client en el móvil:

**Test de Login:**
```bash
POST http://192.168.100.62:8000/api/dispatcher/login
Content-Type: application/json

{
    "email": "tu_despachador@ejemplo.com",
    "name": "Nombre del Despachador"
}
```

---

## 🔧 Configuración en tu App Móvil

### Flutter (example)
```dart
class ApiConfig {
  // Para desarrollo local
  static const String baseUrl = 'http://192.168.100.62:8000/api';
  
  // Endpoints
  static const String login = '$baseUrl/dispatcher/login';
  static const String checkout = '$baseUrl/dispatcher/checklist/checkout';
  static const String checkin = '$baseUrl/dispatcher/checklist/checkin';
}
```

### React Native (example)
```javascript
const API_CONFIG = {
  // Para desarrollo local
  baseURL: 'http://192.168.100.62:8000/api',
  
  endpoints: {
    login: '/dispatcher/login',
    checkout: '/dispatcher/checklist/checkout',
    checkin: '/dispatcher/checklist/checkin',
  }
};
```

---

## 🚨 Troubleshooting

### Problema: No puedo conectarme desde el móvil
**Soluciones:**
1. Verifica que ambos dispositivos estén en la misma red WiFi
2. Verifica el firewall de Windows (ver paso 2)
3. Asegúrate de que el servidor Laravel está corriendo
4. Prueba hacer ping desde PowerShell:
   ```powershell
   Test-Connection 192.168.100.62
   ```

### Problema: "Connection refused" o "Network error"
**Soluciones:**
1. Verifica que usas `--host=0.0.0.0` al iniciar el servidor
2. Verifica que el puerto 8000 no esté ocupado:
   ```powershell
   netstat -ano | findstr :8000
   ```

### Problema: El API responde pero hay errores CORS
**Solución:** Agregar configuración CORS en Laravel (ya debería estar configurado)

### Problema: Mi IP cambió
**Solución:** Las IPs locales pueden cambiar. Si eso pasa:
1. Obtener nueva IP: `ipconfig`
2. Actualizar en `.env`: `APP_URL=http://TU_NUEVA_IP:8000`
3. Reiniciar servidor Laravel

---

## 📝 Notas Importantes

1. **Seguridad**: Esta configuración es SOLO para desarrollo local
2. **Producción**: En producción usa HTTPS y un dominio real
3. **IP Dinámica**: Tu IP local puede cambiar si reinicias el router
4. **Firewall**: Recuerda reactivarlo después de las pruebas
5. **Red Móvil**: No funcionará si el celular usa datos móviles

---

## 🌍 Alternativa: Usar ngrok (Si la red local no funciona)

Si tienes problemas con la red local, puedes usar ngrok para crear un túnel:

```powershell
# 1. Descargar ngrok de https://ngrok.com/download
# 2. Iniciar servidor Laravel normal
php artisan serve

# 3. En otra terminal, iniciar ngrok
ngrok http 8000
```

Ngrok te dará una URL pública como:
```
https://abc123.ngrok.io
```

Esta URL funcionará desde cualquier lugar, incluso con datos móviles.

---

## 📞 ¿Todo Listo?

Tu servidor está corriendo en:
- **Local**: http://localhost:8000
- **Red Local**: http://192.168.100.62:8000
- **API Base**: http://192.168.100.62:8000/api/dispatcher

¡Ahora puedes hacer pruebas desde tu app móvil! 🚀
