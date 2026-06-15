<?php
/**
 * Planilla Model
 */

class Planilla extends Model {
    protected $table = 'planillas';
    
    /**
     * Obtener todas las planillas de un docente
     */
    public function getByDocente($docenteId) {
        $sql = "SELECT p.*, CONCAT(d.nombres, ' ', d.apellidos) as docente_nombre 
                FROM {$this->table} p
                INNER JOIN docentes d ON p.docente_id = d.id
                WHERE p.docente_id = :docente_id 
                ORDER BY p.anio DESC, p.mes DESC";
        return $this->query($sql, ['docente_id' => $docenteId]);
    }
    
    /**
     * Obtener todas las planillas de un mes y año específico con info de docentes
     */
    public function getByMesAnio($mes, $anio) {
        $sql = "SELECT p.*, 
                       CONCAT(d.nombres, ' ', d.apellidos) as docente_nombre,
                       d.codigo_empleado,
                       d.dni,
                       c.nombre as carrera
                FROM {$this->table} p
                INNER JOIN docentes d ON p.docente_id = d.id
                LEFT JOIN carreras c ON d.carrera_id = c.id
                WHERE p.mes = :mes AND p.anio = :anio
                ORDER BY d.apellidos ASC, d.nombres ASC";
        return $this->query($sql, ['mes' => $mes, 'anio' => $anio]);
    }
    
    /**
     * Obtener una planilla por docente, mes y año para verificar duplicados
     */
    public function getByDocenteMesAnio($docenteId, $mes, $anio) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE docente_id = :docente_id 
                AND mes = :mes 
                AND anio = :anio 
                LIMIT 1";
        $result = $this->query($sql, [
            'docente_id' => $docenteId,
            'mes' => $mes,
            'anio' => $anio
        ]);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Calcular detalles mensuales de asistencia para estimar la planilla
     */
    public function calcularDetallesMensuales($docenteId, $mes, $anio, $sueldoBase = 3000.00) {
        // 1. Obtener tardanzas de la tabla de asistencias
        $sqlTardanzas = "SELECT 
                            SUM(minutos_tardanza) as total_minutos,
                            SUM(CASE WHEN estado = 'tardanza' THEN 1 ELSE 0 END) as total_tardanzas
                         FROM asistencias 
                         WHERE docente_id = :docente_id 
                           AND MONTH(fecha) = :mes 
                           AND YEAR(fecha) = :anio";
        $resTardanzas = $this->query($sqlTardanzas, [
            'docente_id' => $docenteId,
            'mes' => $mes,
            'anio' => $anio
        ]);
        
        $totalMinutosTardanza = intval($resTardanzas[0]['total_minutos'] ?? 0);
        $totalTardanzas = intval($resTardanzas[0]['total_tardanzas'] ?? 0);
        
        // 2. Obtener inasistencias registradas
        $sqlFaltas = "SELECT COUNT(*) as total_faltas 
                      FROM asistencias 
                      WHERE docente_id = :docente_id 
                        AND MONTH(fecha) = :mes 
                        AND YEAR(fecha) = :anio 
                        AND estado = 'ausente'";
        $resFaltas = $this->query($sqlFaltas, [
            'docente_id' => $docenteId,
            'mes' => $mes,
            'anio' => $anio
        ]);
        $totalFaltas = intval($resFaltas[0]['total_faltas'] ?? 0);
        
        // 3. Obtener licencias aprobadas en el mes
        $sqlLicencias = "SELECT COUNT(*) as total_licencias 
                         FROM licencias 
                         WHERE docente_id = :docente_id 
                           AND estado = 'aprobado'
                           AND (MONTH(fecha_inicio) = :mes OR MONTH(fecha_fin) = :mes)
                           AND (YEAR(fecha_inicio) = :anio OR YEAR(fecha_fin) = :anio)";
        $resLicencias = $this->query($sqlLicencias, [
            'docente_id' => $docenteId,
            'mes' => $mes,
            'anio' => $anio
        ]);
        $totalLicencias = intval($resLicencias[0]['total_licencias'] ?? 0);
        
        // Regla de Descuentos: 
        // - Cada minuto de tardanza descuenta 0.50 PEN
        // - Cada inasistencia descuenta 1 día de salario (Sueldo Base / 30)
        $costoMinutoTardanza = 0.50;
        $descuentoTardanza = $totalMinutosTardanza * $costoMinutoTardanza;
        $descuentoFalta = $totalFaltas * ($sueldoBase / 30);
        $totalDescuentos = round($descuentoTardanza + $descuentoFalta, 2);
        
        // Regla de Bonificaciones:
        // - Si el docente tiene asistencia perfecta (0 tardanzas y 0 faltas), bono de 200.00 PEN
        $bonificaciones = 0.00;
        
        // Verificar si tiene al menos un registro de asistencia en el mes para aplicar el bono
        $sqlAsistio = "SELECT COUNT(*) as total FROM asistencias 
                       WHERE docente_id = :docente_id 
                         AND MONTH(fecha) = :mes 
                         AND YEAR(fecha) = :anio";
        $resAsistio = $this->query($sqlAsistio, [
            'docente_id' => $docenteId,
            'mes' => $mes,
            'anio' => $anio
        ]);
        $totalAsistencias = intval($resAsistio[0]['total'] ?? 0);
        
        if ($totalAsistencias > 0 && $totalTardanzas === 0 && $totalFaltas === 0) {
            $bonificaciones = 200.00;
        }
        
        $totalPagar = max(0, $sueldoBase - $totalDescuentos + $bonificaciones);
        
        return [
            'total_minutos_tardanza' => $totalMinutosTardanza,
            'total_tardanzas' => $totalTardanzas,
            'total_faltas' => $totalFaltas,
            'total_licencias' => $totalLicencias,
            'descuento_tardanza' => $descuentoTardanza,
            'descuento_falta' => $descuentoFalta,
            'descuentos' => $totalDescuentos,
            'bonificaciones' => $bonificaciones,
            'sueldo_base' => $sueldoBase,
            'total_pagar' => round($totalPagar, 2)
        ];
    }
}
