<?php 
$pageTitle = 'Gestión de Docentes';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users"></i>
            Lista de Docentes
        </h3>
        <?php if (in_array($_SESSION['user_role'], ['admin', 'supervisor'])): ?>
        <a href="<?= APP_URL ?>/docentes/create" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nuevo Docente
        </a>
        <?php endif; ?>
    </div>
    
    <div class="card-body">
        <!-- Filtros y Búsqueda -->
        <div class="flex-between mb-2" style="gap: var(--spacing-md); align-items: flex-end;">
            <div style="flex: 1;">
                <label for="search-input" class="form-label">Búsqueda</label>
                <input 
                    type="text" 
                    id="search-input"
                    class="form-control" 
                    placeholder="Buscar por nombre, DNI o código..."
                    value="<?= htmlspecialchars($search) ?>"
                >
            </div>
            
            <div style="width: 200px;">
                <label for="filter-estado" class="form-label">Estado</label>
                <select id="filter-estado" class="form-control">
                    <option value="">Todos los estados</option>
                    <option value="activo" <?= $estado === 'activo' ? 'selected' : '' ?>>Activos</option>
                    <option value="inactivo" <?= $estado === 'inactivo' ? 'selected' : '' ?>>Inactivos</option>
                    <option value="licencia" <?= $estado === 'licencia' ? 'selected' : '' ?>>En Licencia</option>
                </select>
            </div>
            
            <div style="width: 250px;">
                <label for="filter-carrera" class="form-label">Carrera</label>
                <select id="filter-carrera" class="form-control">
                    <option value="">Todas las carreras</option>
                    <?php foreach ($carreras as $carrera): ?>
                        <option value="<?= $carrera['id'] ?>" <?= $carrera_id == $carrera['id'] ? 'selected' : '' ?>>
                            <?= $carrera['nombre'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <button id="btn-search" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </div>
        
        <!-- Tabla de Docentes -->
        <div class="table-container">
            <table class="table" id="docentes-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre Completo</th>
                        <th>DNI</th>
                        <th>Carrera</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($docentes)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron docentes</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($docentes as $docente): ?>
                        <tr>
                            <td><strong><?= $docente['codigo_empleado'] ?></strong></td>
                            <td><?= $docente['nombres'] . ' ' . $docente['apellidos'] ?></td>
                            <td><?= $docente['dni'] ?></td>
                            <td><?= $docente['carrera_nombre'] ?? 'N/A' ?></td>
                            <td><?= $docente['email'] ?></td>
                            <td>
                                <span class="badge badge-<?= $docente['estado'] === 'activo' ? 'success' : ($docente['estado'] === 'licencia' ? 'warning' : 'secondary') ?>">
                                    <?= ucfirst($docente['estado']) ?>
                                </span>
                                <?php if ($docente['tiene_huella']): ?>
                                    <i class="fas fa-fingerprint text-success" title="Huella registrada"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="flex gap-1">
                                    <?php if (in_array($_SESSION['user_role'], ['admin', 'supervisor'])): ?>
                                    <a href="<?= APP_URL ?>/docentes/edit/<?= $docente['id'] ?>" class="btn btn-sm btn-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                    <button onclick="deleteDocente(<?= $docente['id'] ?>)" class="btn btn-sm btn-error" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnSearch = document.getElementById('btn-search');
    const searchInput = document.getElementById('search-input');

    if (btnSearch) {
        btnSearch.addEventListener('click', function() {
            const term = searchInput.value;
            performSearch(term);
        });
    }

    // Permitir buscar al presionar Enter en el input
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch(this.value);
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
