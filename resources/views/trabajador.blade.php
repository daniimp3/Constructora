<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Trabajador</title>
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
        
        .badge-low { background: #dbeafe; color: #1e40af; }
        .badge-medium { background: #fef3c7; color: #92400e; }
        .badge-high { background: #fed7aa; color: #9a3412; }
        .badge-urgent { background: #fee2e2; color: #991b1b; }
        
        .badge-materiales { background: #dbeafe; color: #1e40af; }
        .badge-herramientas { background: #e0e7ff; color: #3730a3; }
        .badge-seguridad { background: #fee2e2; color: #991b1b; }
        .badge-calidad { background: #fef3c7; color: #92400e; }
        .badge-otro { background: #f3f4f6; color: #374151; }
        
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

        .task-card {
            border-left: 4px solid var(--accent);
            transition: all 0.3s;
            cursor: pointer;
        }

        .task-card:hover {
            border-left-color: var(--secondary);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateX(5px);
        }

        .task-card.completed {
            opacity: 0.7;
            border-left-color: #10b981;
        }

        .problem-card {
            border-left: 4px solid #ef4444;
            transition: all 0.3s;
        }

        .problem-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .problem-card.resolved {
            border-left-color: #10b981;
            opacity: 0.7;
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
            <p class="company-subtitle mb-0">Panel de Trabajador</p>
        </div>
        
        <!-- User Profile -->
        <div class="bg-dark bg-opacity-25 m-3 p-3 rounded">
            <div class="d-flex align-items-center">
                <div class="bg-gradient rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                     style="width:45px;height:45px;background:linear-gradient(135deg,#3b82f6,#8b5cf6)">
                    {{ Auth::check() ? substr(Auth::user()->name, 0, 2) : 'TR' }}
                </div>
                <div class="ms-3">
                    <div class="text-white fw-semibold">{{ Auth::check() ? Auth::user()->name : 'Trabajador' }}</div>
                    <small class="text-white-50">Trabajador</small>
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
                    <i class="bi bi-list-task me-2"></i>Mis Tareas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="showSection('report-problem');return false">
                    <i class="bi bi-exclamation-triangle me-2"></i>Reportar Problema
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="showSection('my-reports');return false">
                    <i class="bi bi-clipboard-check me-2"></i>Mis Reportes
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
                    <h2 class="mb-1">¡Bienvenido!</h2>
                    <p class="text-muted mb-0">Gestiona tus tareas y reporta problemas</p>
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
                                    <small class="text-muted">Tareas Asignadas</small>
                                </div>
                                <div class="bg-primary bg-opacity-10 rounded p-2">
                                    <i class="bi bi-list-task fs-4 text-primary"></i>
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
                                    <h3 class="mb-0 fw-bold" id="pendingTasks">0</h3>
                                    <small class="text-muted">Pendientes</small>
                                </div>
                                <div class="bg-warning bg-opacity-10 rounded p-2">
                                    <i class="bi bi-clock fs-4 text-warning"></i>
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
                    <div class="card stat-card red shadow-sm h-100" onclick="showSection('my-reports')">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h3 class="mb-0 fw-bold" id="totalReports">0</h3>
                                    <small class="text-muted">Problemas Reportados</small>
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
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-list-check fs-1 text-primary mb-3"></i>
                            <h5>Mis Tareas</h5>
                            <p class="text-muted small">Consulta y actualiza tus tareas asignadas</p>
                            <button class="btn btn-primary-custom" onclick="showSection('tasks')">
                                <i class="bi bi-arrow-right me-2"></i>Ver Tareas
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-exclamation-triangle-fill fs-1 text-danger mb-3"></i>
                            <h5>Reportar Problema</h5>
                            <p class="text-muted small">Notifica problemas en el proyecto</p>
                            <button class="btn btn-primary-custom" onclick="showSection('report-problem')">
                                <i class="bi bi-plus-circle me-2"></i>Reportar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Tasks -->
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tareas Recientes</h5>
                    <button class="btn btn-sm btn-outline-secondary" onclick="showSection('tasks')">
                        Ver todas
                    </button>
                </div>
                <div class="card-body" id="recentTasksOverview">
                    <p class="text-muted text-center py-4">No hay tareas asignadas</p>
                </div>
            </div>
        </div>

        <!-- Sección de Mis Tareas -->
        <div id="tasks" class="content-section d-none">
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="mb-1">Mis Tareas Asignadas</h2>
                        <p class="text-muted mb-0">Consulta y actualiza el estado de tus tareas</p>
                    </div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="searchTask" placeholder="🔍 Buscar tarea..." onkeyup="filterTasks()">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filterTaskStatus" onchange="filterTasks()">
                                <option value="">Todos los estados</option>
                                <option value="pending">Pendientes</option>
                                <option value="in-progress">En Progreso</option>
                                <option value="completed">Completadas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
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
            <div id="tasksList" class="row g-3"></div>
            
            <div id="emptyTasks" class="card shadow-sm text-center d-none">
                <div class="card-body py-5">
                    <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                    <h3>Sin Tareas Asignadas</h3>
                    <p class="text-muted">No tienes tareas asignadas en este momento</p>
                </div>
            </div>
        </div>

        <!-- Sección de Reportar Problema -->
        <div id="report-problem" class="content-section d-none">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="mb-1">Reportar Problema</h2>
                    <p class="text-muted mb-0">Notifica cualquier problema o incidencia en el proyecto</p>
                </div>
            </div>
            
            <!-- Report Form -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <form id="problemForm" onsubmit="saveProblem(event)">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Título del Problema *</label>
                                <input type="text" class="form-control" id="problemTitle" required placeholder="Ej: Falta de materiales">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Categoría *</label>
                                <select class="form-select" id="problemCategory" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="materiales">Materiales</option>
                                    <option value="herramientas">Herramientas</option>
                                    <option value="seguridad">Seguridad</option>
                                    <option value="calidad">Calidad</option>
                                    <option value="otro">Otro</option>
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
                                <input type="text" class="form-control" id="problemLocation" placeholder="Ej: Área de construcción, Piso 2">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Descripción Detallada *</label>
                                <textarea class="form-control" id="problemDescription" rows="4" required placeholder="Describe el problema con el mayor detalle posible..."></textarea>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Nota:</strong> Este reporte será enviado automáticamente al supervisor y al administrador del proyecto.
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="bi bi-send me-2"></i>Enviar Reporte
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('problemForm').reset()">
                                <i class="bi bi-x-circle me-2"></i>Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sección de Mis Reportes -->
        <div id="my-reports" class="content-section d-none">
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="mb-1">Mis Reportes de Problemas</h2>
                        <p class="text-muted mb-0">Historial de problemas que has reportado</p>
                    </div>
                    <button class="btn btn-primary-custom" onclick="showSection('report-problem')">
                        <i class="bi bi-plus-circle me-2"></i>Nuevo Reporte
                    </button>
                </div>
            </div>
            
            <!-- Filter -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="searchReport" placeholder="🔍 Buscar reporte..." onkeyup="filterReports()">
                        </div>
                        <div class="col-md-6">
                            <select class="form-select" id="filterReportStatus" onchange="filterReports()">
                                <option value="">Todos los estados</option>
                                <option value="pending">Pendientes</option>
                                <option value="in-progress">En Proceso</option>
                                <option value="resolved">Resueltos</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Reports List -->
            <div id="reportsList"></div>
            
            <div id="emptyReports" class="card shadow-sm text-center d-none">
                <div class="card-body py-5">
                    <i class="bi bi-clipboard-check display-1 text-muted mb-3"></i>
                    <h3>Sin Reportes</h3>
                    <p class="text-muted">No has reportado problemas aún</p>
                    <button class="btn btn-primary-custom mt-3" onclick="showSection('report-problem')">
                        <i class="bi bi-plus-circle me-2"></i>Reportar Primer Problema
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalle de Tarea -->
    <div class="modal fade" id="modalTaskDetail" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-list-task me-2"></i>Detalle de la Tarea
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="taskDetailContent"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="btnMarkCompleted" onclick="markTaskCompleted()">
                        <i class="bi bi-check-circle me-2"></i>Marcar Completada
                    </button>
                </div>
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
let problems = [];
let currentWorkerId = 1; // En producción, vendría de la sesión del usuario
let currentTaskId = null;

// ============== INICIALIZACIÓN ==============
document.addEventListener('DOMContentLoaded', () => {
    loadAllData();
    updateUI();
});

function loadAllData() {
    tasks = JSON.parse(localStorage.getItem('supervisor_tasks')) || [];
    problems = JSON.parse(localStorage.getItem('worker_problems')) || [];
}

function saveData(key, data) {
    localStorage.setItem(key, JSON.stringify(data));
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
    if (id === 'my-reports') loadReports();
}

// ============== UI UPDATES ==============
function updateUI() {
    updateStats();
    updateRecentTasksOverview();
}

function updateStats() {
    const myTasks = tasks.filter(t => t.workerId === currentWorkerId);
    const totalTasks = myTasks.length;
    const pendingTasks = myTasks.filter(t => t.status === 'pending').length;
    const completedTasks = myTasks.filter(t => t.status === 'completed').length;
    const totalReports = problems.filter(p => p.workerId === currentWorkerId).length;
    
    document.getElementById('totalTasks').textContent = totalTasks;
    document.getElementById('pendingTasks').textContent = pendingTasks;
    document.getElementById('completedTasks').textContent = completedTasks;
    document.getElementById('totalReports').textContent = totalReports;
}

function updateRecentTasksOverview() {
    const container = document.getElementById('recentTasksOverview');
    const myTasks = tasks.filter(t => t.workerId === currentWorkerId);
    
    if (myTasks.length === 0) {
        container.innerHTML = '<p class="text-muted text-center py-4">No hay tareas asignadas</p>';
        return;
    }
    
    const recentTasks = myTasks.slice(-3).reverse();
    let html = '';
    
    recentTasks.forEach(task => {
        html += `
            <div class="task-card card mb-2 ${task.status === 'completed' ? 'completed' : ''}" onclick="viewTaskDetail(${task.id})">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${task.title}</h6>
                            <small class="text-muted">${task.description.substring(0, 60)}...</small>
                        </div>
                        <div class="d-flex gap-2">
                            ${getTaskPriorityBadge(task.priority)}
                            ${getTaskStatusBadge(task.status)}
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>Vence: ${new Date(task.deadline).toLocaleDateString('es-MX')}
                        </small>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// ============== TAREAS ==============
function loadTasks() {
    filterTasks();
}

function filterTasks() {
    const search = document.getElementById('searchTask').value.toLowerCase();
    const status = document.getElementById('filterTaskStatus').value;
    const priority = document.getElementById('filterTaskPriority').value;
    
    const myTasks = tasks.filter(t => t.workerId === currentWorkerId);
    
    const filtered = myTasks.filter(t => {
        const matchSearch = t.title.toLowerCase().includes(search) || t.description.toLowerCase().includes(search);
        const matchStatus = !status || t.status === status;
        const matchPriority = !priority || t.priority === priority;
        return matchSearch && matchStatus && matchPriority;
    });
    
    const container = document.getElementById('tasksList');
    const empty = document.getElementById('emptyTasks');
    
    if (filtered.length === 0) {
        container.innerHTML = '';
        empty.classList.remove('d-none');
    } else {
        empty.classList.add('d-none');
        container.innerHTML = '';
        
        filtered.forEach(task => {
            const col = document.createElement('div');
            col.className = 'col-md-6';
            
            const daysLeft = Math.ceil((new Date(task.deadline) - new Date()) / (1000 * 60 * 60 * 24));
            const urgencyClass = daysLeft < 0 ? 'text-danger' : daysLeft <= 3 ? 'text-warning' : 'text-muted';
            const urgencyText = daysLeft < 0 ? '¡Vencida!' : daysLeft === 0 ? 'Vence hoy' : daysLeft === 1 ? 'Vence mañana' : `${daysLeft} días restantes`;
            
            col.innerHTML = `
                <div class="task-card card shadow-sm ${task.status === 'completed' ? 'completed' : ''}" onclick="viewTaskDetail(${task.id})">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0">${task.title}</h6>
                            ${task.status !== 'completed' ? `
                                <button class="btn btn-sm btn-success btn-action" onclick="event.stopPropagation(); quickMarkCompleted(${task.id})" title="Marcar completada">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            ` : '<i class="bi bi-check-circle-fill text-success fs-4"></i>'}
                        </div>
                        <p class="text-muted small mb-2">${task.description}</p>
                        <div class="d-flex gap-2 flex-wrap mb-2">
                            ${getTaskPriorityBadge(task.priority)}
                            ${getTaskStatusBadge(task.status)}
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>${new Date(task.deadline).toLocaleDateString('es-MX')}
                            </small>
                            <small class="${urgencyClass} fw-bold">
                                ${urgencyText}
                            </small>
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(col);
        });
    }
}

function viewTaskDetail(id) {
    const task = tasks.find(t => t.id === id);
    if (!task) return;
    
    currentTaskId = id;
    const daysLeft = Math.ceil((new Date(task.deadline) - new Date()) / (1000 * 60 * 60 * 24));
    const urgencyClass = daysLeft < 0 ? 'text-danger' : daysLeft <= 3 ? 'text-warning' : 'text-success';
    
    const content = document.getElementById('taskDetailContent');
    content.innerHTML = `
        <div class="row g-3">
            <div class="col-12">
                <h4 class="mb-3">${task.title}</h4>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Estado</small>
                        ${getTaskStatusBadge(task.status)}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Prioridad</small>
                        ${getTaskPriorityBadge(task.priority)}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Fecha Límite</small>
                        <strong>${new Date(task.deadline).toLocaleDateString('es-MX')}</strong>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Tiempo Restante</small>
                        <strong class="${urgencyClass}">${daysLeft >= 0 ? daysLeft + ' días' : 'Vencida'}</strong>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Descripción</small>
                        <p class="mb-0">${task.description}</p>
                    </div>
                </div>
            </div>
            ${task.status === 'completed' ? `
            <div class="col-12">
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>Tarea completada</strong> el ${new Date(task.completedAt).toLocaleDateString('es-MX')}
                </div>
            </div>
            ` : ''}
        </div>
    `;
    
    const btnMarkCompleted = document.getElementById('btnMarkCompleted');
    if (task.status === 'completed') {
        btnMarkCompleted.classList.add('d-none');
    } else {
        btnMarkCompleted.classList.remove('d-none');
    }
    
    new bootstrap.Modal(document.getElementById('modalTaskDetail')).show();
}

function quickMarkCompleted(id) {
    currentTaskId = id;
    markTaskCompleted();
}

function markTaskCompleted() {
    const task = tasks.find(t => t.id === currentTaskId);
    if (!task) return;
    
    task.status = 'completed';
    task.completedAt = new Date().toISOString();
    
    saveData('supervisor_tasks', tasks);
    
    // Cerrar modal si está abierto
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalTaskDetail'));
    if (modal) modal.hide();
    
    showToast('success', '¡Completada!', 'Tarea marcada como completada');
    updateUI();
    filterTasks();
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

// ============== REPORTAR PROBLEMAS ==============
function saveProblem(e) {
    e.preventDefault();
    
    const data = {
        id: Date.now(),
        workerId: currentWorkerId,
        projectId: null, // Se puede obtener del trabajador si está asignado a un proyecto
        title: document.getElementById('problemTitle').value,
        category: document.getElementById('problemCategory').value,
        priority: document.getElementById('problemPriority').value,
        location: document.getElementById('problemLocation').value,
        description: document.getElementById('problemDescription').value,
        status: 'pending',
        createdAt: new Date().toISOString(),
        read: false
    };
    
    problems.push(data);
    saveData('worker_problems', problems);
    
    // Agregar a notificaciones globales para que supervisor y admin lo vean
    let notifications = JSON.parse(localStorage.getItem('constructora_notifications')) || [];
    const notification = {
        id: Date.now() + 1,
        workerId: currentWorkerId,
        projectId: data.projectId,
        type: 'problem',
        title: '🚨 Problema Reportado',
        message: `Problema: "${data.title}" - ${data.description.substring(0, 100)}${data.description.length > 100 ? '...' : ''}`,
        date: new Date().toISOString(),
        read: false
    };
    notifications.push(notification);
    localStorage.setItem('constructora_notifications', JSON.stringify(notifications));
    
    document.getElementById('problemForm').reset();
    showToast('success', 'Enviado', 'Problema reportado correctamente. El supervisor será notificado.');
    updateUI();
    
    // Mostrar alerta de confirmación
    setTimeout(() => {
        showCustomAlert(
            '<i class="bi bi-check-circle-fill text-success" style="font-size:60px"></i>',
            '¡Reporte Enviado!',
            'Tu reporte ha sido enviado al supervisor y administrador. Recibirás actualizaciones sobre su estado.',
            [
                { text: 'Ver Mis Reportes', class: 'btn-primary-custom', action: () => { closeCustomAlert(); showSection('my-reports'); } },
                { text: 'Cerrar', class: 'btn-secondary', action: closeCustomAlert }
            ]
        );
    }, 500);
}

// ============== MIS REPORTES ==============
function loadReports() {
    filterReports();
}

function filterReports() {
    const search = document.getElementById('searchReport').value.toLowerCase();
    const status = document.getElementById('filterReportStatus').value;
    
    const myReports = problems.filter(p => p.workerId === currentWorkerId);
    
    const filtered = myReports.filter(r => {
        const matchSearch = r.title.toLowerCase().includes(search) || r.description.toLowerCase().includes(search);
        const matchStatus = !status || r.status === status;
        return matchSearch && matchStatus;
    });
    
    const container = document.getElementById('reportsList');
    const empty = document.getElementById('emptyReports');
    
    if (filtered.length === 0) {
        container.innerHTML = '';
        empty.classList.remove('d-none');
    } else {
        empty.classList.add('d-none');
        container.innerHTML = '';
        
        filtered.forEach(report => {
            const card = document.createElement('div');
            card.className = `problem-card card shadow-sm mb-3 ${report.status === 'resolved' ? 'resolved' : ''}`;
            
            const statusColor = report.status === 'resolved' ? 'success' : report.status === 'in-progress' ? 'warning' : 'danger';
            const statusText = report.status === 'resolved' ? 'Resuelto' : report.status === 'in-progress' ? 'En Proceso' : 'Pendiente';
            
            card.innerHTML = `
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0">${report.title}</h6>
                        <span class="badge bg-${statusColor}">${statusText}</span>
                    </div>
                    <p class="text-muted small mb-2">${report.description}</p>
                    <div class="d-flex gap-2 flex-wrap mb-2">
                        ${getCategoryBadge(report.category)}
                        ${getTaskPriorityBadge(report.priority)}
                        ${report.location ? `<span class="badge bg-secondary"><i class="bi bi-geo-alt me-1"></i>${report.location}</span>` : ''}
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>Reportado: ${new Date(report.createdAt).toLocaleDateString('es-MX')}
                        </small>
                        ${report.notes ? `
                        <button class="btn btn-sm btn-outline-info" onclick="viewReportNotes('${report.notes}')">
                            <i class="bi bi-chat-left-text me-1"></i>Ver respuesta
                        </button>
                        ` : ''}
                    </div>
                </div>
            `;
            
            container.appendChild(card);
        });
    }
}

function getCategoryBadge(category) {
    const badges = {
        'materiales': '<span class="badge badge-materiales">Materiales</span>',
        'herramientas': '<span class="badge badge-herramientas">Herramientas</span>',
        'seguridad': '<span class="badge badge-seguridad">Seguridad</span>',
        'calidad': '<span class="badge badge-calidad">Calidad</span>',
        'otro': '<span class="badge badge-otro">Otro</span>'
    };
    return badges[category] || badges.otro;
}

function viewReportNotes(notes) {
    showCustomAlert(
        '<i class="bi bi-chat-left-text text-info" style="font-size:60px"></i>',
        'Respuesta del Supervisor',
        notes,
        [
            { text: 'Cerrar', class: 'btn-secondary', action: closeCustomAlert }
        ]
    );
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