<?php
/**
 * EvaluacionController - Controlador de Evaluación de Desempeño
 */

class EvaluacionController extends Controller {
    private $evaluacionModel;
    
    public function __construct() {
        $this->evaluacionModel = new Evaluacion();
    }
    
    public function index() {
        $this->requireAuth();
        $this->view('evaluaciones/index');
    }
    
    public function create() {
        $this->requireRole(['admin', 'supervisor']);
        $this->view('evaluaciones/form');
    }
    
    public function store() {
        // Logica para guardar evaluacion
    }
}
