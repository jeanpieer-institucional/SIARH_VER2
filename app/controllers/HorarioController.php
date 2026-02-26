<?php
/**
 * HorarioController - Controlador de Horarios
 */

class HorarioController extends Controller {
    private $horarioModel;
    
    public function __construct() {
        $this->horarioModel = new Horario();
    }
    
    public function index() {
        $this->requireAuth();
        $this->view('horarios/index');
    }
    
    public function create() {
        $this->requireRole(['admin', 'supervisor']);
        $this->view('horarios/form');
    }
    
    public function store() {
        // Logica para guardar horario
    }
}
