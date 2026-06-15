<?php
/**
 * Asistencia Model
 */

class Asistencia extends Model {
    protected $table = 'asistencias';
    
    /**
     * Registrar asistencia
     */
    public function registrar($docenteId, $fecha, $hora, $tipo = 'manual') {
        // Verificar si ya existe registro para hoy
        $existing = $this->query(
            "SELECT * FROM {$this->table} WHERE docente_id = :docente_id AND fecha = :fecha LIMIT 1",
            ['docente_id' => $docenteId, 'fecha' => $fecha]
        );
        
        // Obtener configuración global
        $configModel = new Configuracion();
        $horaEntrada = $configModel->getValor('hora_entrada');
        $tolerancia = intval($configModel->getValor('tolerancia_minutos'));
        
        // Intentar obtener el horario específico del docente para el día de la semana correspondiente
        $diasMap = [
            1 => 'lunes',
            2 => 'martes',
            3 => 'miercoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sabado',
            7 => 'domingo'
        ];
        $nDia = intval(date('N', strtotime($fecha)));
        $diaSemana = $diasMap[$nDia];
        
        $horarioModel = new Horario();
        $horarioDocente = $horarioModel->getByDocenteYDia($docenteId, $diaSemana);
        
        if ($horarioDocente) {
            $horaEntrada = $horarioDocente['hora_entrada'];
        }
        
        // Calcular estado y tardanza
        $horaEntradaTime = strtotime($horaEntrada);
        $horaRegistroTime = strtotime($hora);
        $minutosTardanza = ($horaRegistroTime - $horaEntradaTime) / 60;
        
        $estado = 'presente';
        if ($minutosTardanza > $tolerancia) {
            $estado = 'tardanza';
        } else {
            $minutosTardanza = 0;
        }
        
        $data = [
            'docente_id' => $docenteId,
            'fecha' => $fecha,
            'estado' => $estado,
            'minutos_tardanza' => max(0, $minutosTardanza),
            'tipo_registro' => $tipo,
            'ip_registro' => $_SERVER['REMOTE_ADDR']
        ];
        
        if (empty($existing)) {
            // Primera marcación (entrada)
            $data['hora_entrada'] = $hora;
            return $this->create($data);
        } else {
            // Segunda marcación (salida)
            return $this->update($existing[0]['id'], ['hora_salida' => $hora]);
        }
    }
    
    /**
     * Obtener asistencias del día
     */
    public function getAsistenciasDelDia($fecha = null) {
        if (!$fecha) {
            $fecha = date('Y-m-d');
        }
        
        $sql = "SELECT a.*, 
                       CONCAT(d.nombres, ' ', d.apellidos) as nombre_completo,
                       d.codigo_empleado,
                       d.dni,
                       c.nombre as carrera
                FROM {$this->table} a
                INNER JOIN docentes d ON a.docente_id = d.id
                LEFT JOIN carreras c ON d.carrera_id = c.id
                WHERE a.fecha = :fecha
                ORDER BY a.hora_entrada DESC";
        
        return $this->query($sql, ['fecha' => $fecha]);
    }
    
    /**
     * Obtener reporte mensual por docente
     */
    public function getReporteMensual($docenteId, $mes, $anio) {
        $sql = "SELECT * FROM {$this->table}
                WHERE docente_id = :docente_id
                  AND MONTH(fecha) = :mes
                  AND YEAR(fecha) = :anio
                ORDER BY fecha";
        
        return $this->query($sql, [
            'docente_id' => $docenteId,
            'mes' => $mes,
            'anio' => $anio
        ]);
    }
    
    /**
     * Obtener estadísticas de asistencia
     */
    public function getEstadisticas($fechaInicio, $fechaFin, $carreraId = null) {
        $sql = "SELECT 
                    COUNT(*) as total_registros,
                    SUM(CASE WHEN a.estado = 'presente' THEN 1 ELSE 0 END) as presentes,
                    SUM(CASE WHEN a.estado = 'tardanza' THEN 1 ELSE 0 END) as tardanzas,
                    SUM(CASE WHEN a.estado = 'ausente' THEN 1 ELSE 0 END) as ausentes,
                    AVG(a.minutos_tardanza) as promedio_tardanza
                FROM {$this->table} a
                INNER JOIN docentes d ON a.docente_id = d.id
                WHERE a.fecha BETWEEN :fecha_inicio AND :fecha_fin";
        
        $params = [
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin
        ];
        
        if ($carreraId) {
            $sql .= " AND d.carrera_id = :carrera_id";
            $params['carrera_id'] = $carreraId;
        }
        
        $result = $this->query($sql, $params);
        return $result[0];
    }
}
