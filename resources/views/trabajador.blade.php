<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Trabajador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background: #f0f4f8; margin: 0; }
        
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: linear-gradient(180deg, #1e3a5f 0%, #2d5166 100%);
            z-index: 1000;
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
            overflow-y: auto;
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
        .stat-card.orange { border-color: #f97316; }
        .stat-card.green { border-color: #10b981; }
        .stat-card.red { border-color: #ef4444; }
        
        .task-card {
            border-left: 4px solid #3b82f6;
            transition: all 0.3s;
        }
        
        .task-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transform: translateX(5px);
        }
        
        .task-pending { border-left-color: #f59e0b; }
        .task-progress { border-left-color: #3b82f6; }
        .task-completed { border-left-color: #10b981; }
        
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-progress { background: #dbeafe; color: #1e40af; }
        .badge-completed { background: #dcfce7; color: #166534; }
        
        .badge-low { background: #dbeafe; color: #1e40af; }
        .badge-medium { background: #fef3c7; color: #92400e; }
        .badge-high { background: #fee2e2; color: #991b1b; }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, #3e6985, #5a8caf);
            border: none;
            color: white;
        }
        
        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #2d5166, #3e6985);
            color: white;
        }
        
        .btn-danger-custom {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            border: none;
            color: white;
        }
        
        .problem-card {
            border-left: 4px solid;
            transition: all 0.3s;
        }
        
        .problem-card.high { border-left-color: #dc2626; }
        .problem-card.medium { border-left-color: #f59e0b; }
        .problem-card.low { border-left-color: #3b82f6; }
        
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e5e7eb;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -26px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #3b82f6;
            border: 3px solid white;
            box-shadow: 0 0 0 2px #3b82f6;
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
            animation: slideIn 0.3s;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
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
        <div class="p-4 border-bottom border-secondary">
            <h4 class="text-white mb-1">🏗️ Constructora</h4>
            <small class="text-white-50">Panel de Trabajador</small>
        </div>
        
        <div class="bg-dark bg-opacity-25 m-3 p-3 rounded">
            <div class="d-flex align-items-center">
                <div class="bg-gradient rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                     style="width:45px;height:45px;background:linear-gradient(135deg,#f97316,#fb923c)">
                    {{ Auth::check() ? substr(Auth::user()->name, 0, 2) : 'TR' }}
                </div>
                <div class="ms-3">
                    <div class="text-white fw-semibold">{{ Auth::check() ? Auth::user()->name : 'Trabajador' }}</div>
                    <small class="text-white-50">Trabajador</small>
                </div>
            </div>
        </div>
        
        <ul class="nav flex-column px-3 mt-3">
            <li class="nav-item">
                <a class="nav-link active" href="#" onclick="showSection('overview');return false">
                    <i class="bi bi-speedometer2 me-2"></i>Inicio
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="showSection('tasks');return false">
                    <i class="bi bi-list-task me-2"></i>Mis Tareas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="showSection('project');return false">
                    <i class="bi bi-building me-2"></i>Mi Proyecto
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="showSection('problems');return false">
                    <i class="bi bi-exclamation-triangle me-2"></i>Reportar Problema
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="showSection('history');return false">
                    <i class="bi bi-clock-history me-2"></i>Historial
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
                    <h2 class="mb-1">Panel de Control</h2>
                    <p class="text-muted mb-0">Bienvenido, <strong id="workerName">Trabajador</strong></p>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card stat-card blue shadow-sm h-100" onclick="showSection('tasks')">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
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
                    <div class="card stat-card orange shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="mb-0 fw-bold" id="pendingTasks">0</h3>
                                    <small class="text-muted">Pendientes</small>
                                </div>
                                <div class="bg-warning bg-opacity-10 rounded p-2">
                                    <i class="bi bi-hourglass-split fs-4 text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card stat-card green shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
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
                    <div class="card stat-card red shadow-sm h-100" onclick="showSection('problems')">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="mb-0 fw-bold" id="totalProblems">0</h3>
                                    <small class="text-muted">Problemas</small>
                                </div>
                                <div class="bg-danger bg-opacity-10 rounded p-2">
                                    <i class="bi bi-exclamation-triangle fs-4 text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tareas Urgentes -->
            <div class="card shadow-sm mb-4" id="urgentTasksCard">
                <div class="card-header bg-white">
                    <h5 class="mb-0">⚡ Tareas Urgentes</h5>
                </div>
                <div class="card-body" id="urgentTasksList"></div>
            </div>
            
            <!-- Proyecto Actual -->
            <div class="card shadow-sm" id="currentProjectCard">
                <div class="card-header bg-white">
                    <h5 class="mb-0">🏗️ Proyecto Asignado</h5>
                </div>
                <div class="card-body" id="currentProjectInfo"></div>
            </div>
        </div>

        <!-- Sección de Tareas -->
        <div id="tasks" class="content-section d-none">
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="mb-1">Mis Tareas</h2>
                        <p class="text-muted mb-0">Gestiona tus tareas asignadas</p>
                    </div>
                </div>
            </div>
            
            <!-- Filtros -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <select class="form-select" id="filterTaskStatus" onchange="filterTasks()">
                                <option value="">Todos los estados</option>
                                <option value="pending">Pendientes</option>
                                <option value="progress">En Progreso</option>
                                <option value="completed">Completadas</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="filterTaskPriority" onchange="filterTasks()">
                                <option value="">Todas las prioridades</option>
                                <option value="high">Alta</option>
                                <option value="medium">Media</option>
                                <option value="low">Baja</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchTask" placeholder="Buscar tarea..." onkeyup="filterTasks()">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lista de Tareas -->
            <div id="tasksList"></div>
            
            <!-- Sin Tareas -->
            <div class="card shadow-sm text-center d-none" id="emptyTasks">
                <div class="card-body py-5">
                    <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                    <h3>Sin Tareas Asignadas</h3>
                    <p class="text-muted">No tienes tareas pendientes en este momento</p>
                </div>
            </div>
        </div>

        <!-- Sección de Mi Proyecto -->
        <div id="project" class="content-section d-none">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="mb-1">Mi Proyecto</h2>
                    <p class="text-muted mb-0">Información del proyecto asignado</p>
                </div>
            </div>
            
            <div id="projectDetails"></div>
        </div>

        <!-- Sección de Reportar Problema -->
        <div id="problems" class="content-section d-none">
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="mb-1">Reportar Problema</h2>
                        <p class="text-muted mb-0">Notifica problemas en el proyecto</p>
                    </div>
                    <button class="btn btn-danger-custom" data-bs-toggle="modal" data-bs-target="#modalProblem">
                        <i class="bi bi-plus-circle me-2"></i>Nuevo Reporte
                    </button>
                </div>
            </div>
            
            <!-- Problemas Reportados -->
            <div class="card shadow-sm" id="problemsListCard">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Problemas Reportados</h5>
                </div>
                <div class="card-body" id="problemsList"></div>
            </div>
            
            <!-- Sin Problemas -->
            <div class="card shadow-sm text-center d-none" id="emptyProblems">
                <div class="card-body py-5">
                    <i class="bi bi-emoji-smile display-1 text-success mb-3"></i>
                    <h3>Sin Problemas Reportados</h3>
                    <p class="text-muted">Excelente trabajo, todo marcha bien</p>
                    <button class="btn btn-danger-custom mt-3" data-bs-toggle="modal" data-bs-target="#modalProblem">
                        <i class="bi bi-plus-circle me-2"></i>Reportar Problema
                    </button>
                </div>
            </div>
        </div>

        <!-- Sección de Historial -->
        <div id="history" class="content-section d-none">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="mb-1">Historial de Actividad</h2>
                    <p class="text-muted mb-0">Registro de tareas y reportes</p>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="timeline" id="historyTimeline"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Reportar Problema -->
    <div class="modal fade" id="modalProblem" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reportar Problema</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="problemForm" onsubmit="saveProblem(event)">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Describe el problema con el mayor detalle posible. El supervisor será notificado inmediatamente.
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Título del Problema *</label>
                            <input type="text" class="form-control" id="problemTitle" required placeholder="Ej: Falta de material en zona A">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Prioridad *</label>
                            <select class="form-select" id="problemPriority" required>
                                <option value="">Seleccionar...</option>
                                <option value="low">🟢 Baja - Puede esperar</option>
                                <option value="medium">🟡 Media - Atención próximamente</option>
                                <option value="high">🔴 Alta - Requiere atención inmediata</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Categoría</label>
                            <select class="form-select" id="problemCategory">
                                <option value="materials">Materiales</option>
                                <option value="equipment">Equipos</option>
                                <option value="safety">Seguridad</option>
                                <option value="quality">Calidad</option>
                                <option value="other">Otro</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción Detallada *</label>
                            <textarea class="form-control" id="problemDescription" rows="4" required placeholder="Describe el problema con el mayor detalle posible..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Ubicación</label>
                            <input type="text" class="form-control" id="problemLocation" placeholder="Ej: Planta 2, Sección B">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger-custom">
                            <i class="bi bi-send me-2"></i>Enviar Reporte
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Detalles de Tarea -->
    <div class="modal fade" id="modalTaskDetail" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Tarea</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="taskDetailContent"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary-custom" id="btnUpdateTaskStatus">
                        <i class="bi bi-arrow-right me-2"></i>Actualizar Estado
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
        let currentTask = null;
        let workerData = {
            id: Date.now(),
            name: '{{ Auth::check() ? Auth::user()->name : "Trabajador" }}',
            email: '{{ Auth::check() ? Auth::user()->email : "" }}',
            project: 'Sin proyecto asignado',
            projectId: null
        };

        // ============== INICIALIZACIÓN ==============
        document.addEventListener('DOMContentLoaded', () => {
            loadTasks();
            loadProblems();
            loadWorkerData();
            updateUI();
        });

        // ============== FUNCIONES DE CARGA Y GUARDADO ==============
        function loadTasks() {
            const stored = localStorage.getItem('worker_tasks_' + workerData.id);
            if (stored) tasks = JSON.parse(stored);
        }

        function saveTasks() {
            localStorage.setItem('worker_tasks_' + workerData.id, JSON.stringify(tasks));
        }

        function loadProblems() {
            const stored = localStorage.getItem('worker_problems_' + workerData.id);
            if (stored) problems = JSON.parse(stored);
        }

        function saveProblems() {
            localStorage.setItem('worker_problems_' + workerData.id, JSON.stringify(problems));
        }

        function loadWorkerData() {
            const stored = localStorage.getItem('worker_data_' + workerData.email);
            if (stored) {
                const data = JSON.parse(stored);
                workerData = { ...workerData, ...data };
            }
            document.getElementById('workerName').textContent = workerData.name;
        }

        // ============== FUNCIONES DE NAVEGACIÓN ==============
        function showSection(id) {
            document.querySelectorAll('.content-section').forEach(s => s.classList.add('d-none'));
            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            document.getElementById(id).classList.remove('d-none');
            
            if (event && event.target) {
                const link = event.target.closest('.nav-link');
                if (link) link.classList.add('active');
            }
            
            if (id === 'tasks') {
                filterTasks();
            } else if (id === 'project') {
                showProjectDetails();
            } else if (id === 'problems') {
                updateProblemsList();
            } else if (id === 'history') {
                updateHistory();
            }
        }

        // ============== FUNCIONES DE UI ==============
        function updateUI() {
            updateStats();
            updateOverview();
            updateProblemsList();
        }

        function updateStats() {
            const total = tasks.length;
            const pending = tasks.filter(t => t.status === 'pending').length;
            const completed = tasks.filter(t => t.status === 'completed').length;
            const totalProblems = problems.length;
            
            document.getElementById('totalTasks').textContent = total;
            document.getElementById('pendingTasks').textContent = pending;
            document.getElementById('completedTasks').textContent = completed;
            document.getElementById('totalProblems').textContent = totalProblems;
        }

        function updateOverview() {
            // Tareas urgentes
            const urgentTasks = tasks.filter(t => t.priority === 'high' && t.status !== 'completed');
            const urgentList = document.getElementById('urgentTasksList');
            
            if (urgentTasks.length === 0) {
                urgentList.innerHTML = '<p class="text-muted mb-0">No hay tareas urgentes en este momento</p>';
            } else {
                urgentList.innerHTML = urgentTasks.map(t => `
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                        <div>
                            <strong>${t.title}</strong>
                            <small class="d-block text-muted">${t.description}</small>
                        </div>
                        <button class="btn btn-sm btn-primary-custom" onclick="viewTask(${t.id})">Ver</button>
                    </div>
                `).join('');
            }
            
            // Proyecto actual
            const projectInfo = document.getElementById('currentProjectInfo');
            if (workerData.project && workerData.project !== 'Sin proyecto asignado') {
                projectInfo.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">${workerData.project}</h5>
                            <p class="text-muted mb-0">Proyecto activo</p>
                        </div>
                        <button class="btn btn-primary-custom" onclick="showSection('project')">Ver Detalles</button>
                    </div>
                `;
            } else {
                projectInfo.innerHTML = '<p class="text-muted mb-0">No estás asignado a ningún proyecto actualmente</p>';
            }
        }

        // ============== FUNCIONES DE TAREAS ==============
        function filterTasks() {
            const status = document.getElementById('filterTaskStatus').value;
            const priority = document.getElementById('filterTaskPriority').value;
            const search = document.getElementById('searchTask').value.toLowerCase();
            
            const filtered = tasks.filter(t => {
                const matchStatus = !status || t.status === status;
                const matchPriority = !priority || t.priority === priority;
                const matchSearch = !search || t.title.toLowerCase().includes(search) || t.description.toLowerCase().includes(search);
                return matchStatus && matchPriority && matchSearch;
            });
            
            const container = document.getElementById('tasksList');
            const emptyState = document.getElementById('emptyTasks');
            
            if (filtered.length === 0) {
                container.innerHTML = '';
                emptyState.classList.remove('d-none');
            } else {
                emptyState.classList.add('d-none');
                container.innerHTML = filtered.map(t => createTaskCard(t)).join('');
            }
        }

        function createTaskCard(task) {
            const statusBadge = getStatusBadge(task.status);
            const priorityBadge = getPriorityBadge(task.priority);
            const statusClass = `task-${task.status}`;
            
            return `
                <div class="card task-card ${statusClass} shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h5 class="mb-1">${task.title}</h5>
                                ${statusBadge} ${priorityBadge}
                            </div>
                            <button class="btn btn-sm btn-primary-custom" onclick="viewTask(${task.id})">
                                <i class="bi bi-eye"></i> Ver
                            </button>
                        </div>
                        <p class="text-muted mb-2">${task.description}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>${new Date(task.date).toLocaleDateString('es-ES')}
                            </small>
                            ${task.status !== 'completed' ? `
                                <button class="btn btn-sm btn-success" onclick="updateTaskStatus(${task.id})">
                                    <i class="bi bi-check-circle me-1"></i>Completar
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }

        function getStatusBadge(status) {
            const badges = {
                'pending': '<span class="badge badge-pending">Pendiente</span>',
                'progress': '<span class="badge badge-progress">En Progreso</span>',
                'completed': '<span class="badge badge-completed">Completada</span>'
            };
            return badges[status] || badges.pending;
        }

        function getPriorityBadge(priority) {
            const badges = {
                'low': '<span class="badge badge-low">Baja</span>',
                'medium': '<span class="badge badge-medium">Media</span>',
                'high': '<span class="badge badge-high">Alta</span>'
            };
            return badges[priority] || badges.low;
        }

        function viewTask(id) {
            const task = tasks.find(t => t.id === id);
            if (!task) return;
            
            currentTask = task;
            const content = document.getElementById('taskDetailContent');
            
            content.innerHTML = `
                <div class="row g-3">
                    <div class="col-12">
                        <h4>${task.title}</h4>
                        ${getStatusBadge(task.status)} ${getPriorityBadge(task.priority)}
                    </div>
                    <div class="col-12">
                        <label class="text-muted small">Descripción</label>
                        <p>${task.description}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Fecha de Asignación</label>
                        <p><i class="bi bi-calendar me-2"></i>${new Date(task.date).toLocaleDateString('es-ES')}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Prioridad</label>
                        <p>${task.priority === 'high' ? '🔴 Alta' : task.priority === 'medium' ? '🟡 Media' : '🟢 Baja'}</p>
                    </div>
                    ${task.notes ? `
                        <div class="col-12">
                            <label class="text-muted small">Notas Adicionales</label>
                            <p>${task.notes}</p>
                        </div>
                    ` : ''}
                </div>
            `;
            
            const btnUpdate = document.getElementById('btnUpdateTaskStatus');
            if (task.status === 'completed') {
                btnUpdate.classList.add('d-none');
            } else {
                btnUpdate.classList.remove('d-none');
                btnUpdate.onclick = () => updateTaskStatus(task.id);
            }
            
            new bootstrap.Modal(document.getElementById('modalTaskDetail')).show();
        }

        function updateTaskStatus(id) {
            const task = tasks.find(t => t.id === id);
            if (!task) return;
            
            if (task.status === 'pending') {
                task.status = 'progress';
                showToast('info', 'Actualizado', 'Tarea marcada como "En Progreso"');
            } else if (task.status === 'progress') {
                task.status = 'completed';
                showToast('success', 'Completada', '¡Excelente trabajo! Tarea completada');
                addToHistory('Completaste la tarea: ' + task.title);
            }
            
            saveTasks();
            updateUI();
            filterTasks();
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalTaskDetail'));
            if (modal) modal.hide();
        }

        // ============== FUNCIONES DE PROYECTO ==============
        function showProjectDetails() {
            const container = document.getElementById('projectDetails');
            
            if (!workerData.project || workerData.project === 'Sin proyecto asignado') {
                container.innerHTML = `
                    <div class="card shadow-sm text-center">
                        <div class="card-body py-5">
                            <i class="bi bi-building display-1 text-muted mb-3"></i>
                            <h3>Sin Proyecto Asignado</h3>
                            <p class="text-muted">Actualmente no estás asignado a ningún proyecto</p>
                        </div>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = `
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="mb-4">${workerData.project}</h3>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted">Tu Rol</small>
                                    <div class="fw-bold">Trabajador</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted">Tareas Asignadas</small>
                                    <div class="fw-bold">${tasks.length}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted">Tareas Completadas</small>
                                    <div class="fw-bold">${tasks.filter(t => t.status === 'completed').length}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted">Problemas Reportados</small>
                                    <div class="fw-bold">${problems.length}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h5>Progreso de Tareas</h5>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-success" style="width: ${tasks.length > 0 ? (tasks.filter(t => t.status === 'completed').length / tasks.length * 100) : 0}%">
                                    ${tasks.length > 0 ? Math.round(tasks.filter(t => t.status === 'completed').length / tasks.length * 100) : 0}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // ============== FUNCIONES DE PROBLEMAS ==============
        function saveProblem(e) {
            e.preventDefault();
            
            const problem = {
                id: Date.now(),
                title: document.getElementById('problemTitle').value,
                priority: document.getElementById('problemPriority').value,
                category: document.getElementById('problemCategory').value,
                description: document.getElementById('problemDescription').value,
                location: document.getElementById('problemLocation').value,
                date: new Date().toISOString(),
                workerName: workerData.name,
                status: 'pending'
            };
            
            problems.push(problem);
            saveProblems();
            
            // Enviar notificación al supervisor
            sendNotificationToSupervisor(problem);
            
            bootstrap.Modal.getInstance(document.getElementById('modalProblem')).hide();
            document.getElementById('problemForm').reset();
            
            showToast('success', 'Enviado', 'Problema reportado correctamente');
            addToHistory('Reportaste: ' + problem.title);
            
            updateUI();
            updateProblemsList();
        }

        function sendNotificationToSupervisor(problem) {
            const notifications = JSON.parse(localStorage.getItem('constructora_notifications') || '[]');
            
            notifications.push({
                id: Date.now(),
                workerId: workerData.id,
                projectId: workerData.projectId,
                type: 'problem',
                title: 'Problema Reportado: ' + problem.title,
                message: `${workerData.name} reportó: ${problem.description}`,
                date: new Date().toISOString(),
                read: false
            });
            
            localStorage.setItem('constructora_notifications', JSON.stringify(notifications));
        }

        function updateProblemsList() {
            const container = document.getElementById('problemsList');
            const emptyState = document.getElementById('emptyProblems');
            const listCard = document.getElementById('problemsListCard');
            
            if (problems.length === 0) {
                listCard.classList.add('d-none');
                emptyState.classList.remove('d-none');
            } else {
                emptyState.classList.add('d-none');
                listCard.classList.remove('d-none');
                
                container.innerHTML = problems.sort((a, b) => new Date(b.date) - new Date(a.date)).map(p => {
                    const priorityClass = `problem-${p.priority}`;
                    const priorityIcon = p.priority === 'high' ? '🔴' : p.priority === 'medium' ? '🟡' : '🟢';
                    
                    return `
                        <div class="card problem-card ${priorityClass} mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">${priorityIcon} ${p.title}</h6>
                                        <small class="text-muted">
                                            ${new Date(p.date).toLocaleString('es-ES')}
                                            ${p.location ? ` • ${p.location}` : ''}
                                        </small>
                                    </div>
                                    <span class="badge bg-secondary">${p.category}</span>
                                </div>
                                <p class="mt-2 mb-0">${p.description}</p>
                            </div>
                        </div>
                    `;
                }).join('');
            }
        }

        // ============== FUNCIONES DE HISTORIAL ==============
        function addToHistory(action) {
            const history = JSON.parse(localStorage.getItem('worker_history_' + workerData.id) || '[]');
            history.unshift({
                action: action,
                date: new Date().toISOString()
            });
            localStorage.setItem('worker_history_' + workerData.id, JSON.stringify(history.slice(0, 50))); // Mantener últimas 50
        }

        function updateHistory() {
            const history = JSON.parse(localStorage.getItem('worker_history_' + workerData.id) || '[]');
            const container = document.getElementById('historyTimeline');
            
            if (history.length === 0) {
                container.innerHTML = '<p class="text-muted">No hay actividad registrada</p>';
            } else {
                container.innerHTML = history.map(h => `
                    <div class="timeline-item">
                        <div class="card">
                            <div class="card-body p-2">
                                <small class="text-muted d-block">${new Date(h.date).toLocaleString('es-ES')}</small>
                                <div>${h.action}</div>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        }

        // ============== FUNCIONES DE UTILIDAD ==============
        function confirmLogout() {
            showCustomAlert(
                '<i class="bi bi-box-arrow-right text-warning" style="font-size:48px"></i>',
                'Cerrar Sesión',
                '¿Cerrar sesión?',
                [
                    { text: 'Cancelar', class: 'btn-secondary', action: closeCustomAlert },
                    { text: 'Salir', class: 'btn-primary-custom', action: logout }
                ]
            );
        }

        function logout() {
            closeCustomAlert();
            showToast('success', 'Cerrando', 'Hasta pronto...');
            setTimeout(() => {
                document.getElementById('logout-form').submit();
            }, 1500);
        }

        function showCustomAlert(icon, title, message, actions) {
            document.getElementById('alertIcon').innerHTML = icon;
            document.getElementById('alertTitle').textContent = title;
            document.getElementById('alertMessage').textContent = message;
            
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
            const icons = {
                success: '✅',
                error: '❌',
                warning: '⚠️',
                info: 'ℹ️'
            };
            
            document.getElementById('toastIcon').textContent = icons[type];
            document.getElementById('toastTitle').textContent = title;
            document.getElementById('toastMessage').textContent = message;
            
            new bootstrap.Toast(toast).show();
        }

        // ============== DATOS DE EJEMPLO ==============
        // Generar tareas de ejemplo si no hay ninguna
        if (tasks.length === 0) {
            tasks = [
                {
                    id: 1,
                    title: 'Instalación de tuberías',
                    description: 'Instalar sistema de tuberías en planta baja',
                    priority: 'high',
                    status: 'pending',
                    date: new Date().toISOString(),
                    notes: 'Revisar planos antes de comenzar'
                },
                {
                    id: 2,
                    title: 'Pintura de muros',
                    description: 'Aplicar primera capa de pintura en área común',
                    priority: 'medium',
                    status: 'progress',
                    date: new Date(Date.now() - 86400000).toISOString()
                },
                {
                    id: 3,
                    title: 'Limpieza de área',
                    description: 'Limpiar escombros de la zona de construcción',
                    priority: 'low',
                    status: 'completed',
                    date: new Date(Date.now() - 172800000).toISOString()
                }
            ];
            saveTasks();
        }

        // Datos de proyecto de ejemplo
        if (!workerData.project || workerData.project === 'Sin proyecto asignado') {
            workerData.project = 'Construcción de Edificio Central';
            workerData.projectId = 1;
        }

        console.log('✅ Dashboard del Trabajador cargado correctamente');
    </script>
</body>
</html>