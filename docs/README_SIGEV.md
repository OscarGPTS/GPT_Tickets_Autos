# Sistema de Gestión Vehicular (SIGEV) - GPT Services

Sistema completo de gestión de requisiciones y control de vehículos para GPT Services, desarrollado con Laravel 12 y Tailwind CSS.

## 🚀 Características Principales

### Gestión de Requisiciones
- Solicitud de vehículos con flujo de aprobación
- Sistema de folios único (AAAA-NNNN)
- Asignación de vehículos y despachadores
- Checkout/Checkin con checklist detallado
- Calificación del servicio

### Control de Vehículos
- Inventario completo de vehículos
- Documentación y seguimiento
- Historial de mantenimiento
- Control de kilometraje y combustible

### Sistema de Notificaciones
- Notificaciones por email en cada etapa
- Alertas automáticas a encargados
- Copia a jefe inmediato

### Roles y Permisos
- **Usuario (Solicitante)**: Crear requisiciones y calificar servicio
- **Despachador**: Realizar checkout/checkin
- **Encargado**: Aprobar solicitudes, asignar recursos, administrar sistema

## 📋 Requisitos

- PHP 8.2 o superior
- Composer
- Node.js y NPM
- MySQL, PostgreSQL o SQLite
- Servidor web (Apache, Nginx, o PHP built-in)

## ⚙️ Instalación

### 1. Clonar o copiar el proyecto

```bash
cd c:\xampp\htdocs\GPT_Tickets_Autos
```

### 2. Instalar dependencias

```bash
composer install
npm install
```

### 3. Configurar variables de entorno

Copia el archivo `.env.example` a `.env` (si no existe) y configura:

```env
APP_NAME="GPT Services - SIGEV"
APP_URL=http://localhost:8000

# Base de datos (SQLite por defecto)
DB_CONNECTION=sqlite

# Configuración de correo
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-app-password
MAIL_FROM_ADDRESS="noreply@gptservices.com"

# Encargados (separados por coma)
ENCARGADOS_EMAILS="analilia@gptservices.com,josecarmen@gptservices.com"
CC_EMAILS="denisse@gptservices.com"

# Google OAuth2 (configura tus propias credenciales)
# Obtén las credenciales en: https://console.cloud.google.com/
GOOGLE_CLIENT_ID=tu-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=tu-client-secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

### 4. Crear base de datos

Si usas SQLite (recomendado para desarrollo):
```bash
New-Item database\database.sqlite
```

Si usas MySQL, crea la base de datos manualmente.

### 5. Ejecutar migraciones y seeders

```bash
php artisan migrate --seed
```

Esto creará:
- ✅ Todas las tablas necesarias
- ✅ Roles y permisos
- ✅ 5 usuarios de prueba
- ✅ 5 vehículos de ejemplo

### 6. Compilar assets

```bash
npm run build
```

Para desarrollo con hot-reload:
```bash
npm run dev
```

### 7. Iniciar servidor

```bash
php artisan serve
```

Accede a: http://localhost:8000

## 👥 Usuarios de Prueba

| Email | Rol | Password |
|-------|-----|----------|
| analilia@gptservices.com | Encargada | password123 |
| josecarmen@gptservices.com | Encargado | password123 |
| despachador1@gptservices.com | Despachador | password123 |
| usuario1@gptservices.com | Usuario | password123 |
| denisse@gptservices.com | Usuario | password123 |

## 📊 Estructura de Base de Datos

### Tablas Principales

- `users` - Usuarios del sistema
- `roles` - Roles (usuario, despachador, encargado)
- `permissions` - Permisos específicos
- `tickets` - Requisiciones de vehículos
- `vehicles` - Inventario de vehículos
- `checklists` - Inspecciones de entrada/salida
- `vehicle_documents` - Documentación de vehículos
- `vehicle_maintenance` - Historial de mantenimiento
- `driver_licenses` - Licencias de conducir
- `notifications` - Notificaciones del sistema

## 🔄 Flujo de Trabajo

### 1. Solicitud
Usuario crea requisición con destino, fecha, motivo, etc.

### 2. Notificación
Sistema notifica automáticamente a:
- Ana Lilia (analilia@gptservices.com)
- José Carmen (josecarmen@gptservices.com)
- CC: Denisse (denisse@gptservices.com)
- Jefe inmediato del solicitante

### 3. Aprobación
Encargado revisa y:
- **Aprueba**: Asigna vehículo y despachador → Genera folio
- **Rechaza**: Indica motivo → Notifica al solicitante

### 4. Checkout
Despachador realiza inspección de salida:
- Registra kilometraje inicial
- Nivel de combustible
- Checklist completo (70+ ítems)
- Condición de carrocería

### 5. Uso del Vehículo
Estado cambia a "En Curso"

### 6. Checkin
Despachador realiza inspección de entrada:
- Registra kilometraje final
- Nivel de combustible final
- Verifica checklist
- Documenta daños o problemas

### 7. Calificación
Usuario califica:
- Servicio (1-5 estrellas)
- Vehículo (1-5 estrellas)
- Comentarios opcionales

## 🎨 Características UX/UI

- ✅ Diseño responsive con Tailwind CSS
- ✅ Navegación intuitiva según rol
- ✅ Dashboard personalizado por rol
- ✅ Feedback visual inmediato
- ✅ Notificaciones en tiempo real
- ✅ Estados de ticket con colores
- ✅ Validación de formularios

## 🔒 Seguridad

- ✅ Autenticación con Auth0 (Google OAuth)
- ✅ Sistema de roles y permisos
- ✅ Políticas de autorización
- ✅ Protección CSRF
- ✅ Validación de datos
- ✅ Middleware de autenticación

## 📧 Configuración de Correo

### Gmail
1. Habilita verificación en 2 pasos
2. Genera una "App Password"
3. Configura en `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-app-password
MAIL_ENCRYPTION=tls
```

## 🔧 Comandos Útiles

```bash
# Limpiar caché
php artisan optimize:clear

# Regenerar autoload
composer dump-autoload

# Ver rutas
php artisan route:list

# Crear nueva migración
php artisan make:migration nombre_migracion

# Crear nuevo controlador
php artisan make:controller NombreController

# Ejecutar tests
php artisan test
```

## 📦 Dependencias Principales

- **Laravel 12**: Framework PHP
- **Tailwind CSS**: Estilos
- **Auth0**: Autenticación OAuth
- **SQLite/MySQL**: Base de datos
- **Laravel Notifications**: Sistema de notificaciones
- **Laravel Mail**: Envío de correos

## 🏗️ Arquitectura

El proyecto sigue el patrón MVC de Laravel con:

- **Modelos**: Lógica de negocio y relaciones
- **Controladores**: Manejo de requests (Single Responsibility)
- **Vistas**: Blade templates con Tailwind CSS
- **Políticas**: Autorización granular
- **Notificaciones**: Sistema de alertas
- **Middleware**: Protección de rutas

## 🐛 Troubleshooting

### Error de permisos
```bash
chmod -R 775 storage bootstrap/cache
```

### Error de SQLite
```bash
touch database/database.sqlite
php artisan migrate:fresh --seed
```

### Error de Vite
```bash
npm install
npm run build
```

## 📝 Notas de Desarrollo

- Las migraciones están numeradas para ejecución ordenada
- Los seeders incluyen datos de prueba realistas
- Todos los modelos tienen relaciones Eloquent definidas
- Las validaciones están centralizadas en los controladores
- El sistema de notificaciones usa colas (configurable)

## 🚀 Próximos Pasos

1. Configurar Auth0 en producción
2. Agregar módulo de reportes
3. Implementar dashboard de encargado completo
4. Agregar gestión de usuarios
5. Implementar carga de documentos (PDF, imágenes)
6. Agregar gráficas y estadísticas
7. Implementar sistema de alertas (licencias, documentos vencidos)

## 📞 Soporte

Para dudas o problemas, contactar al equipo de desarrollo.

---

**Desarrollado con ❤️ para GPT Services**
