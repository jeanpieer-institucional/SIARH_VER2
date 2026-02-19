-- =====================================================
-- SIARH - Actualización de Carreras
-- Script para actualizar y agregar nuevas carreras
-- =====================================================

USE siarh_db;

-- Actualizar carreras existentes
UPDATE carreras SET 
    nombre = 'Administración Industrial',
    codigo = 'ADM-IND',
    descripcion = 'Carrera de Administración Industrial'
WHERE codigo = 'ADMIN';

UPDATE carreras SET 
    nombre = 'Enfermería Técnica',
    codigo = 'ENF-TEC',
    descripcion = 'Carrera de Enfermería Técnica'
WHERE codigo = 'ENF';

-- Eliminar carreras que no se usarán
DELETE FROM carreras WHERE codigo IN ('COMP-INF', 'CONT');

-- Insertar nuevas carreras
INSERT INTO carreras (nombre, codigo, descripcion, estado) VALUES
('Mecánica Automotriz', 'MEC-AUTO', 'Carrera de Mecánica Automotriz', 'activo'),
('Técnica en Farmacia', 'TEC-FARM', 'Carrera de Técnica en Farmacia', 'activo'),
('Electrónica Industrial', 'ELEC-IND', 'Carrera de Electrónica Industrial', 'activo')
ON DUPLICATE KEY UPDATE 
    nombre = VALUES(nombre),
    descripcion = VALUES(descripcion);

-- Verificar las carreras actualizadas
SELECT * FROM carreras ORDER BY nombre;
