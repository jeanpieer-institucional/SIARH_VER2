ALTER TABLE usuarios 
ADD COLUMN intentos_fallidos INT DEFAULT 0 AFTER estado,
ADD COLUMN bloqueos_totales INT DEFAULT 0 AFTER intentos_fallidos,
ADD COLUMN bloqueado_hasta TIMESTAMP NULL AFTER bloqueos_totales;
