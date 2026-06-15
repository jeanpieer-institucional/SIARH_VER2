<?php 
$pageTitle = 'Registrar Asistencia';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<!-- Estilos personalizados para el Simulador Biométrico -->
<style>
.tab-container {
    display: flex;
    border-bottom: 2px solid var(--border-color);
    margin-bottom: var(--spacing-lg);
    gap: var(--spacing-md);
}
.tab-button {
    background: none;
    border: none;
    padding: var(--spacing-sm) var(--spacing-lg);
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-secondary);
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
}
.tab-button.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
}
.tab-content {
    display: none;
}
.tab-content.active {
    display: block;
}

/* Estilo del Terminal Biométrico */
.biometric-terminal {
    background: #1e1e24;
    border: 10px solid #2d2d35;
    border-radius: 20px;
    padding: 30px;
    max-width: 500px;
    margin: 0 auto;
    color: #fff;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    position: relative;
    overflow: hidden;
}
.terminal-screen {
    background: #0d0d0f;
    border-radius: 10px;
    padding: 20px;
    height: 180px;
    border: 1px solid #3f3f46;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    margin-bottom: 25px;
    position: relative;
}
.terminal-status-light {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    background: #555; /* Apagada / Gris */
    box-shadow: 0 0 10px rgba(0,0,0,0.5);
    transition: all 0.3s ease;
}
.terminal-status-light.idle { background: #3b82f6; box-shadow: 0 0 15px #3b82f6; } /* Azul */
.terminal-status-light.scanning { background: #eab308; box-shadow: 0 0 25px #eab308; animation: pulse 0.5s infinite alternate; } /* Amarillo */
.terminal-status-light.success { background: #22c55e; box-shadow: 0 0 25px #22c55e; } /* Verde */
.terminal-status-light.error { background: #ef4444; box-shadow: 0 0 25px #ef4444; } /* Rojo */

.terminal-message {
    font-family: 'Courier New', Courier, monospace;
    font-size: 1.1rem;
    color: #38bdf8;
    margin-bottom: 10px;
}
.terminal-submessage {
    font-size: 0.85em;
    color: #a1a1aa;
}

/* Lector de huella */
.scanner-pad {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    background: radial-gradient(circle, #2d2d35 40%, #18181b 100%);
    border: 4px solid #3f3f46;
    margin: 0 auto;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    overflow: hidden;
    box-shadow: inset 0 4px 8px rgba(0,0,0,0.6);
    transition: all 0.2s ease;
}
.scanner-pad:hover {
    border-color: #38bdf8;
    transform: scale(1.03);
}
.scanner-pad:active {
    transform: scale(0.97);
}
.scanner-pad i {
    font-size: 3rem;
    color: #52525b;
    z-index: 2;
    transition: all 0.3s ease;
}
.scanner-pad.active i {
    color: #38bdf8;
    text-shadow: 0 0 15px #38bdf8;
}

/* Láser del escáner */
.scanner-laser {
    position: absolute;
    top: -5px;
    left: 0;
    width: 100%;
    height: 4px;
    background: #38bdf8;
    box-shadow: 0 0 10px #38bdf8, 0 0 20px #38bdf8;
    display: none;
    z-index: 3;
}
@keyframes laserSweep {
    0% { top: 0%; }
    100% { top: 100%; }
}
@keyframes pulse {
    0% { opacity: 0.4; }
    100% { opacity: 1; }
}
</style>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2>
            <i class="fas fa-clipboard-check"></i>
            Registro de Asistencia
        </h2>
        <a href="<?= APP_URL ?>/asistencias" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    
    <div class="card-body">
        
        <!-- Pestañas de Selección de Modo -->
        <div class="tab-container">
            <button class="tab-button active" onclick="switchTab('manual')">
                <i class="fas fa-keyboard"></i> Registro Manual (Admin)
            </button>
            <button class="tab-button" onclick="switchTab('biometric')">
                <i class="fas fa-fingerprint"></i> Simulador Biométrico
            </button>
        </div>

        <!-- CONTENIDO: REGISTRO MANUAL -->
        <div id="tab-manual" class="tab-content active">
            <form action="<?= APP_URL ?>/asistencias/store" method="POST">
                <?= Csrf::input() ?>
                <div class="grid" style="grid-template-columns: 1fr 1fr; gap: var(--spacing-lg);">
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Docente *</label>
                        <select name="docente_id" class="form-control" required>
                            <option value="">Seleccione un docente</option>
                            <?php foreach ($docentes as $docente): ?>
                                <option value="<?= $docente['id'] ?>">
                                    <?= $docente['codigo_empleado'] ?> - <?= $docente['nombres'] . ' ' . $docente['apellidos'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Fecha *</label>
                        <input 
                            type="date" 
                            name="fecha" 
                            class="form-control" 
                            value="<?= date('Y-m-d') ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Hora *</label>
                        <input 
                            type="time" 
                            name="hora" 
                            class="form-control" 
                            value="<?= date('H:i') ?>"
                            required
                        >
                    </div>
                </div>
                
                <div class="mt-lg" style="display: flex; gap: var(--spacing-md); justify-content: flex-end; padding-top: var(--spacing-md); border-top: 1px solid var(--border-color);">
                    <a href="<?= APP_URL ?>/asistencias" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Registrar Asistencia
                    </button>
                </div>
            </form>
        </div>

        <!-- CONTENIDO: SIMULADOR BIOMÉTRICO -->
        <div id="tab-biometric" class="tab-content">
            <p class="text-secondary text-center mb-md">Simula la marcación de asistencia biométrica. Ingresa el DNI del docente y presiona el lector de huella para simular el reconocimiento.</p>
            
            <div class="biometric-terminal">
                <!-- Pantalla del Terminal -->
                <div class="terminal-screen" id="term-screen">
                    <div class="terminal-status-light idle" id="term-light"></div>
                    <div class="terminal-message" id="term-msg">LISTO PARA LECTURA</div>
                    <div class="terminal-submessage" id="term-submsg">Coloque el DNI abajo y presione el sensor</div>
                </div>
                
                <!-- Entrada de Identificación (DNI) -->
                <div class="form-group mb-lg">
                    <label class="form-label" style="color: #a1a1aa;" for="dni-sim">Nº de Documento (DNI) del Docente</label>
                    <input 
                        type="text" 
                        id="dni-sim" 
                        class="form-control" 
                        placeholder="Ingresa DNI de 8 dígitos..." 
                        maxlength="8"
                        style="background: #27272a; border-color: #52525b; color: white; text-align: center; font-size: 1.2rem; letter-spacing: 3px;"
                    >
                </div>
                
                <!-- Pad del Lector de Huella -->
                <div class="scanner-pad" id="scanner-pad" onclick="simulateScan()">
                    <i class="fas fa-fingerprint"></i>
                    <div class="scanner-laser" id="scanner-laser"></div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Manejo de Pestañas
function switchTab(tab) {
    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    if (tab === 'manual') {
        document.querySelectorAll('.tab-button')[0].classList.add('active');
        document.getElementById('tab-manual').classList.add('active');
    } else {
        document.querySelectorAll('.tab-button')[1].classList.add('active');
        document.getElementById('tab-biometric').classList.add('active');
    }
}

// Simulación de Escaneo Biométrico
let isScanning = false;
function simulateScan() {
    if (isScanning) return;
    
    const dni = document.getElementById('dni-sim').value.trim();
    const screen = document.getElementById('term-screen');
    const msg = document.getElementById('term-msg');
    const submsg = document.getElementById('term-submsg');
    const light = document.getElementById('term-light');
    const laser = document.getElementById('scanner-laser');
    const pad = document.getElementById('scanner-pad');
    
    if (!dni || dni.length !== 8) {
        // Alerta de DNI inválido
        light.className = 'terminal-status-light error';
        msg.textContent = 'ERROR: DNI REQUERIDO';
        msg.style.color = '#ef4444';
        submsg.textContent = 'Ingrese un DNI válido de 8 dígitos';
        
        setTimeout(() => {
            resetTerminal();
        }, 3000);
        return;
    }
    
    // Iniciar escaneo
    isScanning = true;
    light.className = 'terminal-status-light scanning';
    msg.textContent = 'ESCANEANDO...';
    msg.style.color = '#eab308';
    submsg.textContent = 'Espere un momento';
    pad.classList.add('active');
    
    laser.style.display = 'block';
    laser.style.animation = 'laserSweep 1s infinite linear';
    
    // Simular retraso de lectura del hardware
    setTimeout(() => {
        // Enviar AJAX a registrarBiometrico
        const formData = new FormData();
        formData.append('dni', dni);
        formData.append('huella_data', 'MOCK_FINGERPRINT_HASH_HEX_ABC123'); // Huella simulada
        
        fetch('<?= APP_URL ?>/asistencias/registrar-biometrico', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Detener láser
            laser.style.display = 'none';
            laser.style.animation = '';
            pad.classList.remove('active');
            
            if (data.success) {
                // Entrada exitosa (Verde)
                light.className = 'terminal-status-light success';
                msg.textContent = 'ACCESO AUTORIZADO';
                msg.style.color = '#22c55e';
                submsg.innerHTML = `<strong>Bienvenido:</strong> ${data.docente}<br><span style="color:#22c55e;">Hora: ${data.hora}</span>`;
            } else {
                // Entrada fallida (Rojo)
                light.className = 'terminal-status-light error';
                msg.textContent = 'ACCESO DENEGADO';
                msg.style.color = '#ef4444';
                submsg.textContent = data.message || 'Huella o DNI no reconocido';
            }
            
            // Reiniciar terminal a los 4 segundos
            setTimeout(() => {
                resetTerminal();
                isScanning = false;
            }, 4000);
        })
        .catch(err => {
            laser.style.display = 'none';
            laser.style.animation = '';
            pad.classList.remove('active');
            
            light.className = 'terminal-status-light error';
            msg.textContent = 'ERROR DE CONEXION';
            msg.style.color = '#ef4444';
            submsg.textContent = 'Reintente más tarde';
            
            setTimeout(() => {
                resetTerminal();
                isScanning = false;
            }, 4000);
        });
    }, 1500); // 1.5s de escaneo
}

function resetTerminal() {
    const msg = document.getElementById('term-msg');
    const submsg = document.getElementById('term-submsg');
    const light = document.getElementById('term-light');
    
    light.className = 'terminal-status-light idle';
    msg.textContent = 'LISTO PARA LECTURA';
    msg.style.color = '#38bdf8';
    submsg.textContent = 'Coloque el DNI abajo y presione el sensor';
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
