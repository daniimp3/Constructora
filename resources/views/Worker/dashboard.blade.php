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
        .stat-card.purple { border-color: #8b5cf6; }
        
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
        
        .project-card {
            border-left: 4px solid var(--accent);
            transition: all 0.3s;
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
            <p class="company-subtitle mb-0">Panel de Trabajador</p>
        </div>
        
        <div class="bg-dark bg-opacity-25 m-3 p-3 rounded">
            <div class="d-flex align-items-center">
                <div class="bg-gradient rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                     style="width:45px;height:45px;background:linear-gradient(135deg,#3b82f6,#8b5cf6)">
                    {{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 2)) : 'TR' }}
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
                    <h2 class="mb-1">Bienvenido de Nuevo</h2>
                    <p class="text-muted mb-0">Resumen de tu actividad</p>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card stat-card blue shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h3 class="mb-0 fw-bold" id="totalProjects">0</h3>
                                    <small class="text-muted">Proyectos</small>
                                </div>
                                <div class="bg-primary bg-opacity-10 rounded p-2">
                                    <i class="bi bi-building fs-4 text-primary"></i>
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
                                    <h3 class="mb-0 fw-bold" id="totalTasks">0</h3>
                                    <small class="text-muted">Tareas Asignadas</small>
                                </div>
                                <div class="bg-warning bg-opacity-10 rounded p-2">
                                    <i class="bi bi-list-task fs-4 text-warning"></i>
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
                    <div class="card stat-card purple shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h3 class="mb-0 fw-bold" id="pendingTasks">0</h3>
                                    <small class="text-muted">Pendientes</small>
                                </div>
                                <div class="bg-danger bg-opacity-10 rounded p-2">
                                    <i class="bi bi-clock fs-4 text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- tareas recientes -->
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

        <!-- sección de proyectos -->
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

        <!-- detalles del proyecto -->
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
            
            <!-- info proyecto -->
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
            
            <!-- tareas poyecto -->
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

        <!-- seccion tareas -->
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
    <script src="{{ asset('js/trabajador-dashboard.js') }}"></script>
</body>
</html>