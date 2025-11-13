<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Supervisor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0d273d;
            --secondary: #3e6985;
            --accent: #8aa7bc;
            --light: #a6bed1;
            --lighter: #cdd7df;
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
            max-width: 80px;
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
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card.blue { border-color: #3b82f6; }
        .stat-card.green { border-color: #10b981; }
        .stat-card.yellow { border-color: #f59e0b; }
        .stat-card.purple { border-color: #8b5cf6; }
        .stat-card.red { border-color: #ef4444; }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--secondary), #5a8caf);
            border: none;
            color: white;
        }
        
        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #2d5166, var(--secondary));
            color: white;
        }
        
        .btn-action {
            width: 38px;
            height: 38px;
            padding: 0;
        }
        
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-in-progress { background: #dbeafe; color: #1e40af; }
        .badge-completed { background: #dcfce7; color: #166534; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        
        .badge-low { background: #dbeafe; color: #1e40af; }
        .badge-medium { background: #fef3c7; color: #92400e; }
        .badge-high { background: #fed7aa; color: #9a3412; }
        .badge-urgent { background: #fee2e2; color: #991b1b; }
        
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
            animation: slideIn 0.3s;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .alert-item {
            border-left: 3px solid #ef4444;
            transition: all 0.3s;
        }
        
        .alert-item:hover {
            background: #fef2f2;
            transform: translateX(5px);
        }
        
        .alert-item.read {
            border-left-color: #d1d5db;
            opacity: 0.7;
        }

        .progress {
            height: 10px;
            border-radius: 5px;
            overflow: hidden;
        }

        .modal-content {
            border-radius: 15px;
            border: none;
        }

        .modal-header {
            background: var(--secondary);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.2rem rgba(62, 105, 133, 0.25);
        }

        .evidence-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
        }

        .attendance-present { background: #dcfce7; color: #166534; }
        .attendance-absent { background: #fee2e2; color: #991b1b; }
        .attendance-late { background: #fef3c7; color: #92400e; }

        .material-item {
            border-left: 3px solid var(--accent);
            transition: all 0.3s;
        }

        .material-item:hover {
            border-left-color: var(--secondary);
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
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
        <!-- Logo Section -->
        <div class="logo-container">
            <img src="{{ asset('img/logo.png') }}" alt="Logo Empresa" class="company-logo">
            <p class="company-subtitle mb-0">Panel de Supervisor</p>
        </div>
        
        <!-- User Profile -->
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
        
        <!-- Navigation -->
        <ul class="nav flex-column px-3 mt-3">
            <li class="nav-item">
                <a class="nav-link active" href="#" onclick="showSection('overview');return false">
                    <i class="bi bi-graph-up me-2"></i>Vista General
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="showSection('tasks');return false">
                    <i class="bi bi-list-task me-2"></i>Tareas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="showSection('evidence');return false">
                    <i class="bi bi-camera me-2"></i>Evidencias
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="showSection('materials');return false">
                    <i class="bi bi-box-seam me-2"></i>Materiales
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="showSection('attendance');return false">
                    <i class="bi bi-calendar-check me-2"></i>Asistencias
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link position-relative" href="#" onclick="showSection('alerts');return false">
                    <i class="bi bi-exclamation-triangle me-2"></i>Alertas
                    <span class="notification-badge d-none" id="alertsBadge">0</span>
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
                    <div class="card stat-card blue shadow-sm h-100" onclick="showSection('tasks')">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h3 class="mb-0 fw-bold" id="totalTasks">0</h3>
                                    <small class="text-muted">Tareas Totales</small>
                                </div>
                                <div class="bg-primary bg-opacity-10 rounded p-2">
                                    <i class="bi bi-list-task fs-4 text-primary"></i>
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
                                    <h3 class="mb-0 fw-bold" id="completedTasks">0</h3>
                                    <small class="text-muted">Completadas</small>
                                </div>
                                <div class="bg-success bg-opacity-10 rounded p-2">
                                    <i class="bi bi-check-circle fs-4 text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card stat-card yellow shadow-sm h-100" onclick="showSection('attendance')">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h3 class="mb-0 fw-bold" id="presentWorkers">0</h3>
                                    <small class="text-muted">Presentes Hoy</small>
                                </div>
                                <div class="bg-warning bg-opacity-10 rounded p-2">
                                    <i class="bi bi-people fs-4 text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card stat-card red shadow-sm h-100" onclick="showSection('alerts')">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h3 class="mb-0 fw-bold text-danger" id="pendingAlerts">0</h3>
                                    <small class="text-muted">Alertas Pendientes</small>
                                </div>
                                <div class="bg-danger bg-opacity-10 rounded p-2">
                                    <i class="bi bi-exclamation-triangle fs-4 text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-plus-circle fs-1 text-primary mb-3"></i>
                            <h5>Asignar Tarea</h5>
                            <p class="text-muted small">Crea y asigna tareas a tu equipo</p>
                            <button class="btn btn-primary-custom" onclick="showSection('tasks')">
                                <i class="bi bi-arrow-right me-2"></i>Gestionar
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-camera fs-1 text-success mb-3"></i>
                            <h5>Subir Evidencia</h5>
                            <p class="text-muted small">Documenta el avance del proyecto</p>
                            <button class="btn btn-primary-custom" onclick="showSection('evidence')">
                                <i class="bi bi-arrow-right me-2"></i>Agregar
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-calendar-check fs-1 text-warning mb-3"></i>
                            <h5>Registrar Asistencia</h5>
                            <p class="text-muted small">Control de personal diario</p>
                            <button class="btn btn-primary-custom" onclick="showSection('attendance')">
                                <i class="bi bi-arrow-right me-2"></i>Registrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Actividad Reciente</h5>
                </div>
                <div class="card-body" id="recentActivity">
                    <p class="text-muted text-center py-4">No hay actividad reciente</p>
                </div>
            </div>
        </div>

        <!-- Sección de Tareas -->
        <div id="tasks" class="content-section d-none">
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="mb-1">Gestión de Tareas</h2>
                        <p class="text-muted mb-0">Asigna y da seguimiento a las tareas del equipo</p>
                    </div>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalTask" onclick="openCreateTaskModal()">
                        <i class="bi bi-plus-circle me-2"></i>Nueva Tarea
                    </button>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchTask" placeholder="🔍 Buscar tarea..." onkeyup="filterTasks()">
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="filterTaskStatus" onchange="filterTasks()">
                                <option value="">Todos los estados</option>
                                <option value="pending">Pendientes</option>
                                <option value="in-progress">En Progreso</option>
                                <option value="completed">Completadas</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="filterTaskPriority" onchange="filterTasks()">
                                <option value="">Todas las prioridades</option>
                                <option value="low">Baja</option>
                                <option value="medium">Media</option>
                                <option value="high">Alta</option>
                                <option value="urgent">Urgente</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tasks List -->
            <div class="card shadow-sm" id="tasksListCard">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Tarea</th>
                                    <th>Asignado a</th>
                                    <th>Prioridad</th>
                                    <th>Estado</th>
                                    <th>Fecha Límite</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tasksTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Evidencias -->
        <div id="evidence" class="content-section d-none">
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="mb-1">Evidencias de Avance</h2>
                        <p class="text-muted mb-0">Fotos y documentación del progreso del proyecto</p>
                    </div>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalEvidence" onclick="openCreateEvidenceModal()">
                        <i class="bi bi-camera me-2"></i>Subir Evidencia
                    </button>
                </div>
            </div>
            
            <!-- Evidence Gallery -->
            <div id="evidenceGallery" class="row g-3"></div>
            
            <div id="emptyEvidence" class="card shadow-sm text-center d-none">
                <div class="card-body py-5">
                    <i class="bi bi-camera-fill display-1 text-muted mb-3"></i>
                    <h3>Sin Evidencias</h3>
                    <p class="text-muted">Sube fotos y notas del avance del proyecto</p>
                    <button class="btn btn-primary-custom mt-3" data-bs-toggle="modal" data-bs-target="#modalEvidence" onclick="openCreateEvidenceModal()">
                        <i class="bi bi-camera me-2"></i>Subir Primera Evidencia
                    </button>
                </div>
            </div>
        </div>

        <!-- Sección de Materiales -->
        <div id="materials" class="content-section d-none">
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="mb-1">Control de Materiales</h2>
                        <p class="text-muted mb-0">Inventario de materiales utilizados en el proyecto</p>
                    </div>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalMaterial" onclick="openCreateMaterialModal()">
                        <i class="bi bi-plus-circle me-2"></i>Registrar Material
                    </button>
                </div>
            </div>
            
            <!-- Materials List -->
            <div class="row g-3" id="materialsList"></div>
            
            <div id="emptyMaterials" class="card shadow-sm text-center d-none">
                <div class="card-body py-5">
                    <i class="bi bi-box-seam display-1 text-muted mb-3"></i>
                    <h3>Sin Materiales Registrados</h3>
                    <p class="text-muted">Comienza a registrar los materiales utilizados</p>
                    <button class="btn btn-primary-custom mt-3" data-bs-toggle="modal" data-bs-target="#modalMaterial" onclick="openCreateMaterialModal()">
                        <i class="bi bi-plus-circle me-2"></i>Registrar Primer Material
                    </button>
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
            
            <!-- Attendance List -->
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

        <!-- Sección de Alertas -->
        <div id="alerts" class="content-section d-none">
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="mb-1">Centro de Alertas</h2>
                        <p class="text-muted mb-0">Notificaciones y alertas del sistema</p>
                    </div>
                    <button class="btn btn-outline-secondary" onclick="markAllAlertsAsRead()">
                        <i class="bi bi-check-all me-2"></i>Marcar todas como leídas
                    </button>
                </div>
            </div>
            
            <!-- Alerts List -->
            <div class="card shadow-sm d-none" id="alertsListCard">
                <div class="card-body" id="alertsList"></div>
            </div>
            
            <div class="card shadow-sm text-center" id="emptyAlerts">
                <div class="card-body py-5">
                    <i class="bi bi-bell-slash display-1 text-muted mb-3"></i>
                    <h3>Sin Alertas</h3>
                    <p class="text-muted">No hay alertas pendientes en este momento</p>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALES -->
    
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
                        <input type="hidden" id="evidenceId">
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
                        <input type="hidden" id="materialId">
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

    <!-- Alertas Personalizadas -->
    <div class="custom-alert-overlay" id="customAlert">
        <div class="custom-alert-box text-center">
            <div class="mb-3" id="alertIcon"></div>
            <h4 id="alertTitle" class="mb-2"></h4>
            <p id="alertMessage" class="text-muted mb-4"></p>
            <div id="alertActions" class="d-flex gap-2 justify-content-center"></div>
        </div>
    </div>

    <!-- Toast Notifications -->
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
let tasks = [];
let evidences = [];
let materials = [];
let attendance = [];
let alerts = [];
let workers = [];
let projects = [];
let currentTaskId = null;
let currentEvidenceId = null;
let currentMaterialId = null;

// ============== INICIALIZACIÓN ==============
document.addEventListener('DOMContentLoaded', () => {
    loadAllData();
    updateUI();
    setTodayDate();
    
    // Preview de imagen de evidencia
    document.getElementById('evidencePhoto').addEventListener('change', previewEvidencePhoto);
    
    // Verificar alertas cada 30 segundos
    setInterval(checkAlerts, 30000);
});

function loadAllData() {
    tasks = JSON.parse(localStorage.getItem('supervisor_tasks')) || [];
    evidences = JSON.parse(localStorage.getItem('supervisor_evidences')) || [];
    materials = JSON.parse(localStorage.getItem('supervisor_materials')) || [];
    attendance = JSON.parse(localStorage.getItem('supervisor_attendance')) || [];
    alerts = JSON.parse(localStorage.getItem('constructora_notifications')) || [];
    workers = JSON.parse(localStorage.getItem('constructora_workers')) || [];
    projects = JSON.parse(localStorage.getItem('constructora_projects')) || [];
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
    
    if (id === 'tasks') loadTasks();
    if (id === 'evidence') loadEvidences();
    if (id === 'materials') loadMaterials();
    if (id === 'attendance') loadAttendance();
    if (id === 'alerts') loadAlerts();
}

// ============== UI UPDATES ==============
function updateUI() {
    updateStats();
    updateRecentActivity();
    updateAlertsBadge();
}

function updateStats() {
    const totalTasks = tasks.length;
    const completedTasks = tasks.filter(t => t.status === 'completed').length;
    const todayAttendance = attendance.filter(a => a.date === new Date().toISOString().split('T')[0]);
    const presentToday = todayAttendance.filter(a => a.status === 'present').length;
    const pendingAlerts = alerts.filter(a => !a.read).length;
    
    document.getElementById('totalTasks').textContent = totalTasks;
    document.getElementById('completedTasks').textContent = completedTasks;
    document.getElementById('presentWorkers').textContent = presentToday;
    document.getElementById('pendingAlerts').textContent = pendingAlerts;
}

function updateRecentActivity() {
    const container = document.getElementById('recentActivity');
    
    const recentTasks = tasks.slice(-3).reverse();
    const recentEvidences = evidences.slice(-2).reverse();
    
    let html = '';
    
    if (recentTasks.length > 0) {
        html += '<h6 class="mb-3"><i class="bi bi-list-task me-2"></i>Tareas Recientes</h6>';
        recentTasks.forEach(task => {
            const worker = workers.find(w => w.id === task.workerId);
            html += `
                <div class="alert alert-info mb-2">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>${task.title}</strong><br>
                            <small>Asignado a: ${worker?.name || 'N/A'}</small>
                        </div>
                        ${getTaskStatusBadge(task.status)}
                    </div>
                </div>
            `;
        });
    }
    
    if (recentEvidences.length > 0) {
        html += '<h6 class="mb-3 mt-4"><i class="bi bi-camera me-2"></i>Evidencias Recientes</h6>';
        recentEvidences.forEach(evidence => {
            html += `
                <div class="alert alert-success mb-2">
                    <strong>${evidence.title}</strong><br>
                    <small>${new Date(evidence.date).toLocaleDateString('es-MX')}</small>
                </div>
            `;
        });
    }
    
    if (html === '') {
        html = '<p class="text-muted text-center py-4">No hay actividad reciente</p>';
    }
    
    container.innerHTML = html;
}

function updateAlertsBadge() {
    const unreadCount = alerts.filter(a => !a.read).length;
    const badge = document.getElementById('alertsBadge');
    
    if (unreadCount > 0) {
        badge.textContent = unreadCount;
        badge.classList.remove('d-none');
    } else {
        badge.classList.add('d-none');
    }
}

// ============== TAREAS ==============
function loadTasks() {
    const select = document.getElementById('taskWorker');
    select.innerHTML = '<option value="">Seleccionar trabajador...</option>';
    workers.filter(w => w.role === 'trabajador').forEach(w => {
        select.innerHTML += `<option value="${w.id}">${w.name}</option>`;
    });
    
    filterTasks();
}

function filterTasks() {
    const search = document.getElementById('searchTask').value.toLowerCase();
    const status = document.getElementById('filterTaskStatus').value;
    const priority = document.getElementById('filterTaskPriority').value;
    
    const filtered = tasks.filter(t => {
        const matchSearch = t.title.toLowerCase().includes(search) || t.description.toLowerCase().includes(search);
        const matchStatus = !status || t.status === status;
        const matchPriority = !priority || t.priority === priority;
        return matchSearch && matchStatus && matchPriority;
    });
    
    const tbody = document.getElementById('tasksTable');
    tbody.innerHTML = '';
    
    if (filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No se encontraron tareas</td></tr>';
    } else {
        filtered.forEach(t => {
            const worker = workers.find(w => w.id === t.workerId);
            const row = document.createElement('tr');
            row.style.cursor = 'pointer';
            row.onclick = () => viewTask(t.id);
            
            row.innerHTML = `
                <td><strong>${t.title}</strong><br><small class="text-muted">${t.description.substring(0, 50)}...</small></td>
                <td>${worker?.name || 'N/A'}</td>
                <td>${getTaskPriorityBadge(t.priority)}</td>
                <td>${getTaskStatusBadge(t.status)}</td>
                <td>${new Date(t.deadline).toLocaleDateString('es-MX')}</td>
                <td onclick="event.stopPropagation()" class="text-center">
                    ${t.status !== 'completed' ? `
                    <button class="btn btn-sm btn-success btn-action me-1" onclick="markTaskCompleted(${t.id})" title="Marcar completada">
                        <i class="bi bi-check-circle"></i>
                    </button>
                    ` : ''}
                    <button class="btn btn-sm btn-warning btn-action me-1" onclick="editTask(${t.id})" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-action" onclick="confirmDeleteTask(${t.id})" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            
            tbody.appendChild(row);
        });
    }
}

function openCreateTaskModal() {
    document.getElementById('modalTaskTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Nueva Tarea';
    document.getElementById('taskForm').reset();
    currentTaskId = null;
}

function saveTask(e) {
    e.preventDefault();
    
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
        const index = tasks.findIndex(t => t.id === currentTaskId);
        tasks[index] = { ...tasks[index], ...data };
        showToast('success', 'Actualizado', 'Tarea actualizada correctamente');
    } else {
        data.id = Date.now();
        tasks.push(data);
        showToast('success', 'Creado', 'Tarea creada correctamente');
    }
    
    saveData('supervisor_tasks', tasks);
    bootstrap.Modal.getInstance(document.getElementById('modalTask')).hide();
    updateUI();
    filterTasks();
}

function editTask(id) {
    const task = tasks.find(t => t.id === id);
    if (!task) return;
    
    currentTaskId = id;
    document.getElementById('modalTaskTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Tarea';
    document.getElementById('taskTitle').value = task.title;
    document.getElementById('taskWorker').value = task.workerId;
    document.getElementById('taskPriority').value = task.priority;
    document.getElementById('taskStatus').value = task.status;
    document.getElementById('taskDeadline').value = task.deadline;
    document.getElementById('taskDescription').value = task.description;
    
    new bootstrap.Modal(document.getElementById('modalTask')).show();
}

function viewTask(id) {
    const task = tasks.find(t => t.id === id);
    if (!task) return;
    
    const worker = workers.find(w => w.id === task.workerId);
    
    showCustomAlert(
        '<i class="bi bi-list-task text-primary" style="font-size:60px"></i>',
        task.title,
        `<div class="text-start">
            <p><strong>Descripción:</strong> ${task.description}</p>
            <p><strong>Asignado a:</strong> ${worker?.name || 'N/A'}</p>
            <p><strong>Prioridad:</strong> ${getTaskPriorityBadge(task.priority)}</p>
            <p><strong>Estado:</strong> ${getTaskStatusBadge(task.status)}</p>
            <p><strong>Fecha límite:</strong> ${new Date(task.deadline).toLocaleDateString('es-MX')}</p>
        </div>`,
        [
            { text: 'Cerrar', class: 'btn-secondary', action: closeCustomAlert },
            { text: 'Editar', class: 'btn-primary-custom', action: () => { closeCustomAlert(); editTask(id); } }
        ]
    );
}

function markTaskCompleted(id) {
    const task = tasks.find(t => t.id === id);
    if (!task) return;
    
    task.status = 'completed';
    task.completedAt = new Date().toISOString();
    
    saveData('supervisor_tasks', tasks);
    showToast('success', 'Completada', 'Tarea marcada como completada');
    filterTasks();
    updateUI();
}

function confirmDeleteTask(id) {
    const task = tasks.find(t => t.id === id);
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
    tasks = tasks.filter(t => t.id !== id);
    saveData('supervisor_tasks', tasks);
    closeCustomAlert();
    showToast('success', 'Eliminado', 'Tarea eliminada correctamente');
    filterTasks();
    updateUI();
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

// ============== EVIDENCIAS ==============
function loadEvidences() {
    const gallery = document.getElementById('evidenceGallery');
    const empty = document.getElementById('emptyEvidence');
    
    if (evidences.length === 0) {
        gallery.innerHTML = '';
        empty.classList.remove('d-none');
    } else {
        empty.classList.add('d-none');
        gallery.innerHTML = '';
        
        evidences.forEach(evidence => {
            const col = document.createElement('div');
            col.className = 'col-md-4';
            col.innerHTML = `
                <div class="card shadow-sm h-100">
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
            gallery.appendChild(col);
        });
    }
}

function openCreateEvidenceModal() {
    document.getElementById('evidenceForm').reset();
    document.getElementById('evidencePreview').classList.add('d-none');
    currentEvidenceId = null;
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
        
        evidences.push(data);
        saveData('supervisor_evidences', evidences);
        
        bootstrap.Modal.getInstance(document.getElementById('modalEvidence')).hide();
        showToast('success', 'Subido', 'Evidencia subida correctamente');
        loadEvidences();
        updateUI();
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
    evidences = evidences.filter(e => e.id !== id);
    saveData('supervisor_evidences', evidences);
    closeCustomAlert();
    showToast('success', 'Eliminado', 'Evidencia eliminada correctamente');
    loadEvidences();
    updateUI();
}

// ============== MATERIALES ==============
function loadMaterials() {
    const list = document.getElementById('materialsList');
    const empty = document.getElementById('emptyMaterials');
    
    if (materials.length === 0) {
        list.innerHTML = '';
        empty.classList.remove('d-none');
    } else {
        empty.classList.add('d-none');
        list.innerHTML = '';
        
        materials.forEach(material => {
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
            list.appendChild(col);
        });
    }
}

function openCreateMaterialModal() {
    document.getElementById('materialForm').reset();
    currentMaterialId = null;
}

function saveMaterial(e) {
    e.preventDefault();
    
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
    
    materials.push(data);
    saveData('supervisor_materials', materials);
    
    bootstrap.Modal.getInstance(document.getElementById('modalMaterial')).hide();
    showToast('success', 'Registrado', 'Material registrado correctamente');
    loadMaterials();
    updateUI();
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
    materials = materials.filter(m => m.id !== id);
    saveData('supervisor_materials', materials);
    closeCustomAlert();
    showToast('success', 'Eliminado', 'Material eliminado correctamente');
    loadMaterials();
    updateUI();
}

// ============== ASISTENCIAS ==============
function loadAttendance() {
    const date = document.getElementById('attendanceDate').value;
    const tbody = document.getElementById('attendanceTable');
    tbody.innerHTML = '';
    
    const workersToShow = workers.filter(w => w.role === 'trabajador');
    
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
    
    // Remover registro anterior si existe
    attendance = attendance.filter(a => !(a.workerId === workerId && a.date === date));
    attendance.push(data);
    
    saveData('supervisor_attendance', attendance);
    bootstrap.Modal.getInstance(document.getElementById('modalAttendance')).hide();
    showToast('success', 'Registrado', 'Asistencia registrada correctamente');
    loadAttendance();
    updateUI();
}

function getAttendanceBadge(status) {
    const badges = {
        'present': '<span class="badge attendance-present">Presente</span>',
        'absent': '<span class="badge attendance-absent">Ausente</span>',
        'late': '<span class="badge attendance-late">Retardo</span>'
    };
    return badges[status] || badges.present;
}

// ============== ALERTAS ==============
function checkAlerts() {
    // Verificar alertas de presupuesto
    projects.forEach(project => {
        const budget = parseFloat(project.budget) || 0;
        const spent = parseFloat(project.spent) || 0;
        const percentage = budget > 0 ? (spent / budget) * 100 : 0;
        
        if (percentage >= 90) {
            const existingAlert = alerts.find(a => 
                a.projectId === project.id && 
                a.type === 'budget' && 
                !a.read
            );
            
            if (!existingAlert) {
                const alert = {
                    id: Date.now(),
                    type: 'budget',
                    projectId: project.id,
                    title: '⚠️ Alerta de Presupuesto',
                    message: `El proyecto "${project.name}" ha superado el ${percentage.toFixed(1)}% del presupuesto. Gasto: $${spent.toLocaleString('es-MX')} de $${budget.toLocaleString('es-MX')}`,
                    date: new Date().toISOString(),
                    read: false
                };
                alerts.push(alert);
                saveData('constructora_notifications', alerts);
            }
        }
    });
    
    updateAlertsBadge();
}

function loadAlerts() {
    const container = document.getElementById('alertsList');
    const listCard = document.getElementById('alertsListCard');
    const empty = document.getElementById('emptyAlerts');
    
    if (alerts.length === 0) {
        listCard.classList.add('d-none');
        empty.classList.remove('d-none');
    } else {
        empty.classList.add('d-none');
        listCard.classList.remove('d-none');
        container.innerHTML = '';
        
        const sortedAlerts = [...alerts].sort((a, b) => new Date(b.date) - new Date(a.date));
        
        sortedAlerts.forEach(alert => {
            const project = projects.find(p => p.id === alert.projectId);
            const date = new Date(alert.date);
            const formattedDate = date.toLocaleDateString('es-MX', { 
                day: '2-digit', 
                month: 'short', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            let icon = '<i class="bi bi-info-circle-fill text-primary fs-4"></i>';
            if (alert.type === 'budget') icon = '<i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>';
            if (alert.type === 'problem') icon = '<i class="bi bi-exclamation-circle-fill text-warning fs-4"></i>';
            
            const div = document.createElement('div');
            div.className = `card mb-3 alert-item ${alert.read ? 'read' : ''}`;
            div.innerHTML = `
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex align-items-start flex-grow-1">
                            <span class="me-3">${icon}</span>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${alert.title}</h6>
                                <small class="text-muted">
                                    ${project ? `<i class="bi bi-building me-1"></i>${project.name} • ` : ''}
                                    <i class="bi bi-clock me-1"></i>${formattedDate}
                                </small>
                            </div>
                        </div>
                        ${!alert.read ? '<span class="badge bg-danger ms-2">Nueva</span>' : ''}
                    </div>
                    <p class="mb-2 ms-5">${alert.message}</p>
                    ${!alert.read ? `
                        <button class="btn btn-sm btn-outline-primary ms-5" onclick="markAlertAsRead(${alert.id})">
                            <i class="bi bi-check me-1"></i>Marcar como leída
                        </button>
                    ` : ''}
                </div>
            `;
            container.appendChild(div);
        });
    }
}

function markAlertAsRead(id) {
    const alert = alerts.find(a => a.id === id);
    if (alert) {
        alert.read = true;
        saveData('constructora_notifications', alerts);
        loadAlerts();
        updateAlertsBadge();
        showToast('success', 'Actualizado', 'Alerta marcada como leída');
    }
}

function markAllAlertsAsRead() {
    if (alerts.length === 0) {
        showToast('info', 'Información', 'No hay alertas para marcar');
        return;
    }
    
    alerts.forEach(a => a.read = true);
    saveData('constructora_notifications', alerts);
    loadAlerts();
    updateAlertsBadge();
    showToast('success', 'Completado', 'Todas las alertas marcadas como leídas');
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