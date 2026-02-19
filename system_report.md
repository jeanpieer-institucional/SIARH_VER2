# Reporte Completo del Sistema SIARH

Este documento detalla las funcionalidades actuales del sistema, la matriz de permisos por usuario y sugerencias para futuras implementaciones.

## 1. Matriz de Permisos del Sistema

El sistema implementa un control de acceso basado en roles (RBAC) con 4 niveles jerárquicos:

1.  **Super Admin (`super_admin`)**: Acceso total ("God Mode") a todas las funcionalidades.
2.  **Administrador (`admin`)**: Gestión completa del sistema excepto la gestión de otros administradores.
3.  **Supervisor (`supervisor`)**: Gestión operativa (docentes, asistencias, licencias) sin acceso a configuración crítica.
4.  **Docente (`docente`)**: Acceso de solo lectura a su propia información y solicitud de licencias.

### Tabla Detallada de Permisos

| Módulo / Función  | Acción Específica               | Super Admin | Admin | Supervisor |    Docente    |
| :---------------- | :------------------------------ | :---------: | :---: | :--------: | :-----------: |
| **Autenticación** | Login / Logout                  |     ✅      |  ✅   |     ✅     |      ✅       |
|                   | Ver Perfil Propio               |     ✅      |  ✅   |     ✅     |      ✅       |
|                   | Cambiar Contraseña Propia       |     ✅      |  ✅   |     ✅     |      ✅       |
| **Usuarios**      | Ver Lista de Usuarios           |     ✅      |  ❌   |     ❌     |      ❌       |
|                   | Crear / Editar Usuarios         |     ✅      |  ❌   |     ❌     |      ❌       |
|                   | Eliminar Usuarios               |     ✅      |  ❌   |     ❌     |      ❌       |
| **Configuración** | Ver Ajustes del Sistema         |     ✅      |  ✅   |     ❌     |      ❌       |
|                   | Modificar Ajustes               |     ✅      |  ✅   |     ❌     |      ❌       |
| **Docentes**      | Ver Lista / Buscar              |     ✅      |  ✅   |     ✅     |      ❌       |
|                   | Registrar (Crear) Docente       |     ✅      |  ✅   |     ✅     |      ❌       |
|                   | Editar Docente                  |     ✅      |  ✅   |     ✅     |      ❌       |
|                   | Eliminar Docente                |     ✅      |  ✅   |     ❌     |      ❌       |
|                   | Registrar Huella Digital        |     ✅      |  ✅   |     ✅     |      ❌       |
| **Asistencias**   | Ver Dashboard de Asistencia     |     ✅      |  ✅   |     ✅     |  ✅ (Propio)  |
|                   | Registrar Asistencia Manual     |     ✅      |  ✅   |     ✅     |      ❌       |
|                   | Registrar Asistencia Biométrica |     ✅      |  ✅   |     ✅     |   ✅ (Auto)   |
|                   | Exportar Reporte Excel          |     ✅      |  ✅   |     ✅     |      ❌       |
| **Licencias**     | Ver Listado de Licencias        |     ✅      |  ✅   |     ✅     | ✅ (Propias)  |
|                   | Solicitar (Crear) Licencia      |     ✅      |  ✅   |     ✅     |      ✅       |
|                   | Aprobar Licencia                |     ✅      |  ✅   |     ✅     |      ❌       |
|                   | Rechazar Licencia               |     ✅      |  ✅   |     ✅     |      ❌       |
| **Reportes**      | Ver Reportes Estadísticos       |     ✅      |  ✅   |     ✅     | ✅ (Limitado) |
|                   | Exportar Reporte Docentes       |     ✅      |  ✅   |     ❌     |      ❌       |
|                   | Exportar Reporte Asistencias    |     ✅      |  ✅   |     ❌     |      ❌       |

## 2. Sugerencias de Funcionalidades Faltantes

Basado en el análisis del código y buenas prácticas para sistemas de Recursos Humanos y Asistencia, se sugieren las siguientes mejoras:

### A. Funcionalidades Críticas Pendientes

1.  **Gestión de Horarios Personalizados**:
    - _Actual_: El sistema usa una hora de entrada única global (`configuracion` -> `hora_entrada`).
    - _Faltante_: Permitir asignar horarios específicos por docente o por carrera (Turno Mañana/Tarde/Noche).

2.  **Justificación de Tardanzas**:
    - _Actual_: Las tardanzas se registran automáticamente.
    - _Faltante_: Un flujo para que el docente justifique una tardanza y el supervisor la "perdone" o valide.

3.  **Recuperación de Contraseña**:
    - _Actual_: No existe mecanismo de "Olvidé mi contraseña". Solo el admin puede resetearla.
    - _Faltante_: Implementar envío de correo con token de recuperación.

### B. Mejoras de Seguridad y Auditoría

4.  **Bloqueo de Cuenta**:
    - _Actual_: `MAX_LOGIN_ATTEMPTS` está definido en config pero no implementado en `AuthController`.
    - _Faltante_: Bloquear temporalmente al usuario después de X intentos fallidos.

5.  **Historial de Cambios (Audit Trail)**:
    - _Actual_: Existe `logs_actividad`, pero es básico.
    - _Faltante_: Guardar el "antes" y "después" de los datos modificados (ej. alguien cambió la hora de entrada de un registro).

### C. Mejoras Operativas

6.  **Calendario Académico / Feriados**:
    - _Actual_: No hay gestión de días no laborables.
    - _Faltante_: Tabla de feriados para que el sistema no marque "Falta" en días festivos.

7.  **Notificaciones en Tiempo Real**:
    - _Actual_: Tabla de notificaciones existe pero no hay sistema de envío/lectura en tiempo real (AJAX polling o WebSockets).
    - _Faltante_: Alerta visual cuando llega una solicitud de licencia nueva.

8.  **Gestión de Archivos Mejorada**:
    - _Actual_: Las licencias adjuntan archivos, pero no hay interfaz para gestionarlos o limpiarlos.
    - _Faltante_: Módulo de gestión documental para carpetas de docentes.

## 3. Estado Técnico Actual

- **Framework**: MVC Personalizado (PHP Puro).
- **Base de Datos**: MySQL/MariaDB con PDO.
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla).
- **Seguridad**:
  - CSRF Protection: ✅ Implementado (`Csrf.php`)
  - Password Hashing: ✅ Implementado (BCrypt)
  - SQL Injection: ✅ Protegido (PDO Prepared Statements)
  - XSS: ⚠️ Parcial (Depende de cómo se rendericen las vistas, se recomienda usar `htmlspecialchars` siempre).

---

_Reporte generado automáticamente por SIARH Assistant_
