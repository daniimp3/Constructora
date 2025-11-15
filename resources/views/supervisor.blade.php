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
                    {{ Auth::check() ? substr(Auth::user()->name, 0, 2) : 'SU' }}
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
                            <tbody id="attendanceTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                <form id="taskForm" onsubmit="saveTask(event)">
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
                <form id="evidenceForm" onsubmit="saveEvidence(event)">
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
                <form id="materialForm" onsubmit="saveMaterial(event)">
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
                <form id="attendanceForm" onsubmit="saveAttendance(event)">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
// ============== VARIABLES GLOBALES ==============
let projects = [];
let workers = [];
let problems = [];
let attendance = [];
let currentSupervisorId = 1; // En producción vendría de la sesión
let currentProjectId = null;
let currentTaskId = null;

// ============== INICIALIZACIÓN ==============
document.addEventListener('DOMContentLoaded', () => {
    loadAllData();
    updateUI();
    setTodayDate();
    
    document.getElementById('evidencePhoto').addEventListener('change', previewEvidencePhoto);
});

function loadAllData() {
    projects = JSON.parse(localStorage.getItem('constructora_projects')) || [];
    workers = JSON.parse(localStorage.getItem('constructora_workers')) || [];
    problems = JSON.parse(localStorage.getItem('worker_problems')) || [];
    attendance = JSON.parse(localStorage.getItem('supervisor_attendance')) || [];
}

function saveData(key, data) {
    localStorage.setItem(key, JSON.stringify(data));
}

function setTodayDate() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('attendanceDate').value = today;
}

// ============== NAVEGACIÓN ==============
function showSection(id) {
    document.querySelectorAll('.content-section').forEach(s => s.classList.add('d-none'));
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    document.getElementById(id).classList.remove('d-none');
    
    const links = document.querySelectorAll('.nav-link');
    links.forEach(link => {
        if (link.getAttribute('onclick') && link.getAttribute('onclick').includes(`'${id}'`)) {
            link.classList.add('active');
        }
    });
    
    if (id === 'projects') loadProjects();
    if (id === 'notifications') loadNotifications();
    if (id === 'attendance') loadAttendance();
}

function showProjectTab(tabName) {
    document.querySelectorAll('.project-tab').forEach(t => t.classList.add('d-none'));
    document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
    
    document.getElementById(`tab-${tabName}`).classList.remove('d-none');
    event.target.classList.add('active');
    
    if (tabName === 'tasks') loadProjectTasks();
    if (tabName === 'evidences') loadProjectEvidences();
    if (tabName === 'materials') loadProjectMaterials();
}

// ============== UI UPDATES ==============
function updateUI() {
    updateStats();
    updateRecentProjects();
    updateNotificationBadge();
}

function updateStats() {
    const myProjects = projects.filter(p => p.supervisorId === currentSupervisorId);
    const totalProjects = myProjects.length;
    
    let totalTasks = 0;
    let completedTasks = 0;
    myProjects.forEach(p => {
        if (p.tasks) {
            totalTasks += p.tasks.length;
            completedTasks += p.tasks.filter(t => t.status === 'completed').length;
        }
    });
    
    const myNotifications = problems.filter(p => {
        const project = projects.find(pr => pr.id === p.projectId);
        return project && project.supervisorId === currentSupervisorId;
    });
    const unreadNotifications = myNotifications.filter(n => !n.read).length;
    
    document.getElementById('totalProjects').textContent = totalProjects;
    document.getElementById('totalTasks').textContent = totalTasks;
    document.getElementById('completedTasks').textContent = completedTasks;
    document.getElementById('pendingNotifications').textContent = unreadNotifications;
}

function updateNotificationBadge() {
    const myNotifications = problems.filter(p => {
        const project = projects.find(pr => pr.id === p.projectId);
        return project && project.supervisorId === currentSupervisorId;
    });
    const unreadCount = myNotifications.filter(n => !n.read).length;
    
    const badge = document.getElementById('notificationBadge');
    if (unreadCount > 0) {
        badge.textContent = unreadCount;
        badge.style.display = 'inline-block';
    } else {
        badge.style.display = 'none';
    }
}

function updateRecentProjects() {
    const container = document.getElementById('recentProjects');
    const myProjects = projects.filter(p => p.supervisorId === currentSupervisorId);
    
    if (myProjects.length === 0) {
        container.innerHTML = '<p class="text-muted text-center py-4">No hay proyectos asignados</p>';
        return;
    }
    
    let html = '';
    myProjects.slice(0, 3).forEach(project => {
        const progress = project.progress || 0;
        html += `
            <div class="project-card card mb-3" onclick="viewProjectDetail(${project.id})">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0">${project.name}</h6>
                        ${getStatusBadge(project.status)}
                    </div>
                    <p class="text-muted small mb-2">Cliente: ${project.client}</p>
                    <div class="d-flex align-items-center">
                        <span class="me-2">${progress}%</span>
                        <div class="progress flex-grow-1" style="height:8px">
                            <div class="progress-bar" style="width:${progress}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// ============== PROYECTOS ==============
function loadProjects() {
    const myProjects = projects.filter(p => p.supervisorId === currentSupervisorId);
    const container = document.getElementById('projectsList');
    const empty = document.getElementById('emptyProjects');
    
    if (myProjects.length === 0) {
        container.innerHTML = '';
        empty.classList.remove('d-none');
    } else {
        empty.classList.add('d-none');
        container.innerHTML = '';
        
        myProjects.forEach(project => {
            const col = document.createElement('div');
            col.className = 'col-md-6';
            const progress = project.progress || 0;
            const tasksCount = project.tasks ? project.tasks.length : 0;
            const completedTasksCount = project.tasks ? project.tasks.filter(t => t.status === 'completed').length : 0;
            
            col.innerHTML = `
                <div class="project-card card shadow-sm" onclick="viewProjectDetail(${project.id})">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">${project.name}</h5>
                                <small class="text-muted">${project.client}</small>
                            </div>
                            ${getStatusBadge(project.status)}
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Avance del Proyecto</small>
                            <div class="d-flex align-items-center">
                                <span class="me-2"><strong>${progress}%</strong></span>
                                <div class="progress flex-grow-1" style="height:10px">
                                    <div class="progress-bar" style="width:${progress}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between text-muted small">
                            <span><i class="bi bi-list-task me-1"></i>${tasksCount} tareas</span>
                            <span><i class="bi bi-check-circle me-1"></i>${completedTasksCount} completadas</span>
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(col);
        });
    }
}

function viewProjectDetail(id) {
    const project = projects.find(p => p.id === id);
    if (!project) return;
    
    currentProjectId = id;
    
    document.getElementById('projectDetailTitle').textContent = project.name;
    document.getElementById('projectDetailClient').textContent = 'Cliente: ' + project.client;
    document.getElementById('projectDetailStatus').textContent = getStatusText(project.status);
    document.getElementById('projectDetailStatus').className = 'badge ' + getStatusBadgeClass(project.status);
    
    const progress = project.progress || 0;
    document.getElementById('currentProgress').textContent = progress;
    document.getElementById('newProgress').value = '';
    
    const budget = parseFloat(project.budget) || 0;
    document.getElementById('projectBudget').textContent = '$' + budget.toLocaleString('es-MX');
    
    const totalTasks = project.tasks ? project.tasks.length : 0;
    const completedTasks = project.tasks ? project.tasks.filter(t => t.status === 'completed').length : 0;
    document.getElementById('projectTotalTasks').textContent = totalTasks;
    document.getElementById('projectCompletedTasks').textContent = completedTasks;
    
    showSection('project-detail');
    showProjectTab('tasks');
}

function updateProgress() {
    const newProgress = parseInt(document.getElementById('newProgress').value);
    
    if (isNaN(newProgress) || newProgress < 0 || newProgress > 100) {
        showToast('warning', 'Atención', 'El avance debe estar entre 0 y 100');
        return;
    }
    
    const project = projects.find(p => p.id === currentProjectId);
    if (!project) return;
    
    project.progress = newProgress;
    saveData('constructora_projects', projects);
    
    document.getElementById('currentProgress').textContent = newProgress;
    document.getElementById('newProgress').value = '';
    
    showToast('success', 'Actualizado', 'Avance del proyecto actualizado');
    updateUI();
    loadProjects();
}

// ============== TAREAS ==============
function loadProjectTasks() {
    const project = projects.find(p => p.id === currentProjectId);
    if (!project) return;
    
    const container = document.getElementById('tasksList');
    const empty = document.getElementById('emptyTasks');
    
    if (!project.tasks || project.tasks.length === 0) {
        container.innerHTML = '';
        empty.classList.remove('d-none');
        return;
    }
    
    empty.classList.add('d-none');
    container.innerHTML = '';
    
    project.tasks.forEach(task => {
        const worker = workers.find(w => w.id === task.workerId);
        const card = document.createElement('div');
        card.className = `task-item card mb-2 ${task.status === 'completed' ? 'completed' : ''}`;
        
        card.innerHTML = `
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${task.title}</h6>
                        <small class="text-muted">${task.description}</small>
                        <div class="mt-2">
                            <small class="text-muted">Asignado a: <strong>${worker?.name || 'N/A'}</strong></small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 align-items-start">
                        ${getTaskPriorityBadge(task.priority)}
                        ${getTaskStatusBadge(task.status)}
                        <div class="btn-group">
                            <button class="btn btn-sm btn-warning btn-action" onclick="event.stopPropagation(); editTask(${task.id})" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-action" onclick="event.stopPropagation(); confirmDeleteTask(${task.id})" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="bi bi-calendar me-1"></i>Vence: ${new Date(task.deadline).toLocaleDateString('es-MX')}
                    </small>
                </div>
            </div>
        `;
        
        container.appendChild(card);
    });
}

function openCreateTaskModal() {
    document.getElementById('modalTaskTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Nueva Tarea';
    document.getElementById('taskForm').reset();
    currentTaskId = null;
    
    const project = projects.find(p => p.id === currentProjectId);
    const select = document.getElementById('taskWorker');
    select.innerHTML = '<option value="">Seleccionar trabajador...</option>';
    
    const projectWorkers = workers.filter(w => w.projectId === currentProjectId && w.role === 'trabajador');
    projectWorkers.forEach(w => {
        select.innerHTML += `<option value="${w.id}">${w.name}</option>`;
    });
    
    new bootstrap.Modal(document.getElementById('modalTask')).show();
}

function saveTask(e) {
    e.preventDefault();
    
    const project = projects.find(p => p.id === currentProjectId);
    if (!project) return;
    
    if (!project.tasks) project.tasks = [];
    
    const data = {
        title: document.getElementById('taskTitle').value,
        workerId: parseInt(document.getElementById('taskWorker').value),
        priority: document.getElementById('taskPriority').value,
        status: document.getElementById('taskStatus').value,
        deadline: document.getElementById('taskDeadline').value,
        description: document.getElementById('taskDescription').value,
        createdAt: new Date().toISOString()
    };
    
    if (currentTaskId) {
        const index = project.tasks.findIndex(t => t.id === currentTaskId);
        project.tasks[index] = { ...project.tasks[index], ...data };
        showToast('success', 'Actualizado', 'Tarea actualizada correctamente');
    } else {
        data.id = Date.now();
        project.tasks.push(data);
        showToast('success', 'Creado', 'Tarea creada correctamente');
    }
    
    saveData('constructora_projects', projects);
    bootstrap.Modal.getInstance(document.getElementById('modalTask')).hide();
    loadProjectTasks();
    updateUI();
    viewProjectDetail(currentProjectId);
}

function editTask(id) {
    const project = projects.find(p => p.id === currentProjectId);
    if (!project || !project.tasks) return;
    
    const task = project.tasks.find(t => t.id === id);
    if (!task) return;
    
    currentTaskId = id;
    document.getElementById('modalTaskTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Tarea';
    document.getElementById('taskTitle').value = task.title;
    document.getElementById('taskWorker').value = task.workerId;
    document.getElementById('taskPriority').value = task.priority;
    document.getElementById('taskStatus').value = task.status;
    document.getElementById('taskDeadline').value = task.deadline;
    document.getElementById('taskDescription').value = task.description;
    
    const select = document.getElementById('taskWorker');
    select.innerHTML = '<option value="">Seleccionar trabajador...</option>';
    const projectWorkers = workers.filter(w => w.projectId === currentProjectId && w.role === 'trabajador');
    projectWorkers.forEach(w => {
        select.innerHTML += `<option value="${w.id}">${w.name}</option>`;
    });
    
    new bootstrap.Modal(document.getElementById('modalTask')).show();
}

function confirmDeleteTask(id) {
    const project = projects.find(p => p.id === currentProjectId);
    if (!project || !project.tasks) return;
    
    const task = project.tasks.find(t => t.id === id);
    if (!task) return;
    
    showCustomAlert(
        '<i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:60px"></i>',
        'Confirmar Eliminación',
        `¿Eliminar la tarea "${task.title}"?`,
        [
            { text: 'Cancelar', class: 'btn-secondary', action: closeCustomAlert },
            { text: 'Eliminar', class: 'btn-danger', action: () => deleteTask(id) }
        ]
    );
}

function deleteTask(id) {
    const project = projects.find(p => p.id === currentProjectId);
    if (!project || !project.tasks) return;
    
    project.tasks = project.tasks.filter(t => t.id !== id);
    
    saveData('constructora_projects', projects);
    closeCustomAlert();
    showToast('success', 'Eliminado', 'Tarea eliminada correctamente');
    loadProjectTasks();
    updateUI();
    viewProjectDetail(currentProjectId);
}

function getTaskStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge badge-pending">Pendiente</span>',
        'in-progress': '<span class="badge badge-in-progress">En Progreso</span>',
        'completed': '<span class="badge badge-completed">Completada</span>'
    };
    return badges[status] || badges.pending;
}

function getTaskPriorityBadge(priority) {
    const badges = {
        'low': '<span class="badge badge-low">Baja</span>',
        'medium': '<span class="badge badge-medium">Media</span>',
        'high': '<span class="badge badge-high">Alta</span>',
        'urgent': '<span class="badge badge-urgent">Urgente</span>'
    };
    return badges[priority] || badges.medium;
}

function getStatusBadge(status) {
    const badges = {
        'active': '<span class="badge badge-active">Activo</span>',
        'paused': '<span class="badge badge-paused">Pausado</span>',
        'completed': '<span class="badge badge-completed">Completado</span>'
    };
    return badges[status] || badges.active;
}

function getStatusText(status) {
    const texts = {
        'active': 'Activo',
        'paused': 'Pausado',
        'completed': 'Completado'
    };
    return texts[status] || 'Activo';
}

function getStatusBadgeClass(status) {
    const classes = {
        'active': 'badge-active',
        'paused': 'badge-paused',
        'completed': 'badge-completed'
    };
    return classes[status] || 'badge-active';
}

// ============== EVIDENCIAS ==============
function loadProjectEvidences() {
    const project = projects.find(p => p.id === currentProjectId);
    if (!project) return;
    
    const container = document.getElementById('evidencesList');
    const empty = document.getElementById('emptyEvidences');
    
    if (!project.evidences || project.evidences.length === 0) {
        container.innerHTML = '';
        empty.classList.remove('d-none');
        return;
    }
    
    empty.classList.add('d-none');
    container.innerHTML = '';
    
    project.evidences.forEach(evidence => {
        const col = document.createElement('div');
        col.className = 'col-md-4';
        col.innerHTML = `
            <div class="card shadow-sm h-100 evidence-card">
                <img src="${evidence.photo}" class="card-img-top" style="height:200px;object-fit:cover;" alt="${evidence.title}">
                <div class="card-body">
                    <h6 class="card-title">${evidence.title}</h6>
                    <p class="card-text text-muted small">${evidence.description || 'Sin descripción'}</p>
                    <small class="text-muted"><i class="bi bi-calendar me-1"></i>${new Date(evidence.date).toLocaleDateString('es-MX')}</small>
                </div>
                <div class="card-footer bg-white">
                    <button class="btn btn-sm btn-danger w-100" onclick="confirmDeleteEvidence(${evidence.id})">
                        <i class="bi bi-trash me-1"></i>Eliminar
                    </button>
                </div>
            </div>
        `;
        container.appendChild(col);
    });
}

function openCreateEvidenceModal() {
    document.getElementById('evidenceForm').reset();
    document.getElementById('evidencePreview').classList.add('d-none');
}

function previewEvidencePhoto(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('evidencePreviewImg').src = event.target.result;
            document.getElementById('evidencePreview').classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
}

function saveEvidence(e) {
    e.preventDefault();
    
    const project = projects.find(p => p.id === currentProjectId);
    if (!project) return;
    
    if (!project.evidences) project.evidences = [];
    
    const fileInput = document.getElementById('evidencePhoto');
    const file = fileInput.files[0];
    
    if (!file) {
        showToast('warning', 'Atención', 'Selecciona una foto');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(event) {
        const data = {
            id: Date.now(),
            title: document.getElementById('evidenceTitle').value,
            description: document.getElementById('evidenceDescription').value,
            photo: event.target.result,
            date: new Date().toISOString()
        };
        
        project.evidences.push(data);
        saveData('constructora_projects', projects);
        
        bootstrap.Modal.getInstance(document.getElementById('modalEvidence')).hide();
        showToast('success', 'Subido', 'Evidencia subida correctamente');
        loadProjectEvidences();
    };
    reader.readAsDataURL(file);
}

function confirmDeleteEvidence(id) {
    showCustomAlert(
        '<i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:60px"></i>',
        'Confirmar Eliminación',
        '¿Eliminar esta evidencia?',
        [
            { text: 'Cancelar', class: 'btn-secondary', action: closeCustomAlert },
            { text: 'Eliminar', class: 'btn-danger', action: () => deleteEvidence(id) }
        ]
    );
}

function deleteEvidence(id) {
    const project = projects.find(p => p.id === currentProjectId);
    if (!project || !project.evidences) return;
    
    project.evidences = project.evidences.filter(e => e.id !== id);
    saveData('constructora_projects', projects);
    closeCustomAlert();
    showToast('success', 'Eliminado', 'Evidencia eliminada correctamente');
    loadProjectEvidences();
}

// ============== MATERIALES ==============
function loadProjectMaterials() {
    const project = projects.find(p => p.id === currentProjectId);
    if (!project) return;
    
    const container = document.getElementById('materialsList');
    const empty = document.getElementById('emptyMaterials');
    
    if (!project.materials || project.materials.length === 0) {
        container.innerHTML = '';
        empty.classList.remove('d-none');
        return;
    }
    
    empty.classList.add('d-none');
    container.innerHTML = '';
    
    project.materials.forEach(material => {
        const col = document.createElement('div');
        col.className = 'col-md-6';
        const totalCost = (parseFloat(material.quantity) * parseFloat(material.cost || 0)).toFixed(2);
        
        col.innerHTML = `
            <div class="card material-item shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0">${material.name}</h6>
                        <button class="btn btn-sm btn-danger btn-action" onclick="confirmDeleteMaterial(${material.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <small class="text-muted">Cantidad:</small><br>
                            <strong>${material.quantity} ${material.unit}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Costo Total:</small><br>
                            <strong class="text-primary">$${parseFloat(totalCost).toLocaleString('es-MX')}</strong>
                        </div>
                        ${material.supplier ? `
                        <div class="col-12">
                            <small class="text-muted">Proveedor:</small><br>
                            <span>${material.supplier}</span>
                        </div>
                        ` : ''}
                        ${material.notes ? `
                        <div class="col-12">
                            <small class="text-muted">Notas:</small><br>
                            <span class="small">${material.notes}</span>
                        </div>
                        ` : ''}
                        <div class="col-12">
                            <small class="text-muted"><i class="bi bi-calendar me-1"></i>${new Date(material.date).toLocaleDateString('es-MX')}</small>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(col);
    });
}

function openCreateMaterialModal() {
    document.getElementById('materialForm').reset();
}

function saveMaterial(e) {
    e.preventDefault();
    
    const project = projects.find(p => p.id === currentProjectId);
    if (!project) return;
    
    if (!project.materials) project.materials = [];
    
    const data = {
        id: Date.now(),
        name: document.getElementById('materialName').value,
        quantity: document.getElementById('materialQuantity').value,
        unit: document.getElementById('materialUnit').value,
        cost: document.getElementById('materialCost').value || 0,
        supplier: document.getElementById('materialSupplier').value,
        notes: document.getElementById('materialNotes').value,
        date: new Date().toISOString()
    };
    
    project.materials.push(data);
    saveData('constructora_projects', projects);
    
    bootstrap.Modal.getInstance(document.getElementById('modalMaterial')).hide();
    showToast('success', 'Registrado', 'Material registrado correctamente');
    loadProjectMaterials();
}

function confirmDeleteMaterial(id) {
    showCustomAlert(
        '<i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:60px"></i>',
        'Confirmar Eliminación',
        '¿Eliminar este material?',
        [
            { text: 'Cancelar', class: 'btn-secondary', action: closeCustomAlert },
            { text: 'Eliminar', class: 'btn-danger', action: () => deleteMaterial(id) }
        ]
    );
}

function deleteMaterial(id) {
    const project = projects.find(p => p.id === currentProjectId);
    if (!project || !project.materials) return;
    
    project.materials = project.materials.filter(m => m.id !== id);
    saveData('constructora_projects', projects);
    closeCustomAlert();
    showToast('success', 'Eliminado', 'Material eliminado correctamente');
    loadProjectMaterials();
}

// ============== NOTIFICACIONES ==============
function loadNotifications() {
    const myNotifications = problems.filter(p => {
        const project = projects.find(pr => pr.id === p.projectId);
        return project && project.supervisorId === currentSupervisorId;
    });
    
    const container = document.getElementById('notificationsList');
    const empty = document.getElementById('emptyNotifications');
    
    if (myNotifications.length === 0) {
        container.innerHTML = '';
        empty.classList.remove('d-none');
        return;
    }
    
    empty.classList.add('d-none');
    container.innerHTML = '';
    
    myNotifications.reverse().forEach(notification => {
        const worker = workers.find(w => w.id === notification.workerId);
        const project = projects.find(p => p.id === notification.projectId);
        
        const card = document.createElement('div');
        card.className = `notification-item card mb-3 ${notification.read ? 'read' : ''}`;
        card.onclick = () => viewNotificationDetail(notification.id);
        
        card.innerHTML = `
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0">${notification.title}</h6>
                    ${getTaskPriorityBadge(notification.priority)}
                </div>
                <p class="text-muted small mb-2">${notification.description}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="bi bi-person me-1"></i>${worker?.name || 'Desconocido'} - ${project?.name || 'N/A'}
                    </small>
                    <small class="text-muted">
                        <i class="bi bi-calendar me-1"></i>${new Date(notification.createdAt).toLocaleDateString('es-MX')}
                    </small>
                </div>
            </div>
        `;
        
        container.appendChild(card);
    });
}

function viewNotificationDetail(id) {
    const notification = problems.find(p => p.id === id);
    if (!notification) return;
    
    const worker = workers.find(w => w.id === notification.workerId);
    const project = projects.find(p => p.id === notification.projectId);
    
    const content = document.getElementById('notificationContent');
    content.innerHTML = `
        <div class="mb-3">
            <label class="text-muted small">Título</label>
            <h6>${notification.title}</h6>
        </div>
        <div class="mb-3">
            <label class="text-muted small">Categoría</label>
            <p>${notification.category}</p>
        </div>
        <div class="mb-3">
            <label class="text-muted small">Prioridad</label>
            <div>${getTaskPriorityBadge(notification.priority)}</div>
        </div>
        ${notification.location ? `
        <div class="mb-3">
            <label class="text-muted small">Ubicación</label>
            <p>${notification.location}</p>
        </div>
        ` : ''}
        <div class="mb-3">
            <label class="text-muted small">Descripción</label>
            <p>${notification.description}</p>
        </div>
        <div class="mb-3">
            <label class="text-muted small">Reportado por</label>
            <p>${worker?.name || 'Desconocido'}</p>
        </div>
        <div class="mb-3">
            <label class="text-muted small">Proyecto</label>
            <p>${project?.name || 'N/A'}</p>
        </div>
        <div class="mb-3">
            <label class="text-muted small">Fecha</label>
            <p>${new Date(notification.createdAt).toLocaleString('es-MX')}</p>
        </div>
    `;
    
    // Marcar como leída
    notification.read = true;
    saveData('worker_problems', problems);
    updateNotificationBadge();
    updateUI();
    
    new bootstrap.Modal(document.getElementById('modalNotification')).show();
}

function markAllAsRead() {
    const myNotifications = problems.filter(p => {
        const project = projects.find(pr => pr.id === p.projectId);
        return project && project.supervisorId === currentSupervisorId;
    });
    
    myNotifications.forEach(n => n.read = true);
    saveData('worker_problems', problems);
    updateNotificationBadge();
    updateUI();
    loadNotifications();
    showToast('success', 'Actualizado', 'Todas las notificaciones marcadas como leídas');
}

// ============== ASISTENCIAS ==============
function loadAttendance() {
    const date = document.getElementById('attendanceDate').value;
    const tbody = document.getElementById('attendanceTable');
    tbody.innerHTML = '';
    
    const myProjects = projects.filter(p => p.supervisorId === currentSupervisorId);
    const myProjectIds = myProjects.map(p => p.id);
    const workersToShow = workers.filter(w => w.role === 'trabajador' && myProjectIds.includes(w.projectId));
    
    workersToShow.forEach(worker => {
        const project = projects.find(p => p.id === worker.projectId);
        const attendanceRecord = attendance.find(a => a.workerId === worker.id && a.date === date);
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><strong>${worker.name}</strong></td>
            <td>${project?.name || 'Sin proyecto'}</td>
            <td>${attendanceRecord ? getAttendanceBadge(attendanceRecord.status) : '<span class="text-muted">Sin registrar</span>'}</td>
            <td>${attendanceRecord?.time || '-'}</td>
            <td>${attendanceRecord?.notes || '-'}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-primary btn-action" onclick="openAttendanceModal(${worker.id}, '${worker.name}', '${date}')">
                    <i class="bi bi-pencil"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function openAttendanceModal(workerId, workerName, date) {
    document.getElementById('attendanceWorkerId').value = workerId;
    document.getElementById('attendanceWorkerName').value = workerName;
    
    const existingRecord = attendance.find(a => a.workerId === workerId && a.date === date);
    if (existingRecord) {
        document.getElementById('attendanceStatus').value = existingRecord.status;
        document.getElementById('attendanceTime').value = existingRecord.time || '';
        document.getElementById('attendanceNotes').value = existingRecord.notes || '';
    } else {
        document.getElementById('attendanceStatus').value = 'present';
        document.getElementById('attendanceTime').value = '';
        document.getElementById('attendanceNotes').value = '';
    }
    
    new bootstrap.Modal(document.getElementById('modalAttendance')).show();
}

function saveAttendance(e) {
    e.preventDefault();
    
    const workerId = parseInt(document.getElementById('attendanceWorkerId').value);
    const date = document.getElementById('attendanceDate').value;
    
    const data = {
        id: Date.now(),
        workerId: workerId,
        date: date,
        status: document.getElementById('attendanceStatus').value,
        time: document.getElementById('attendanceTime').value,
        notes: document.getElementById('attendanceNotes').value
    };
    
    attendance = attendance.filter(a => !(a.workerId === workerId && a.date === date));
    attendance.push(data);
    
    saveData('supervisor_attendance', attendance);
    bootstrap.Modal.getInstance(document.getElementById('modalAttendance')).hide();
    showToast('success', 'Registrado', 'Asistencia registrada correctamente');
    loadAttendance();
}

function getAttendanceBadge(status) {
    const badges = {
        'present': '<span class="badge" style="background:#dcfce7;color:#166534">Presente</span>',
        'absent': '<span class="badge" style="background:#fee2e2;color:#991b1b">Ausente</span>',
        'late': '<span class="badge" style="background:#fef3c7;color:#92400e">Retardo</span>'
    };
    return badges[status] || badges.present;
}

// ============== UTILIDADES ==============
function confirmLogout() {
    showCustomAlert(
        '<i class="bi bi-box-arrow-right text-warning" style="font-size:60px"></i>',
        'Cerrar Sesión',
        '¿Estás seguro de que deseas cerrar sesión?',
        [
            { text: 'Cancelar', class: 'btn-secondary', action: closeCustomAlert },
            { text: 'Cerrar Sesión', class: 'btn-primary-custom', action: logout }
        ]
    );
}

function logout() {
    closeCustomAlert();
    showToast('success', 'Cerrando sesión', 'Hasta pronto...');
    setTimeout(() => {
        document.getElementById('logout-form').submit();
    }, 1500);
}

function showCustomAlert(icon, title, message, actions) {
    document.getElementById('alertIcon').innerHTML = icon;
    document.getElementById('alertTitle').textContent = title;
    document.getElementById('alertMessage').innerHTML = message;
    
    const actionsContainer = document.getElementById('alertActions');
    actionsContainer.innerHTML = '';
    
    actions.forEach(action => {
        const button = document.createElement('button');
        button.className = 'btn ' + action.class;
        button.textContent = action.text;
        button.onclick = action.action;
        actionsContainer.appendChild(button);
    });
    
    document.getElementById('customAlert').classList.add('show');
}

function closeCustomAlert() {
    document.getElementById('customAlert').classList.remove('show');
}

function showToast(type, title, message) {
    const toast = document.getElementById('toast');
    const toastElement = new bootstrap.Toast(toast);
    
    const icons = {
        success: '✅',
        error: '❌',
        warning: '⚠️',
        info: 'ℹ️'
    };
    
    document.getElementById('toastIcon').textContent = icons[type] || icons.info;
    document.getElementById('toastTitle').textContent = title;
    document.getElementById('toastMessage').textContent = message;
    
    toastElement.show();
}
    </script>
</body>
</html>