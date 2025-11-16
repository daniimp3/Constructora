@extends('layouts.dashboard')

@section('title', 'Dashboard Administrador')
@section('panel-title', 'Panel de Administrador')

@section('sidebar-menu')
<ul class="nav flex-column px-3 mt-3">
    <li class="nav-item">
        <a class="nav-link active" href="#" onclick="showSection('overview');return false">
            <i class="bi bi-speedometer2 me-2"></i>Vista General
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" onclick="showSection('projects');return false">
            <i class="bi bi-building me-2"></i>Proyectos
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" onclick="showSection('workers');return false">
            <i class="bi bi-people me-2"></i>Personal
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
            <p class="text-muted mb-0">Resumen general del sistema</p>
        </div>
    </div>
    
    <!-- Stats Cards usando componente -->
    <div class="row g-3 mb-4">
        <x-stat-card 
            color="blue" 
            value="{{ $totalProjects }}" 
            label="Total Proyectos" 
            icon="building" 
            iconBg="primary" 
        />
        
        <x-stat-card 
            color="green" 
            value="{{ $activeProjects }}" 
            label="Proyectos Activos" 
            icon="check-circle" 
            iconBg="success" 
        />
        
        <x-stat-card 
            color="purple" 
            value="${{ number_format($totalBudget, 2) }}" 
            label="Presupuesto Total" 
            icon="cash-stack" 
            iconBg="info" 
        />
        
        <x-stat-card 
            color="yellow" 
            value="{{ $totalWorkers }}" 
            label="Total Personal" 
            icon="people" 
            iconBg="warning" 
        />
    </div>
    
    <!-- Proyectos Recientes -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Proyectos Recientes</h5>
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
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">Gestión de Proyectos</h2>
                <p class="text-muted mb-0">Administra todos los proyectos de construcción</p>
            </div>
            <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalProject" onclick="openNewProject()">
                <i class="bi bi-plus-circle me-2"></i>Nuevo Proyecto
            </button>
        </div>
    </div>
    
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <input type="text" class="form-control" id="searchProject" placeholder="🔍 Buscar proyecto..." onkeyup="filterProjects()">
        </div>
        <div class="col-md-6">
            <select class="form-select" id="filterProjectStatus" onchange="filterProjects()">
                <option value="">Todos los estados</option>
                <option value="active">Activos</option>
                <option value="paused">Pausados</option>
                <option value="completed">Completados</option>
            </select>
        </div>
    </div>
    
    <div id="projectsList"></div>
    
    <div id="emptyProjects" class="card shadow-sm text-center d-none">
        <div class="card-body py-5">
            <i class="bi bi-folder-x display-1 text-muted mb-3"></i>
            <h3>No hay proyectos</h3>
            <p class="text-muted">Crea tu primer proyecto haciendo clic en "Nuevo Proyecto"</p>
        </div>
    </div>
</div>

<!-- Sección de Personal -->
<div id="workers" class="content-section d-none">
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">Gestión de Personal</h2>
                <p class="text-muted mb-0">Administra supervisores y trabajadores</p>
            </div>
            <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalWorker" onclick="openNewWorker()">
                <i class="bi bi-person-plus me-2"></i>Agregar Personal
            </button>
        </div>
    </div>
    
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <input type="text" class="form-control" id="searchWorker" placeholder="🔍 Buscar personal..." onkeyup="filterWorkers()">
        </div>
        <div class="col-md-6">
            <select class="form-select" id="filterWorkerRole" onchange="filterWorkers()">
                <option value="">Todos los roles</option>
                <option value="supervisor">Supervisores</option>
                <option value="trabajador">Trabajadores</option>
            </select>
        </div>
    </div>
    
    <div id="workersList"></div>
    
    <div id="emptyWorkers" class="card shadow-sm text-center d-none">
        <div class="card-body py-5">
            <i class="bi bi-person-x display-1 text-muted mb-3"></i>
            <h3>No hay personal registrado</h3>
            <p class="text-muted">Agrega tu primer empleado haciendo clic en "Agregar Personal"</p>
        </div>
    </div>
</div>
@endsection

@section('modals')
<!-- Modal Proyecto -->
<div class="modal fade" id="modalProject" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProjectTitle">Nuevo Proyecto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="projectForm" onsubmit="saveProject(event)">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre del Proyecto *</label>
                            <input type="text" class="form-control" id="projectName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cliente *</label>
                            <input type="text" class="form-control" id="projectClient" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de Inicio *</label>
                            <input type="date" class="form-control" id="projectStartDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado *</label>
                            <select class="form-select" id="projectStatus" required>
                                <option value="active">Activo</option>
                                <option value="paused">Pausado</option>
                                <option value="completed">Completado</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Presupuesto Inicial *</label>
                            <input type="number" class="form-control" id="projectBudgetInitial" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Supervisor</label>
                            <select class="form-select" id="projectSupervisor">
                                <option value="">Sin asignar</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" id="projectDescription" rows="3"></textarea>
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

<!-- Modal Personal -->
<div class="modal fade" id="modalWorker" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalWorkerTitle">Agregar Personal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="workerForm" onsubmit="saveWorker(event)">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" id="workerName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" id="workerEmail" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono *</label>
                            <input type="tel" class="form-control" id="workerPhone" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rol *</label>
                            <select class="form-select" id="workerRole" required>
                                <option value="trabajador">Trabajador</option>
                                <option value="supervisor">Supervisor</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Proyecto Asignado</label>
                            <select class="form-select" id="workerProject">
                                <option value="">Sin asignar</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Especialidad</label>
                            <input type="text" class="form-control" id="workerSpecialty">
                        </div>
                        <div class="col-12" id="passwordField" style="display:none;">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="text" class="form-control" id="workerPassword">
                            <small class="text-muted">Dejar en blanco para mantener la actual</small>
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
@endsection

@section('scripts')
<script src="{{ asset('js/admin-dashboard.js') }}"></script>
@endsection