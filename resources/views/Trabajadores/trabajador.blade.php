@extends('layouts.dashboard')

@section('title', 'Dashboard Trabajador')
@section('panel-title', 'Panel de Trabajador')

@section('extra-styles')
<style>
    .project-card {
        border-left: 4px solid var(--accent);
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .project-card:hover {
        border-left-color: var(--secondary);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transform: translateY(-3px);
    }
    
    .task-item {
        border-left: 3px solid #cbd5e1;
        transition: all 0.3s;
    }
    
    .task-item:hover {
        border-left-color: var(--secondary);
        background: #f8fafc;
    }
    
    .task-item.completed {
        opacity: 0.7;
        border-left-color: #10b981;
    }
</style>
@endsection

@section('sidebar-menu')
<ul class="nav flex-column px-3 mt-3">
    <li class="nav-item">
        <a class="nav-link active" href="#" onclick="showSection('overview');return false">
            <i class="bi bi-graph-up me-2"></i>Vista General
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" onclick="showSection('projects');return false">
            <i class="bi bi-building me-2"></i>Mis Proyectos
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" onclick="showSection('tasks');return false">
            <i class="bi bi-list-task me-2"></i>Mis Tareas
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" onclick="showSection('report');return false">
            <i class="bi bi-exclamation-triangle me-2"></i>Reportar Problema
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" onclick="confirmLogout();return false">
            <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
        </a>
    </li>
</ul>
@endsection

@section('content')
<!-- Vista General -->
<div id="overview" class="content-section">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="mb-1">Bienvenido de Nuevo</h2>
            <p class="text-muted mb-0">Resumen de tu actividad</p>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <x-stat-card 
            color="blue" 
            value="{{ $totalProjects }}" 
            label="Proyectos" 
            icon="building" 
            iconBg="primary" 
        />
        
        <x-stat-card 
            color="yellow" 
            value="{{ $totalTasks }}" 
            label="Tareas Asignadas" 
            icon="list-task" 
            iconBg="warning" 
        />
        
        <x-stat-card 
            color="green" 
            value="{{ $completedTasks }}" 
            label="Completadas" 
            icon="check-circle" 
            iconBg="success" 
        />
        
        <x-stat-card 
            color="purple" 
            value="{{ $pendingTasks }}" 
            label="Pendientes" 
            icon="clock" 
            iconBg="danger" 
        />
    </div>
    
    <!-- Recent Tasks -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Mis Tareas Recientes</h5>
            <button class="btn btn-sm btn-outline-secondary" onclick="showSection('tasks')">
                Ver todas
            </button>
        </div>
        <div class="card-body" id="recentTasks">
            <p class="text-muted text-center py-4">No hay tareas asignadas</p>
        </div>
    </div>
</div>

<!-- Sección de Proyectos -->
<div id="projects" class="content-section d-none">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="mb-1">Mis Proyectos</h2>
            <p class="text-muted mb-0">Proyectos en los que estás trabajando</p>
        </div>
    </div>
    
    <div id="projectsList" class="row g-3"></div>
    
    <div id="emptyProjects" class="card shadow-sm text-center d-none">
        <div class="card-body py-5">
            <i class="bi bi-building display-1 text-muted mb-3"></i>
            <h3>Sin Proyectos Asignados</h3>
            <p class="text-muted">No tienes proyectos asignados en este momento</p>
        </div>
    </div>
</div>

<!-- Detalle del Proyecto -->
<div id="project-detail" class="content-section d-none">
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <button class="btn btn-sm btn-outline-secondary mb-2" onclick="showSection('projects')">
                    <i class="bi bi-arrow-left me-1"></i>Volver a Proyectos
                </button>
                <h2 class="mb-1" id="projectDetailTitle">Proyecto</h2>
                <p class="text-muted mb-0" id="projectDetailClient">Cliente</p>
            </div>
            <div>
                <span class="badge badge-active">Activo</span>
            </div>
        </div>
    </div>
    
    <!-- Project Info -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-light h-100">
                <div class="card-body text-center">
                    <small class="text-muted">Avance del Proyecto</small>
                    <h4 class="mt-2 mb-0"><span id="projectDetailProgress">0</span>%</h4>
                    <div class="progress mt-2" style="height:8px">
                        <div class="progress-bar bg-success" id="projectDetailProgressBar" style="width:0%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light h-100">
                <div class="card-body text-center">
                    <small class="text-muted">Mis Tareas</small>
                    <h4 class="mt-2 mb-0" id="projectDetailMyTasks">0</h4>
                    <small class="text-muted">Asignadas a mí</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light h-100">
                <div class="card-body text-center">
                    <small class="text-muted">Completadas</small>
                    <h4 class="mt-2 mb-0 text-success" id="projectDetailCompletedTasks">0</h4>
                    <small class="text-muted">Por mí</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tareas del Proyecto -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-list-task me-2"></i>Mis Tareas en este Proyecto</h5>
        </div>
        <div class="card-body">
            <div id="projectTasksList"></div>
            <div id="emptyProjectTasks" class="text-center py-4 d-none">
                <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                <h5>Sin Tareas</h5>
                <p class="text-muted">No tienes tareas asignadas en este proyecto</p>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Tareas -->
<div id="tasks" class="content-section d-none">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="mb-1">Mis Tareas</h2>
            <p class="text-muted mb-0">Tareas asignadas a ti</p>
        </div>
    </div>
    
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <input type="text" class="form-control" id="searchTask" placeholder="🔍 Buscar tarea..." onkeyup="filterTasks()">
        </div>
        <div class="col-md-6">
            <select class="form-select" id="filterTaskStatus" onchange="filterTasks()">
                <option value="">Todos los estados</option>
                <option value="pending">Pendientes</option>
                <option value="in-progress">En Progreso</option>
                <option value="completed">Completadas</option>
            </select>
        </div>
    </div>
    
    <div id="tasksList"></div>
    
    <div id="emptyTasks" class="card shadow-sm text-center d-none">
        <div class="card-body py-5">
            <i class="bi bi-inbox display-1 text-muted mb-3"></i>
            <h3>Sin Tareas Asignadas</h3>
            <p class="text-muted">No tienes tareas asignadas en este momento</p>
        </div>
    </div>
</div>

<!-- Sección de Reportar Problema -->
<div id="report" class="content-section d-none">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="mb-1">Reportar Problema</h2>
            <p class="text-muted mb-0">Notifica a tu supervisor sobre cualquier inconveniente</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form id="problemForm" onsubmit="submitProblem(event)">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Proyecto *</label>
                                <select class="form-select" id="problemProject" required>
                                    <option value="">Seleccionar proyecto...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Categoría *</label>
                                <select class="form-select" id="problemCategory" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="Material">Falta de Material</option>
                                    <option value="Herramienta">Problema con Herramienta</option>
                                    <option value="Seguridad">Seguridad</option>
                                    <option value="Clima">Clima</option>
                                    <option value="Personal">Personal</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prioridad *</label>
                                <select class="form-select" id="problemPriority" required>
                                    <option value="low">Baja</option>
                                    <option value="medium" selected>Media</option>
                                    <option value="high">Alta</option>
                                    <option value="urgent">Urgente</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ubicación</label>
                                <input type="text" class="form-control" id="problemLocation" placeholder="Ej: Piso 2 - Área norte">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Título del Problema *</label>
                                <input type="text" class="form-control" id="problemTitle" required placeholder="Resumen breve del problema">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Descripción Detallada *</label>
                                <textarea class="form-control" id="problemDescription" rows="5" required placeholder="Describe el problema con detalle..."></textarea>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="bi bi-send me-2"></i>Enviar Reporte
                            </button>
                            <button type="reset" class="btn btn-secondary ms-2">
                                <i class="bi bi-x-circle me-2"></i>Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="mb-3"><i class="bi bi-clock-history me-2"></i>Mis Reportes Recientes</h6>
                    <div id="myRecentProblems">
                        <p class="text-muted small text-center">No hay reportes</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modals')
<!-- Modal Cambiar Estado de Tarea -->
<div class="modal fade" id="modalTaskStatus" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambiar Estado de Tarea</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong id="taskStatusTitle"></strong>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nuevo Estado</label>
                    <select class="form-select" id="newTaskStatus">
                        <option value="pending">Pendiente</option>
                        <option value="in-progress">En Progreso</option>
                        <option value="completed">Completada</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary-custom" onclick="confirmTaskStatusChange()">
                        <i class="bi bi-save me-1"></i>Guardar
                    </button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/worker-dashboard.js') }}"></script>
@endsection