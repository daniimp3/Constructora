<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Supervisor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0d273d;
            --secondary: #3e6985;
            --accent: #8aa7bc;
        }
        
        * { font-family: 'Poppins', sans-serif; }
        body { background: #f0f4f8; }
        
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: linear-gradient(180deg, var(--primary) 0%, var(--secondary) 100%);
            z-index: 1000;
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
            overflow-y: auto;
        }
        
        .logo-container {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.2);
        }
        
        .company-logo {
            max-width: 180px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        
        .company-subtitle {
            color: rgba(255,255,255,0.7);
            text-align: center;
            font-size: 0.85rem;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 10px;
            transition: all 0.3s;
            padding: 12px 20px;
            margin: 5px 0;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
            min-height: 100vh;
        }
        
        .stat-card {
            transition: transform 0.3s;
            cursor: pointer;
            border-left: 4px solid;
        }
        
        .stat-card:hover { transform: translateY(-5px); }
        
        .stat-card.blue { border-color: #3b82f6; }
        .stat-card.green { border-color: #10b981; }
        .stat-card.yellow { border-color: #f59e0b; }
        .stat-card.red { border-color: #ef4444; }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--secondary), #5a8caf);
            border: none;
            color: white;
        }
        
        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #2d5166, var(--secondary));
        }
        
        .btn-action {
            width: 38px;
            height: 38px;
            padding: 0;
        }
        
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-in-progress { background: #dbeafe; color: #1e40af; }
        .badge-completed { background: #dcfce7; color: #166534; }
        .badge-low { background: #dbeafe; color: #1e40af; }
        .badge-medium { background: #fef3c7; color: #92400e; }
        .badge-high { background: #fed7aa; color: #9a3412; }
        .badge-urgent { background: #fee2e2; color: #991b1b; }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-paused { background: #fef3c7; color: #92400e; }
        
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
        
        .evidence-card {
            transition: transform 0.3s;
        }
        
        .evidence-card:hover {
            transform: scale(1.05);
        }
        
        .material-item {
            border-left: 3px solid var(--accent);
            transition: all 0.3s;
        }
        
        .material-item:hover {
            border-left-color: var(--secondary);
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .notification-item {
            border-left: 4px solid #ef4444;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .notification-item:hover {
            background: #fef2f2;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .notification-item.read {
            border-left-color: #cbd5e1;
            opacity: 0.7;
        }
        
        .custom-alert-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 9999;
            backdrop-filter: blur(5px);
        }
        
        .custom-alert-overlay.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .custom-alert-box {
            background: white;
            border-radius: 20px;
            padding: 35px;
            max-width: 450px;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideIn 0.3s;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .progress { height: 10px; border-radius: 5px; }
        .modal-content { border-radius: 15px; border: none; }
        .modal-header {
            background: var(--secondary);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.2rem rgba(62, 105, 133, 0.25);
        }

        .tab-button {
            padding: 10px 20px;
            border: none;
            background: #e5e7eb;
            color: #6b7280;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            margin-right: 10px;
        }
        
        .tab-button.active {
            background: var(--secondary);
            color: white;
        }
        
        .tab-button:hover:not(.active) {
            background: #d1d5db;
        }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-280px); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-container">
            <img src="{{ asset('img/logo.png') }}" alt="Logo Empresa" class="company-logo">
            <p class="company-subtitle mb-0">Panel de Supervisor</p>
        </div>
        
        <div class="bg-dark bg-opacity-25 m-3 p-3 rounded">
            <div class="d-flex align-items-center">
                <div class="bg-gradient rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                     style="width:45px;height:45px;background:linear-gradient(135deg,#3b82f6,#8b5cf6)">
                    {{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 2)) : 'SU' }}
                </div>
                <div class="ms-3">
                    <div class="text-white fw-semibold">{{ Auth::check() ? Auth::user()->name : 'Supervisor' }}</div>
                    <small class="text-white-50">Supervisor</small>
                </div>
            </div>
        </div>
        
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
        
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Vista General -->
        <div id="overview" class="content-section">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="mb-1">Panel de Supervisor</h2>
                    <p class="text-muted mb-0">Gestión de proyectos y equipos de trabajo</p>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card stat-card blue shadow-sm h-100" onclick="showSection('projects')">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h3 class="mb-0 fw-bold" id="totalProjects">0</h3>
                                    <small class="text-muted">Proyectos Asignados</small>
                                </div>
                                <div class="bg-primary bg-opacity-10 rounded p-2">
                                    <i class="bi bi-building fs-4 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card stat-card green shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h3 class="mb-0 fw-bold" id="totalTasks">0</h3>
                                    <small class="text-muted">Tareas Totales</small>
                                </div>
                                <div class="bg-success bg-opacity-10 rounded p-2">
                                    <i class="bi bi-list-task fs-4 text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card stat-card yellow shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h3 class="mb-0 fw-bold" id="completedTasks">0</h3>
                                    <small class="text-muted">Tareas Completadas</small>
                                </div>
                                <div class="bg-warning bg-opacity-10 rounded p-2">
                                    <i class="bi bi-check-circle fs-4 text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card stat-card red shadow-sm h-100" onclick="showSection('notifications')">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h3 class="mb-0 fw-bold" id="pendingNotifications">0</h3>
                                    <small class="text-muted">Notificaciones</small>
                                </div>
                                <div class="bg-danger bg-opacity-10 rounded p-2">
                                    <i class="bi bi-bell fs-4 text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Projects -->
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mis Proyectos Recientes</h5>
                    <button class="btn btn-sm btn-outline-secondary" onclick="showSection('projects')">
                        Ver todos
                    </button>
                </div>
                <div class="card-body" id="recentProjects">
                    <p class="text-muted text-center py-4">No hay proyectos asignados</p>
                </div>
            </div>
        </div>

        <!-- Sección de Proyectos -->
        <div id="projects" class="content-section d-none">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="mb-1">Mis Proyectos</h2>
                    <p class="text-muted mb-0">Gestiona tus proyectos asignados</p>
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
                        <span class="badge badge-active" id="projectDetailStatus">Activo</span>
                    </div>
                </div>
            </div>
            
            <!-- Project Info -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <small class="text-muted">Avance del Proyecto</small>
                            <h4 class="mt-2 mb-3"><span id="currentProgress">0</span>%</h4>
                            <div class="input-group">
                                <input type="number" class="form-control form-control-sm" id="newProgress" min="0" max="100" placeholder="0-100">
                                <button class="btn btn-sm btn-primary-custom" onclick="updateProgress()">
                                    <i class="bi bi-check"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <small class="text-muted">Tareas del Proyecto</small>
                            <h4 class="mt-2 mb-0" id="projectTotalTasks">0</h4>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <small class="text-muted">Tareas Completadas</small>
                            <h4 class="mt-2 mb-0 text-success" id="projectCompletedTasks">0</h4>
                            <small class="text-muted">Finalizadas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <small class="text-muted">Presupuesto</small>
                            <h4 class="mt-2 mb-0" id="projectBudget">$0</h4>
                            <small class="text-muted">Asignado</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabs -->
            <div class="mb-4">
                <button class="tab-button active" onclick="showProjectTab('tasks')">
                    <i class="bi bi-list-task me-2"></i>Tareas
                </button>
                <button class="tab-button" onclick="showProjectTab('evidences')">
                    <i class="bi bi-camera me-2"></i>Evidencias
                </button>
                <button class="tab-button" onclick="showProjectTab('materials')">
                    <i class="bi bi-box-seam me-2"></i>Materiales
                </button>
            </div>
            
            <!-- Tab: Tareas -->
            <div id="tab-tasks" class="project-tab">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-list-task me-2"></i>Tareas del Proyecto</h5>
                            <button class="btn btn-primary-custom" onclick="openCreateTaskModal()">
                                <i class="bi bi-plus-circle me-1"></i>Nueva Tarea
                            </button>
                        </div>
                    </div>
                </div>
                <div id="tasksList"></div>
                <div id="emptyTasks" class="card shadow-sm text-center d-none">
                    <div class="card-body py-5">
                        <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                        <h5>Sin Tareas</h5>
                        <p class="text-muted">Agrega la primera tarea del proyecto</p>
                        <button class="btn btn-primary-custom" onclick="openCreateTaskModal()">
                            <i class="bi bi-plus-circle me-1"></i>Crear Tarea
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Tab: Evidencias -->
            <div id="tab-evidences" class="project-tab d-none">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-camera me-2"></i>Evidencias del Proyecto</h5>
                            <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalEvidence" onclick="openCreateEvidenceModal()">
                                <i class="bi bi-camera me-1"></i>Subir Evidencia
                            </button>
                        </div>
                    </div>
                </div>
                <div id="evidencesList" class="row g-3"></div>
                <div id="emptyEvidences" class="card shadow-sm text-center d-none">
                    <div class="card-body py-5">
                        <i class="bi bi-camera-fill display-1 text-muted mb-3"></i>
                        <h5>Sin Evidencias</h5>
                        <p class="text-muted">Sube fotos del avance del proyecto</p>
                        <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalEvidence" onclick="openCreateEvidenceModal()">
                            <i class="bi bi-camera me-1"></i>Subir Evidencia
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Tab: Materiales -->
            <div id="tab-materials" class="project-tab d-none">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Materiales del Proyecto</h5>
                            <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalMaterial" onclick="openCreateMaterialModal()">
                                <i class="bi bi-plus-circle me-1"></i>Registrar Material
                            </button>
                        </div>
                    </div>
                </div>
                <div id="materialsList" class="row g-3"></div>
                <div id="emptyMaterials" class="card shadow-sm text-center d-none">
                    <div class="card-body py-5">
                        <i class="bi bi-box-seam display-1 text-muted mb-3"></i>
                        <h5>Sin Materiales</h5>
                        <p class="text-muted">Registra los materiales utilizados</p>
                        <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalMaterial" onclick="openCreateMaterialModal()">
                            <i class="bi bi-plus-circle me-1"></i>Registrar Material
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Notificaciones -->
        <div id="notifications" class="content-section d-none">
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1">Problemas Reportados</h2>
                        <p class="text-muted mb-0">Notificaciones de trabajadores</p>
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
                    <h3>Sin Notificaciones</h3>
                    <p class="text-muted">No hay problemas reportados</p>
                </div>
            </div>
        </div>


         <!-- Sección de Asistencias -->
<div id="attendance" class="content-section d-none">
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2 class="mb-1">Control de Asistencias</h2>
                <p class="text-muted mb-0">Registro diario del personal</p>
            </div>
            <div>
                <input type="date" class="form-control" id="attendanceDate" onchange="loadAttendance()">
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Trabajador</th>
                            <th>Proyecto</th>
                            <th>Estado</th>
                            <th>Hora Entrada</th>
                            <th>Notas</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="attendanceTable">
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
    <!-- MODALES-->

    <!-- Modal de Tarea -->
    <div class="modal fade" id="modalTask" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTaskTitle">
                        <i class="bi bi-plus-circle me-2"></i>Nueva Tarea
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="taskForm" onsubmit="return saveTask(event)">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Título de la Tarea *</label>
                                <input type="text" class="form-control" id="taskTitle" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Asignar a *</label>
                                <select class="form-select" id="taskWorker" required>
                                    <option value="">Seleccionar trabajador...</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Prioridad *</label>
                                <select class="form-select" id="taskPriority" required>
                                    <option value="low">Baja</option>
                                    <option value="medium" selected>Media</option>
                                    <option value="high">Alta</option>
                                    <option value="urgent">Urgente</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Estado</label>
                                <select class="form-select" id="taskStatus">
                                    <option value="pending">Pendiente</option>
                                    <option value="in-progress">En Progreso</option>
                                    <option value="completed">Completada</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fecha Límite *</label>
                                <input type="date" class="form-control" id="taskDeadline" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Descripción *</label>
                                <textarea class="form-control" id="taskDescription" rows="3" required></textarea>
                            </div>
                        </div>
                        <input type="hidden" id="taskId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-save me-2"></i>Guardar Tarea
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Evidencia -->
    <div class="modal fade" id="modalEvidence" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-camera me-2"></i>Subir Evidencia
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="evidenceForm" onsubmit="return saveEvidence(event)">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Título *</label>
                            <input type="text" class="form-control" id="evidenceTitle" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" id="evidenceDescription" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subir Foto *</label>
                            <input type="file" class="form-control" id="evidencePhoto" accept="image/*" required>
                            <small class="text-muted">Formatos: JPG, PNG. Máximo 5MB</small>
                        </div>
                        <div id="evidencePreview" class="mb-3 d-none">
                            <img id="evidencePreviewImg" class="img-fluid rounded" style="max-height: 300px;">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-upload me-2"></i>Subir Evidencia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Material -->
    <div class="modal fade" id="modalMaterial" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-box-seam me-2"></i>Registrar Material
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="materialForm" onsubmit="return saveMaterial(event)">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Material *</label>
                            <input type="text" class="form-control" id="materialName" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Cantidad *</label>
                                <input type="number" class="form-control" id="materialQuantity" step="0.01" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Unidad *</label>
                                <select class="form-select" id="materialUnit" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="kg">Kilogramos (kg)</option>
                                    <option value="m">Metros (m)</option>
                                    <option value="m2">Metros cuadrados (m²)</option>
                                    <option value="m3">Metros cúbicos (m³)</option>
                                    <option value="pza">Piezas</option>
                                    <option value="lt">Litros</option>
                                    <option value="ton">Toneladas</option>
                                    <option value="bulto">Bultos</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Costo Unitario</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="materialCost" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Proveedor</label>
                            <input type="text" class="form-control" id="materialSupplier">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notas</label>
                            <textarea class="form-control" id="materialNotes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-save me-2"></i>Guardar Material
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

   <!-- Modal de Asistencia -->
<div class="modal fade" id="modalAttendance" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-calendar-check me-2"></i>Registrar Asistencia
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="attendanceForm" onsubmit="return saveAttendance(event)">
                <div class="modal-body">
                    <input type="hidden" id="attendanceWorkerId">
                    <div class="mb-3">
                        <label class="form-label">Trabajador</label>
                        <input type="text" class="form-control" id="attendanceWorkerName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado *</label>
                        <select class="form-select" id="attendanceStatus" required>
                            <option value="present">Presente</option>
                            <option value="absent">Ausente</option>
                            <option value="late">Retardo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hora de Entrada</label>
                        <input type="time" class="form-control" id="attendanceTime">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <textarea class="form-control" id="attendanceNotes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-save me-2"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Modal Ver Notificación -->
    <div class="modal fade" id="modalNotification" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle me-2"></i>Detalle del Problema
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="notificationContent"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    <div class="custom-alert-overlay" id="customAlert">
        <div class="custom-alert-box text-center">
            <div class="mb-3" id="alertIcon"></div>
            <h4 id="alertTitle" class="mb-2"></h4>
            <p id="alertMessage" class="text-muted mb-4"></p>
            <div id="alertActions" class="d-flex gap-2 justify-content-center"></div>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="toast" class="toast" role="alert">
            <div class="toast-header">
                <span id="toastIcon" class="me-2"></span>
                <strong class="me-auto" id="toastTitle"></strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body" id="toastMessage"></div>
        </div>
    </div>
<script>
    // Esto crea la variable global que tu JS puede usar
    let workers = @json($workers);
window.workers = workers;
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/supervisor-dashboard.js') }}"></script>
</body>
</html>