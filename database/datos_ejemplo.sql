-- =====================================================
-- SIARH - Datos de Ejemplo para Demostración
-- Script para poblar la base de datos con datos realistas
-- =====================================================

USE siarh_db;

-- =====================================================
-- USUARIOS ADICIONALES
-- =====================================================
-- Contraseña para todos: "password123"
INSERT INTO usuarios (username, password, rol, email, estado, ip_registro) VALUES
('supervisor1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'supervisor', 'supervisor@siarh.com', 'activo', '127.0.0.1'),
('docente1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'docente', 'docente1@siarh.com', 'activo', '127.0.0.1'),
('docente2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'docente', 'docente2@siarh.com', 'activo', '127.0.0.1');

-- =====================================================
-- DOCENTES (20 docentes de ejemplo)
-- =====================================================
INSERT INTO docentes (codigo_empleado, nombres, apellidos, dni, email, telefono, carrera_id, usuario_id, tiene_huella, estado, fecha_ingreso, direccion) VALUES
-- Administración Industrial
('DOC202500001', 'María Elena', 'García Rodríguez', '12345678', 'maria.garcia@instituto.edu.pe', '987654321', 1, 3, TRUE, 'activo', '2023-03-15', 'Av. Los Pinos 123, Lima'),
('DOC202500002', 'Carlos Alberto', 'Mendoza Silva', '23456789', 'carlos.mendoza@instituto.edu.pe', '987654322', 1, NULL, TRUE, 'activo', '2023-04-20', 'Jr. Las Flores 456, Lima'),
('DOC202500003', 'Ana Lucía', 'Torres Vega', '34567890', 'ana.torres@instituto.edu.pe', '987654323', 1, NULL, FALSE, 'activo', '2024-01-10', 'Av. Universitaria 789, Lima'),
('DOC202500004', 'Roberto', 'Sánchez Pérez', '45678901', 'roberto.sanchez@instituto.edu.pe', '987654324', 1, NULL, TRUE, 'activo', '2023-08-05', 'Calle Los Olivos 321, Lima'),

-- Enfermería Técnica
('DOC202500005', 'Patricia', 'Ramírez Castro', '56789012', 'patricia.ramirez@instituto.edu.pe', '987654325', 2, 4, TRUE, 'activo', '2023-02-14', 'Av. Salud 234, Lima'),
('DOC202500006', 'Luis Fernando', 'Díaz Morales', '67890123', 'luis.diaz@instituto.edu.pe', '987654326', 2, NULL, TRUE, 'activo', '2023-05-18', 'Jr. Medicina 567, Lima'),
('DOC202500007', 'Carmen Rosa', 'Flores Gutiérrez', '78901234', 'carmen.flores@instituto.edu.pe', '987654327', 2, NULL, TRUE, 'activo', '2023-09-22', 'Av. Enfermería 890, Lima'),
('DOC202500008', 'Jorge Luis', 'Vargas Rojas', '89012345', 'jorge.vargas@instituto.edu.pe', '987654328', 2, NULL, FALSE, 'activo', '2024-02-01', 'Calle Salud 123, Lima'),

-- Mecánica Automotriz
('DOC202500009', 'Miguel Ángel', 'Herrera López', '90123456', 'miguel.herrera@instituto.edu.pe', '987654329', 3, NULL, TRUE, 'activo', '2023-03-10', 'Av. Industrial 456, Lima'),
('DOC202500010', 'Rosa María', 'Castro Núñez', '01234567', 'rosa.castro@instituto.edu.pe', '987654330', 3, NULL, TRUE, 'activo', '2023-06-15', 'Jr. Mecánica 789, Lima'),
('DOC202500011', 'Pedro José', 'Quispe Mamani', '11234567', 'pedro.quispe@instituto.edu.pe', '987654331', 3, NULL, TRUE, 'activo', '2023-07-20', 'Av. Automotriz 012, Lima'),
('DOC202500012', 'Sandra', 'Paredes Ríos', '21234567', 'sandra.paredes@instituto.edu.pe', '987654332', 3, NULL, FALSE, 'licencia', '2024-01-05', 'Calle Taller 345, Lima'),

-- Técnica en Farmacia
('DOC202500013', 'Fernando', 'Huamán Chávez', '31234567', 'fernando.huaman@instituto.edu.pe', '987654333', 4, NULL, TRUE, 'activo', '2023-04-12', 'Av. Farmacia 678, Lima'),
('DOC202500014', 'Gabriela', 'Ramos Soto', '41234567', 'gabriela.ramos@instituto.edu.pe', '987654334', 4, NULL, TRUE, 'activo', '2023-08-25', 'Jr. Medicamentos 901, Lima'),
('DOC202500015', 'Ricardo', 'Campos Vera', '51234567', 'ricardo.campos@instituto.edu.pe', '987654335', 4, NULL, TRUE, 'activo', '2023-11-30', 'Av. Salud 234, Lima'),

-- Electrónica Industrial
('DOC202500016', 'Diana', 'Moreno Aguilar', '61234567', 'diana.moreno@instituto.edu.pe', '987654336', 5, NULL, TRUE, 'activo', '2023-02-28', 'Calle Electrónica 567, Lima'),
('DOC202500017', 'Andrés', 'Salazar Ortiz', '71234567', 'andres.salazar@instituto.edu.pe', '987654337', 5, NULL, TRUE, 'activo', '2023-05-10', 'Av. Tecnología 890, Lima'),
('DOC202500018', 'Mónica', 'Vásquez Ruiz', '81234567', 'monica.vasquez@instituto.edu.pe', '987654338', 5, NULL, FALSE, 'activo', '2024-03-01', 'Jr. Circuitos 123, Lima'),
('DOC202500019', 'Javier', 'Ponce Delgado', '91234567', 'javier.ponce@instituto.edu.pe', '987654339', 5, NULL, TRUE, 'activo', '2023-09-15', 'Av. Industrial 456, Lima'),
('DOC202500020', 'Lucía', 'Navarro Cruz', '10234567', 'lucia.navarro@instituto.edu.pe', '987654340', 5, NULL, TRUE, 'activo', '2023-12-01', 'Calle Electrónica 789, Lima');

-- =====================================================
-- ASISTENCIAS (Últimos 7 días)
-- =====================================================

-- Día 1 (hace 6 días)
INSERT INTO asistencias (docente_id, fecha, hora_entrada, hora_salida, estado, minutos_tardanza, tipo_registro, ip_registro) VALUES
(1, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '07:55:00', '17:05:00', 'presente', 0, 'biometrico', '192.168.1.100'),
(2, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '08:10:00', '17:00:00', 'presente', 0, 'biometrico', '192.168.1.101'),
(3, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '08:20:00', '17:10:00', 'tardanza', 20, 'biometrico', '192.168.1.102'),
(4, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '08:05:00', '17:00:00', 'presente', 0, 'biometrico', '192.168.1.103'),
(5, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '07:58:00', '17:02:00', 'presente', 0, 'biometrico', '192.168.1.104'),
(6, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '08:15:00', '17:15:00', 'tardanza', 15, 'biometrico', '192.168.1.105'),
(7, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '08:00:00', '17:00:00', 'presente', 0, 'biometrico', '192.168.1.106'),
(9, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '08:08:00', '17:05:00', 'presente', 0, 'biometrico', '192.168.1.108'),
(10, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '08:25:00', '17:20:00', 'tardanza', 25, 'manual', '192.168.1.109'),
(11, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '07:50:00', '17:00:00', 'presente', 0, 'biometrico', '192.168.1.110'),
(13, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '08:12:00', '17:08:00', 'presente', 0, 'biometrico', '192.168.1.112'),
(14, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '08:05:00', '17:00:00', 'presente', 0, 'biometrico', '192.168.1.113'),
(15, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '08:18:00', '17:12:00', 'tardanza', 18, 'biometrico', '192.168.1.114'),
(16, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '08:02:00', '17:05:00', 'presente', 0, 'biometrico', '192.168.1.115'),
(17, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '08:00:00', '17:00:00', 'presente', 0, 'biometrico', '192.168.1.116'),
(19, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '08:07:00', '17:03:00', 'presente', 0, 'biometrico', '192.168.1.118'),
(20, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '08:22:00', '17:18:00', 'tardanza', 22, 'biometrico', '192.168.1.119');

-- Día 2 (hace 5 días)
INSERT INTO asistencias (docente_id, fecha, hora_entrada, hora_salida, estado, minutos_tardanza, tipo_registro, ip_registro) VALUES
(1, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '08:00:00', '17:00:00', 'presente', 0, 'biometrico', '192.168.1.100'),
(2, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '08:05:00', '17:05:00', 'presente', 0, 'biometrico', '192.168.1.101'),
(3, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '08:30:00', '17:15:00', 'tardanza', 30, 'biometrico', '192.168.1.102'),
(4, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '08:10:00', '17:10:00', 'presente', 0, 'biometrico', '192.168.1.103'),
(5, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '08:02:00', '17:00:00', 'presente', 0, 'biometrico', '192.168.1.104'),
(6, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '08:00:00', '17:00:00', 'presente', 0, 'biometrico', '192.168.1.105'),
(7, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '08:08:00', '17:05:00', 'presente', 0, 'biometrico', '192.168.1.106'),
(9, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '08:12:00', '17:10:00', 'presente', 0, 'biometrico', '192.168.1.108'),
(10, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '08:20:00', '17:15:00', 'tardanza', 20, 'biometrico', '192.168.1.109'),
(11, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '07:55:00', '17:00:00', 'presente', 0, 'biometrico', '192.168.1.110'),
(13, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '08:05:00', '17:02:00', 'presente', 0, 'biometrico', '192.168.1.112'),
(14, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '08:00:00', '17:00:00', 'presente', 0, 'biometrico', '192.168.1.113'),
(15, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '08:15:00', '17:08:00', 'tardanza', 15, 'biometrico', '192.168.1.114'),
(16, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '08:03:00', '17:05:00', 'presente', 0, 'biometrico', '192.168.1.115'),
(17, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '08:07:00', '17:03:00', 'presente', 0, 'biometrico', '192.168.1.116'),
(19, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '08:00:00', '17:00:00', 'presente', 0, 'biometrico', '192.168.1.118'),
(20, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '08:25:00', '17:20:00', 'tardanza', 25, 'biometrico', '192.168.1.119');

-- Día 3 (hace 4 días)
INSERT INTO asistencias (docente_id, fecha, hora_entrada, hora_salida, estado, minutos_tardanza, tipo_registro, ip_registro) VALUES
(1, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '07:58:00', '17:02:00', 'presente', 0, 'biometrico', '192.168.1.100'),
(2, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:00:00', '17:00:00', 'presente', 0, 'biometrico', '192.168.1.101'),
(3, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:10:00', '17:05:00', 'presente', 0, 'biometrico', '192.168.1.102'),
(4, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:15:00', '17:10:00', 'tardanza', 15, 'biometrico', '192.168.1.103'),
(5, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:05:00', '17:00:00', 'presente', 0, 'biometrico', '192.168.1.104'),
(6, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:08:00', '17:05:00', 'presente', 0, 'biometrico', '192.168.1.105'),
(7, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:02:00', '17:00:00', 'presente', 0, 'biometrico', '192.168.1.106'),
(9, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:00:00', '17:00:00', 'presente', 0, 'biometrico', '192.168.1.108'),
(10, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:18:00', '17:12:00', 'tardanza', 18, 'biometrico', '192.168.1.109'),
(11, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '07:52:00', '17:00:00', 'presente', 0, 'biometrico', '192.168.1.110'),
(13, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:10:00', '17:08:00', 'presente', 0, 'biometrico', '192.168.1.112'),
(14, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:05:00', '17:00:00', 'presente', 0, 'biometrico', '192.168.1.113'),
(15, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:00:00', '17:00:00', 'presente', 0, 'biometrico', '192.168.1.114'),
(16, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:12:00', '17:10:00', 'presente', 0, 'biometrico', '192.168.1.115'),
(17, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:03:00', '17:02:00', 'presente', 0, 'biometrico', '192.168.1.116'),
(19, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:20:00', '17:15:00', 'tardanza', 20, 'biometrico', '192.168.1.118'),
(20, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:00:00', '17:00:00', 'presente', 0, 'biometrico', '192.168.1.119');

-- Hoy (asistencias actuales)
INSERT INTO asistencias (docente_id, fecha, hora_entrada, estado, minutos_tardanza, tipo_registro, ip_registro) VALUES
(1, CURDATE(), '07:55:00', 'presente', 0, 'biometrico', '192.168.1.100'),
(2, CURDATE(), '08:00:00', 'presente', 0, 'biometrico', '192.168.1.101'),
(3, CURDATE(), '08:25:00', 'tardanza', 25, 'biometrico', '192.168.1.102'),
(4, CURDATE(), '08:10:00', 'presente', 0, 'biometrico', '192.168.1.103'),
(5, CURDATE(), '08:02:00', 'presente', 0, 'biometrico', '192.168.1.104'),
(6, CURDATE(), '08:00:00', 'presente', 0, 'biometrico', '192.168.1.105'),
(7, CURDATE(), '08:05:00', 'presente', 0, 'biometrico', '192.168.1.106'),
(9, CURDATE(), '08:15:00', 'tardanza', 15, 'biometrico', '192.168.1.108'),
(10, CURDATE(), '08:30:00', 'tardanza', 30, 'manual', '192.168.1.109'),
(11, CURDATE(), '07:50:00', 'presente', 0, 'biometrico', '192.168.1.110'),
(13, CURDATE(), '08:08:00', 'presente', 0, 'biometrico', '192.168.1.112'),
(14, CURDATE(), '08:00:00', 'presente', 0, 'biometrico', '192.168.1.113'),
(15, CURDATE(), '08:20:00', 'tardanza', 20, 'biometrico', '192.168.1.114'),
(16, CURDATE(), '08:03:00', 'presente', 0, 'biometrico', '192.168.1.115'),
(17, CURDATE(), '08:00:00', 'presente', 0, 'biometrico', '192.168.1.116'),
(19, CURDATE(), '08:12:00', 'presente', 0, 'biometrico', '192.168.1.118'),
(20, CURDATE(), '08:00:00', 'presente', 0, 'biometrico', '192.168.1.119');

-- =====================================================
-- LICENCIAS
-- =====================================================
INSERT INTO licencias (docente_id, tipo, fecha_inicio, fecha_fin, motivo, estado, aprobado_por, fecha_aprobacion) VALUES
-- Licencias aprobadas
(12, 'medica', DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'Reposo médico por cirugía menor', 'aprobado', 1, DATE_SUB(CURDATE(), INTERVAL 6 DAY)),
(8, 'personal', DATE_SUB(CURDATE(), INTERVAL 10 DAY), DATE_SUB(CURDATE(), INTERVAL 8 DAY), 'Trámites personales urgentes', 'aprobado', 1, DATE_SUB(CURDATE(), INTERVAL 11 DAY)),

-- Licencias pendientes
(18, 'vacaciones', DATE_ADD(CURDATE(), INTERVAL 15 DAY), DATE_ADD(CURDATE(), INTERVAL 22 DAY), 'Vacaciones programadas', 'pendiente', NULL, NULL),
(3, 'medica', DATE_ADD(CURDATE(), INTERVAL 3 DAY), DATE_ADD(CURDATE(), INTERVAL 5 DAY), 'Cita médica especializada', 'pendiente', NULL, NULL),

-- Licencia rechazada
(10, 'personal', DATE_SUB(CURDATE(), INTERVAL 15 DAY), DATE_SUB(CURDATE(), INTERVAL 14 DAY), 'Asunto personal', 'rechazado', 1, DATE_SUB(CURDATE(), INTERVAL 16 DAY));

-- =====================================================
-- LOGS DE ACTIVIDAD
-- =====================================================
INSERT INTO logs_actividad (usuario_id, accion, modulo, descripcion, ip_address, created_at) VALUES
(1, 'login', 'autenticacion', 'Usuario admin inició sesión', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(1, 'crear_docente', 'docentes', 'Docente creado: María Elena García Rodríguez', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(1, 'aprobar_licencia', 'licencias', 'Licencia aprobada para: Sandra Paredes Ríos', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 30 MINUTE)),
(2, 'login', 'autenticacion', 'Usuario supervisor1 inició sesión', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 15 MINUTE)),
(3, 'login', 'autenticacion', 'Usuario docente1 inició sesión', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 10 MINUTE)),
(1, 'registrar_asistencia', 'asistencias', 'Asistencia registrada para: Miguel Ángel Herrera López', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 5 MINUTE));

-- =====================================================
-- NOTIFICACIONES
-- =====================================================
INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, leida, created_at) VALUES
(1, 'warning', 'Docentes sin huella', '3 docentes no tienen huella digital registrada', FALSE, NOW()),
(1, 'info', 'Licencias pendientes', 'Hay 2 solicitudes de licencia pendientes de aprobación', FALSE, NOW()),
(1, 'warning', 'Licencia por vencer', 'La licencia de Sandra Paredes Ríos vence en 2 días', FALSE, NOW()),
(2, 'success', 'Bienvenido', 'Has iniciado sesión correctamente', TRUE, DATE_SUB(NOW(), INTERVAL 15 MINUTE));

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
SELECT 'Datos insertados correctamente' as mensaje;
SELECT COUNT(*) as total_docentes FROM docentes;
SELECT COUNT(*) as total_asistencias FROM asistencias;
SELECT COUNT(*) as total_licencias FROM licencias;
SELECT COUNT(*) as total_usuarios FROM usuarios;
