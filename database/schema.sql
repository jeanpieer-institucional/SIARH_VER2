-- =====================================================
-- SIARH - Sistema Integral de Asistencia y RRHH
-- Base de Datos - Schema Completo
-- =====================================================

CREATE DATABASE IF NOT EXISTS siarh_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE siarh_db;

-- =====================================================
-- TABLA: Carreras/Departamentos
-- =====================================================
CREATE TABLE carreras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    codigo VARCHAR(20) UNIQUE NOT NULL,
    descripcion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLA: Usuarios del Sistema
-- =====================================================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'supervisor', 'docente', 'super_admin') NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    ultimo_acceso TIMESTAMP NULL,
    ip_registro VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_rol (rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLA: Docentes
-- =====================================================
CREATE TABLE docentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_empleado VARCHAR(20) UNIQUE NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    dni VARCHAR(8) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(15),
    carrera_id INT,
    usuario_id INT UNIQUE,
    huella_digital LONGBLOB,
    tiene_huella BOOLEAN DEFAULT FALSE,
    estado ENUM('activo', 'inactivo', 'licencia') DEFAULT 'activo',
    fecha_ingreso DATE,
    direccion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (carrera_id) REFERENCES carreras(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_dni (dni),
    INDEX idx_codigo (codigo_empleado),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLA: Asistencias
-- =====================================================
CREATE TABLE asistencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    docente_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora_entrada TIME,
    hora_salida TIME,
    estado ENUM('presente', 'tardanza', 'ausente', 'licencia') DEFAULT 'ausente',
    minutos_tardanza INT DEFAULT 0,
    tipo_registro ENUM('biometrico', 'manual') DEFAULT 'biometrico',
    ip_registro VARCHAR(45),
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (docente_id) REFERENCES docentes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_asistencia (docente_id, fecha),
    INDEX idx_fecha (fecha),
    INDEX idx_estado (estado),
    INDEX idx_docente_fecha (docente_id, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLA: Licencias y Permisos
-- =====================================================
CREATE TABLE licencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    docente_id INT NOT NULL,
    tipo ENUM('medica', 'personal', 'vacaciones', 'otros') NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    motivo TEXT NOT NULL,
    documento_adjunto VARCHAR(255),
    estado ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'pendiente',
    aprobado_por INT,
    fecha_aprobacion TIMESTAMP NULL,
    comentarios_aprobacion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (docente_id) REFERENCES docentes(id) ON DELETE CASCADE,
    FOREIGN KEY (aprobado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_docente (docente_id),
    INDEX idx_estado (estado),
    INDEX idx_fechas (fecha_inicio, fecha_fin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLA: Logs de Actividad
-- =====================================================
CREATE TABLE logs_actividad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    accion VARCHAR(100) NOT NULL,
    modulo VARCHAR(50) NOT NULL,
    descripcion TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_modulo (modulo),
    INDEX idx_fecha (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLA: Configuración del Sistema
-- =====================================================
CREATE TABLE configuracion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(50) UNIQUE NOT NULL,
    valor TEXT NOT NULL,
    descripcion TEXT,
    tipo ENUM('string', 'int', 'boolean', 'time', 'json') DEFAULT 'string',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLA: Notificaciones
-- =====================================================
CREATE TABLE notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo ENUM('info', 'warning', 'error', 'success') DEFAULT 'info',
    titulo VARCHAR(200) NOT NULL,
    mensaje TEXT NOT NULL,
    leida BOOLEAN DEFAULT FALSE,
    url_accion VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_leida (usuario_id, leida)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- INSERTAR DATOS INICIALES
-- =====================================================

-- Configuraciones por defecto
INSERT INTO configuracion (clave, valor, descripcion, tipo) VALUES
('hora_entrada', '08:00:00', 'Hora oficial de entrada', 'time'),
('hora_salida', '17:00:00', 'Hora oficial de salida', 'time'),
('tolerancia_minutos', '15', 'Minutos de tolerancia para tardanza', 'int'),
('nombre_institucion', 'Instituto Superior Tecnológico', 'Nombre de la institución', 'string'),
('version_sistema', '1.0.0', 'Versión del sistema', 'string'),
('backup_automatico', 'true', 'Activar backup automático', 'boolean'),
('dias_laborales', '["lunes","martes","miercoles","jueves","viernes"]', 'Días laborales', 'json');

-- Usuario administrador por defecto (password: admin123)
INSERT INTO usuarios (username, password, rol, email, ip_registro) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'admin@siarh.com', '127.0.0.1');

-- Carreras de ejemplo
INSERT INTO carreras (nombre, codigo, descripcion) VALUES
('Administración Industrial', 'ADM-IND', 'Carrera de Administración Industrial'),
('Enfermería Técnica', 'ENF-TEC', 'Carrera de Enfermería Técnica'),
('Mecánica Automotriz', 'MEC-AUTO', 'Carrera de Mecánica Automotriz'),
('Técnica en Farmacia', 'TEC-FARM', 'Carrera de Técnica en Farmacia'),
('Electrónica Industrial', 'ELEC-IND', 'Carrera de Electrónica Industrial');

-- =====================================================
-- VISTAS ÚTILES
-- =====================================================

-- Vista de asistencias con información del docente
CREATE VIEW v_asistencias_completas AS
SELECT 
    a.id,
    a.fecha,
    a.hora_entrada,
    a.hora_salida,
    a.estado,
    a.minutos_tardanza,
    a.tipo_registro,
    d.codigo_empleado,
    CONCAT(d.nombres, ' ', d.apellidos) as nombre_completo,
    d.dni,
    c.nombre as carrera,
    a.observaciones
FROM asistencias a
INNER JOIN docentes d ON a.docente_id = d.id
LEFT JOIN carreras c ON d.carrera_id = c.id;

-- Vista de licencias con información del docente
CREATE VIEW v_licencias_completas AS
SELECT 
    l.id,
    l.tipo,
    l.fecha_inicio,
    l.fecha_fin,
    l.estado,
    l.motivo,
    CONCAT(d.nombres, ' ', d.apellidos) as nombre_docente,
    d.codigo_empleado,
    c.nombre as carrera,
    CONCAT(u.username) as aprobado_por_username,
    l.fecha_aprobacion
FROM licencias l
INNER JOIN docentes d ON l.docente_id = d.id
LEFT JOIN carreras c ON d.carrera_id = c.id
LEFT JOIN usuarios u ON l.aprobado_por = u.id;

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS
-- =====================================================

DELIMITER //

-- Procedimiento para registrar asistencia
CREATE PROCEDURE sp_registrar_asistencia(
    IN p_docente_id INT,
    IN p_fecha DATE,
    IN p_hora TIME,
    IN p_tipo_registro VARCHAR(20),
    IN p_ip VARCHAR(45)
)
BEGIN
    DECLARE v_hora_entrada TIME;
    DECLARE v_tolerancia INT;
    DECLARE v_estado VARCHAR(20);
    DECLARE v_minutos_tardanza INT;
    
    -- Obtener configuración
    SELECT valor INTO v_hora_entrada FROM configuracion WHERE clave = 'hora_entrada';
    SELECT valor INTO v_tolerancia FROM configuracion WHERE clave = 'tolerancia_minutos';
    
    -- Calcular estado y tardanza
    SET v_minutos_tardanza = TIMESTAMPDIFF(MINUTE, v_hora_entrada, p_hora);
    
    IF v_minutos_tardanza <= 0 THEN
        SET v_estado = 'presente';
        SET v_minutos_tardanza = 0;
    ELSEIF v_minutos_tardanza <= v_tolerancia THEN
        SET v_estado = 'presente';
        SET v_minutos_tardanza = 0;
    ELSE
        SET v_estado = 'tardanza';
    END IF;
    
    -- Insertar o actualizar asistencia
    INSERT INTO asistencias (docente_id, fecha, hora_entrada, estado, minutos_tardanza, tipo_registro, ip_registro)
    VALUES (p_docente_id, p_fecha, p_hora, v_estado, v_minutos_tardanza, p_tipo_registro, p_ip)
    ON DUPLICATE KEY UPDATE 
        hora_salida = p_hora,
        updated_at = CURRENT_TIMESTAMP;
END //

DELIMITER ;
