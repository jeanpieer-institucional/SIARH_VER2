# SIARH - Sistema Integral de Asistencia y Recursos Humanos

Sistema completo de gestión de recursos humanos y control de asistencia para instituciones educativas.

## Características Principales

### 🔐 Seguridad

- Autenticación con bcrypt
- Control de roles (Admin, Supervisor, Docente)
- Prevención de SQL injection
- Logs de auditoría completos

### 👥 Gestión de Docentes

- CRUD completo
- Registro de huella digital
- Búsqueda avanzada
- Asignación a carreras/departamentos

### 📊 Control de Asistencia

- Registro manual y biométrico
- Cálculo automático de tardanzas
- Reportes diarios y mensuales
- Exportación a Excel

### 📋 Licencias y Permisos

- Flujo de aprobación
- Adjuntar documentos
- Notificaciones de vencimiento
- Historial completo

### 📈 Dashboard y Reportes

- Estadísticas en tiempo real
- Gráficos interactivos
- Alertas y notificaciones
- Métricas de puntualidad

## Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Apache con mod_rewrite
- Extensiones PHP: PDO, mbstring, fileinfo

## Instalación

1. **Clonar o copiar el proyecto**

   ```
   Copiar la carpeta siarh a c:\xampp\htdocs\
   ```

2. **Importar base de datos**
   - Abrir phpMyAdmin
   - Crear nueva base de datos: `siarh_db`
   - Importar: `database/schema.sql`

3. **Configurar conexión**
   - Editar `config/config.php`
   - Verificar credenciales de base de datos

4. **Crear directorios necesarios**

   ```
   mkdir uploads
   mkdir uploads/licencias
   mkdir logs
   mkdir reports
   mkdir temp
   ```

5. **Acceder al sistema**
   - URL: `http://localhost/siarh`
   - Usuario: `admin`
   - Contraseña: `admin123`

## Estructura del Proyecto

```
siarh/
├── app/
│   ├── controllers/     # Controladores MVC
│   ├── models/          # Modelos de datos
│   ├── views/           # Vistas PHP
│   ├── core/            # Clases base
│   └── helpers/         # Utilidades
├── config/              # Configuración
├── database/            # Scripts SQL
├── public/
│   └── assets/
│       ├── css/         # Estilos
│       └── js/          # JavaScript
├── uploads/             # Archivos subidos
├── logs/                # Logs del sistema
├── .htaccess            # Configuración Apache
└── index.php            # Punto de entrada
```

## Credenciales por Defecto

**Administrador:**

- Usuario: `admin`
- Contraseña: `admin123`

> ⚠️ **IMPORTANTE**: Cambiar la contraseña después del primer acceso

## Tecnologías Utilizadas

- **Backend**: PHP 7.4+ con arquitectura MVC
- **Base de Datos**: MySQL con PDO
- **Frontend**: HTML5, CSS3, JavaScript ES6
- **Librerías**: Chart.js para gráficos
- **Iconos**: Font Awesome 6
- **Fuentes**: Google Fonts (Inter)

## Funcionalidades Destacadas

### Dark Mode

El sistema incluye modo oscuro que se activa con el botón en la barra superior.

### Búsqueda en Tiempo Real

Búsqueda instantánea de docentes con filtros avanzados.

### Exportación de Datos

Exportación de reportes a Excel con un solo clic.

### Responsive Design

Interfaz completamente adaptable a dispositivos móviles.

## Seguridad

- Contraseñas hasheadas con bcrypt (cost 10)
- Sesiones seguras con tiempo de expiración
- Validación de datos en cliente y servidor
- Protección contra XSS y CSRF
- Logs de todas las acciones críticas

## Soporte

Para reportar problemas o solicitar nuevas funcionalidades, contactar al administrador del sistema.

## Licencia

© 2025 SIARH. Todos los derechos reservados.
