-- =====================================================
-- SIARH - REPARACIÓN DE BASE DE DATOS
-- Ejecutar este script si tienes errores
-- =====================================================

USE siarh_db;

-- Eliminar tablas en orden correcto (respetando foreign keys)
DROP TABLE IF EXISTS notificaciones;
DROP TABLE IF EXISTS logs_actividad;
DROP TABLE IF EXISTS licencias;
DROP TABLE IF EXISTS asistencias;
DROP TABLE IF EXISTS docentes;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS configuracion;
DROP TABLE IF EXISTS carreras;

-- Eliminar vistas
DROP VIEW IF EXISTS v_asistencias_completas;
DROP VIEW IF EXISTS v_licencias_completas;

-- Eliminar procedimientos
DROP PROCEDURE IF EXISTS sp_registrar_asistencia;

-- Ahora importa el schema.sql completo desde phpMyAdmin
