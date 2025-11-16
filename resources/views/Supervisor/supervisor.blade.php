@extends('layouts.dashboard')

@section('title', 'Dashboard Supervisor')
@section('panel-title', 'Panel de Supervisor')

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

    .evidence-card img {
        height: 200px;
        object-fit: cover;
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
        <a class="nav-link" href="#" onclick="showSection('notifications');return false">
            <i class="bi bi-bell me-2"></i>Notificaciones
            <span class="badge bg-danger ms-2" id="notificationBadge" style="display:none;">0</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" onclick="showSection('attendance');return false">
            <i class="bi bi-calendar-check me-2"></i>Asistencias
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
            <h2 class="mb-1">Bienvenido, {{ Auth::user()->name }}</h2>
            <p class="text-muted mb-0">Resumen de tus proyectos</p>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <x-stat-card 
            color="blue" 
            value="{{ $totalProjects }}" 
            label="Mis Proyectos" 
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
            label="Tareas Completadas" 
            icon="check-circle" 
            iconBg="success" 
        />
        
        <x-stat-card 
            color="red" 
            value="{{ $pendingNotifications }}" 
            label="Notificaciones" 
            icon="bell" 
            iconBg="danger" 
        />
    </div>
    
    <!-- Proyectos Recientes -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Proyectos Activos</h5>
            <button class="btn btn-sm btn-outline-secondary" onclick="showSection('projects')">
                Ver todos
            </button>
        </div>
        <div class="card-body" id="recentProjects">
            <p class="text-muted text-center py-4">Cargando...</p>
        </div>
    </div>
</div>

<!-- Sección de Proyectos -->
<div id="projects" class="content-section d-none">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="mb-1">Mis Proyectos</h2>
            <p class="text-muted mb-0">Proyectos bajo tu supervisión</p>
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
        <div class="card-body">
            <button class="btn btn-sm btn-outline-secondary mb-2" onclick="showSection('projects')">
                <i class="bi bi-arrow-left me-1"></i>Volver a Proyectos
            </button>
            <h2 class="mb-1" id="projectDetailTitle">Proyecto</h2>
            <p class="text-muted mb-0" id="projectDetailClient">Cliente</p>
        </div>
    </div>
    
    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3" id="projectTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button">
                <i class="bi bi-info-circle me-2"></i>Información
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button">
                <i class="bi bi-list-task me-2"></i>Tareas
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="evidence-tab" data-bs-toggle="tab" data-bs-target="#evidence" type="button">
                <i class="bi bi-camera me-2"></i>Evidencias
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="materials-tab" data-bs-toggle="tab" data-bs-target="#materials" type="button">
                <i class="bi bi-box-seam me-2"></i>Materiales
            </button>
        </li>
    </ul>
    
    <div class="tab-content" id="projectTabsContent">
        <!-- Tab Información -->
        <div class="tab-pane fade show active" id="info" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Avance del Proyecto</h5>
                    <div class="d-flex align-items-center mb-3">
                        <span class="me-2" id="currentProgress">0</span>%
                        <div class="progress flex-grow-1 ms-2">
                            <div class="progress-bar bg-success" id="progressBar" style="width:0%"></div>
                        </div>
                    </div>
                    <div class="input-group">
                        <input type="number" class="form-control" id="newProgress" min="0" max="100" placeholder="Nuevo avance">
                        <button class="btn btn-primary-custom" onclick="updateProgress()">
                            <i class="bi bi-arrow-up-circle me-1"></i>Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tab Tareas -->
        <div class="tab-pane fade" id="tasks" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tareas del Proyecto</h5>
                    <button class="btn btn-sm btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalTask" onclick="openNewTask()">
                        <i class="bi bi-plus-circle me-1"></i>Nueva Tarea
                    </button>
                </div>
                <div class="card-body" id="projectTasksList"></div>
            </div>
        </div>
        
        <!-- Tab Evidencias -->
        <div class="tab-pane fade" id="evidence" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Evidencias Fotográficas</h5>
                    <button class="btn btn-sm btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalEvidence">
                        <i class="bi bi-camera me-1"></i>Subir Evidencia
                    </button>
                </div>
                <div class="card-body">
                    <div id="projectEvidencesList" class="row g-3"></div>
                </div>
            </div>
        </div>
        
        <!-- Tab Materiales -->
        <div class="tab-pane fade" id="materials" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Control de Materiales</h5>
                    <button class="btn btn-sm btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalMaterial">
                        <i class="bi bi-plus-circle me-1"></i>Registrar Material
                    </button>
                </div>
                <div class="card-body">
                    <div id="projectMaterialsList"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Notificaciones -->
<div id="notifications" class="content-section d-none">
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">Notificaciones</h2>
                <p class="text-muted mb-0">Problemas reportados por trabajadores</p>
            </div>
            <button class="btn btn-outline-secondary" onclick="markAllAsRead()">
                <i class="bi bi-check-all me-1"></i>Marcar todas como leídas
            </button>
        </div>
    </div>
    
    <div id="notificationsList"></div>
    
    <div id="emptyNotifications" class="card shadow-sm text-center d-none">
        <div class="card-body py-5">
            <i class="bi bi-bell-slash display-1 text-muted mb-3"></i>
            <h3>No hay notificaciones</h3>
            <p class="text-muted">Todas las notificaciones están al día</p>
        </div>
    </div>
</div>

<!-- Sección de Asistencias -->
<div id="attendance" class="content-section d-none">
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">Control de Asistencias</h2>
                <p class="text-muted mb-0">Registro de asistencia de trabajadores</p>
            </div>
            <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalAttendance" onclick="prepareAttendanceModal()">
                <i class="bi bi-plus-circle me-1"></i>Registrar Asistencia
            </button>
        </div>
    </div>
    
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <label class="form-label">Fecha:</label>
                    <input type="date" class="form-control" id="attendanceFilterDate" onchange="loadAttendance()">
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-body">
            <div id="attendanceList"></div>
        </div>
    </div>
</div>
@endsection

@section('modals')
<!-- Modal Tarea -->
<div class="modal fade" id="modalTask" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTaskTitle">Nueva Tarea</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="taskForm" onsubmit="saveTask(event)">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Título de la Tarea *</label>
                            <input type="text" class="form-control" id="taskTitle" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Asignar a *</label>
                            <select class="form-select" id="taskWorker" required>
                                <option value="">Seleccionar trabajador...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prioridad *</label>
                            <select class="form-select" id="taskPriority" required>
                                <option value="low">Baja</option>
                                <option value="medium" selected>Media</option>
                                <option value="high">Alta</option>
                                <option value="urgent">Urgente</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado *</label>
                            <select class="form-select" id="taskStatus" required>
                                <option value="pending" selected>Pendiente</option>
                                <option value="in-progress">En Progreso</option>
                                <option value="completed">Completada</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha Límite *</label>
                            <input type="date" class="form-control" id="taskDeadline" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descripción *</label>
                            <textarea class="form-control" id="taskDescription" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-save me-2"></i>Guardar
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Evidencia -->
<div class="modal fade" id="modalEvidence" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Subir Evidencia</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="evidenceForm" onsubmit="saveEvidence(event)">
                    <div class="mb-3">
                        <label class="form-label">Título *</label>
                        <input type="text" class="form-control" id="evidenceTitle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" id="evidenceDescription" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto *</label>
                        <input type="file" class="form-control" id="evidencePhoto" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <img id="evidencePreview" class="img-fluid rounded" style="display:none;max-height:200px;">
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-upload me-2"></i>Subir
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Material -->
<div class="modal fade" id="modalMaterial" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Material</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="materialForm" onsubmit="saveMaterial(event)">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nombre del Material *</label>
                            <input type="text" class="form-control" id="materialName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cantidad *</label>
                            <input type="number" class="form-control" id="materialQuantity" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unidad *</label>
                            <input type="text" class="form-control" id="materialUnit" placeholder="ej: kg, m, pza" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Costo</label>
                            <input type="number" class="form-control" id="materialCost" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Proveedor</label>
                            <input type="text" class="form-control" id="materialSupplier">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notas</label>
                            <textarea class="form-control" id="materialNotes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-save me-2"></i>Guardar
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Asistencia -->
<div class="modal fade" id="modalAttendance" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Asistencia</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="attendanceForm" onsubmit="saveAttendance(event)">
                    <div class="mb-3">
                        <label class="form-label">Trabajador *</label>
                        <select class="form-select" id="attendanceWorkerId" required>
                            <option value="">Seleccionar...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha *</label>
                        <input type="date" class="form-control" id="attendanceDate" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado *</label>
                        <select class="form-select" id="attendanceStatus" required>
                            <option value="present">Presente</option>
                            <option value="late">Tarde</option>
                            <option value="absent">Ausente</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hora de llegada</label>
                        <input type="time" class="form-control" id="attendanceTime">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <textarea class="form-control" id="attendanceNotes" rows="2"></textarea>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-save me-2"></i>Guardar
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalle Notificación -->
<div class="modal fade" id="modalNotification" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalTitle">Detalle del Problema</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="notificationModalBody">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/supervisor-dashboard.js') }}"></script>
@endsection