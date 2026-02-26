<?php
/**
 * PlanillaController - Controlador de Planillas (Remuneraciones)
 */

class PlanillaController extends Controller {
    private $planillaModel;
    
    public function __construct() {
        $this->planillaModel = new Planilla();
    }
    
    public function index() {
        $this->requireAuth();
        $this->view('planillas/index');
    }
    
    public function create() {
        $this->requireRole('admin');
        $this->view('planillas/form');
    }
    
    public function store() {
        // Logica para generar planilla
    }
}
