# 🔐 Guía de Configuración de Google OAuth2

## ⚠️ IMPORTANTE: Seguridad de Credenciales

**NUNCA** compartas tus credenciales de Google OAuth2 públicamente. Las credenciales deben estar únicamente en tu archivo `.env` local.

---

## 📋 Pasos para Configurar Google OAuth2

### 1. Obtener Credenciales de Google Cloud Console

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Selecciona o crea un proyecto
3. Ve a **APIs y servicios** → **Credenciales**
4. Haz clic en **+ CREAR CREDENCIALES** → **ID de cliente de OAuth**
5. Selecciona **Aplicación web**
6. Configura:
   - **Nombre**: SIGEV - GPT Services
   - **Orígenes de JavaScript autorizados**: 
     - `http://localhost:8000`
   - **URIs de redirección autorizadas**:
     - `http://localhost:8000/auth/google/callback`
7. Haz clic en **Crear**
8. **Copia tus credenciales** (Client ID y Client Secret)

### 2. Configurar el Archivo `.env`

Abre tu archivo `.env` (que está en `.gitignore` y NO se sube a Git) y configura:

```env
GOOGLE_CLIENT_ID=tu-client-id-aqui.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=tu-client-secret-aqui
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

### 3. Limpiar Caché de Laravel

```powershell
php artisan config:clear
php artisan cache:clear
```

### 4. Probar la Autenticación

```powershell
php artisan serve
```

Accede a `http://localhost:8000` y prueba el login con Google.

---

## 🚀 Para Producción

Cuando despliegues a producción:

### 1. Agregar Dominio de Producción en Google Cloud Console

En las URIs de redirección autorizadas, agrega:
```
https://tu-dominio.com/auth/google/callback
```

### 2. Actualizar `.env` de Producción

```env
APP_URL=https://tu-dominio.com
GOOGLE_REDIRECT_URI=https://tu-dominio.com/auth/google/callback
```

---

## 🔒 Mejores Prácticas de Seguridad

### ✅ Hacer:

1. **Mantener credenciales en `.env`** (ya está en `.gitignore`)
2. **Usar variables de entorno** en el código (`env()`)
3. **Regenerar credenciales** si se comprometen
4. **Usar HTTPS** en producción
5. **Limitar orígenes autorizados** a dominios específicos

### ❌ NO Hacer:

1. ❌ **NO** subir el archivo `.env` a Git
2. ❌ **NO** hardcodear credenciales en el código
3. ❌ **NO** compartir credenciales en chats, emails o documentación
4. ❌ **NO** usar las mismas credenciales para desarrollo y producción
5. ❌ **NO** agregar credenciales a archivos de configuración versionados

---

## 📁 Archivos Protegidos

El proyecto ya tiene configurado `.gitignore` para proteger:

```
.env
.env.backup
.env.production
```

Estos archivos **NUNCA** se subirán a Git.

---

## 🆘 Si Expusiste tus Credenciales Accidentalmente

1. **Ve inmediatamente a Google Cloud Console**
2. **Elimina las credenciales comprometidas**
3. **Crea nuevas credenciales**
4. **Actualiza tu `.env`** con las nuevas credenciales
5. **Si fue en un repositorio público**:
   - Cambia las credenciales
   - Considera hacer un historial limpio del repositorio

---

## ✅ Verificación de Seguridad

Ejecuta estos comandos para verificar que no hay credenciales expuestas:

```powershell
# Verificar que .env está en .gitignore
Get-Content .gitignore | Select-String "\.env"

# Verificar que no hay credenciales en archivos PHP
Get-ChildItem -Recurse -Include *.php | Select-String "GOCSPX|googleusercontent"

# Verificar que no hay credenciales en documentación
Get-ChildItem -Recurse -Include *.md | Select-String "GOCSPX|googleusercontent"
```

Si encuentras credenciales en archivos que no sean `.env`, **elimínalas inmediatamente**.

---

## 📞 Soporte

Si tienes dudas sobre la configuración de Google OAuth2, consulta la [documentación oficial](https://developers.google.com/identity/protocols/oauth2).

---

**Recuerda: La seguridad de tus credenciales es tu responsabilidad. Mantenlas siempre privadas.** 🔒
