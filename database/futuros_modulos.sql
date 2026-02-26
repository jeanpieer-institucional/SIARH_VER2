-- Nuevas Tablas para Módulos Futuros (Horarios, Planillas, Evaluaciones)

CREATE TABLE IF NOT EXISTS horarios_docente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    docente_id INT NOT NULL,
    dia_semana ENUM('lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo') NOT NULL,
    hora_entrada TIME NOT NULL,
    hora_salida TIME NOT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (docente_id) REFERENCES docentes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS planillas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    docente_id INT NOT NULL,
    mes TINYINT NOT NULL,
    anio SMALLINT NOT NULL,
    sueldo_base DECIMAL(10,2) NOT NULL,
    descuentos DECIMAL(10,2) DEFAULT 0.00,
    bonificaciones DECIMAL(10,2) DEFAULT 0.00,
    total_pagar DECIMAL(10,2) NOT NULL,
    estado ENUM('generada', 'pagada', 'anulada') DEFAULT 'generada',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (docente_id) REFERENCES docentes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS evaluaciones_docente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    docente_id INT NOT NULL,
    evaluador_id INT NOT NULL,
    periodo VARCHAR(50) NOT NULL,
    puntuacion_metodologia INT NOT NULL CHECK(puntuacion_metodologia BETWEEN 1 AND 5),
    puntuacion_puntualidad INT NOT NULL CHECK(puntuacion_puntualidad BETWEEN 1 AND 5),
    puntuacion_relacion INT NOT NULL CHECK(puntuacion_relacion BETWEEN 1 AND 5),
    comentarios TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (docente_id) REFERENCES docentes(id) ON DELETE CASCADE,
    FOREIGN KEY (evaluador_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
