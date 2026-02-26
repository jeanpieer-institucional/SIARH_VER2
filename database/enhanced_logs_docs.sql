-- Actualizar tabla logs_actividad
ALTER TABLE logs_actividad
ADD COLUMN datos_anteriores JSON NULL AFTER descripcion,
ADD COLUMN datos_nuevos JSON NULL AFTER datos_anteriores;

-- Crear tabla para documentos de docente
CREATE TABLE IF NOT EXISTS documentos_docente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    docente_id INT NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    ruta_archivo VARCHAR(500) NOT NULL,
    tipo_documento VARCHAR(100) NOT NULL,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    subido_por INT NULL,
    FOREIGN KEY (docente_id) REFERENCES docentes(id) ON DELETE CASCADE,
    FOREIGN KEY (subido_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_docente_doc (docente_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
