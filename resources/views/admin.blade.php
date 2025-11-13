<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Librerías para exportar -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    
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
        
        /* Logo Container */
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
        
        .company-name {
            color: white;
            text-align: center;
            margin-top: 10px;
            font-weight: 600;
            font-size: 1.1rem;
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
        
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-completed { background: #dbeafe; color: #1e40af; }
        .badge-paused { background: #fef3c7; color: #92400e; }
        .badge-trabajador { background: #e0e7ff; color: #3730a3; }
        .badge-supervisor { background: #fce7f3; color: #831843; }
        .badge-admin { background: #fef3c7; color: #92400e; }
        
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
        
        .notification-item {
            border-left: 3px solid #ef4444;
            transition: all 0.3s;
        }
        
        .notification-item:hover {
            background: #fef2f2;
            transform: translateX(5px);
        }
        
        .notification-item.read {
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
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-280px); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
    
    <div class="sidebar">
        
        <div class="logo-container">
            
            <img src="{{ asset('img/logo.png') }}" alt="Logo Empresa" class="company-logo">
            
            <p class="company-subtitle mb-0">Panel de Administración</p>
        </div>
        
        
        <div class="bg-dark bg-opacity-25 m-3 p-3 rounded">
            <div class="d-flex align-items-center">
                <div class="bg-gradient rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                     style="width:45px;height:45px;background:linear-gradient(135deg,#3b82f6,#8b5cf6)">
                    {{ Auth::check() ? substr(Auth::user()->name, 0, 2) : 'AD' }}
                </div>
                <div class="ms-3">
                    <div class="text-white fw-semibold">{{ Auth::check() ? Auth::user()->name : 'Administrador' }}</div>
                    <small class="text-white-50">Administrador</small>
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
                <a class="nav-link" href="#" onclick="showSection('reports');return false">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i>Reportes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link position-relative" href="#" onclick="showSection('notifications');return false">
                    <i class="bi bi-bell me-2"></i>Notificaciones
                    <span class="notification-badge d-none" id="notificationBadge">0</span>
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
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="mb-1">Centro de Control Ejecutivo</h2>
                        <p class="text-muted mb-0">Monitoreo integral de proyectos</p>
                    </div>
                    <button class="btn btn-primary-custom" onclick="showSection('projects')">
                        <i class="bi bi-building me-2"></i>Gestionar Proyectos
                    </button>
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
                                    <small class="text-muted">Proyectos Totales</small>
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
                                    <h3 class="mb-0 fw-bold" id="activeProjects">0</h3>
                                    <small class="text-muted">Proyectos Activos</small>
                                </div>
                                <div class="bg-success bg-opacity-10 rounded p-2">
                                    <i class="bi bi-lightning fs-4 text-success"></i>
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
                                    <h3 class="mb-0 fw-bold" id="totalBudget">$0</h3>
                                    <small class="text-muted">Presupuesto Total</small>
                                </div>
                                <div class="bg-warning bg-opacity-10 rounded p-2">
                                    <i class="bi bi-cash fs-4 text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card stat-card purple shadow-sm h-100" onclick="showSection('workers')">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h3 class="mb-0 fw-bold" id="totalWorkers">0</h3>
                                    <small class="text-muted">Personal Total</small>
                                </div>
                                <div class="bg-info bg-opacity-10 rounded p-2">
                                    <i class="bi bi-people fs-4 text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Projects Card -->
            <div class="card shadow-sm d-none" id="recentProjectsCard">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Proyectos Recientes</h5>
                    <button class="btn btn-sm btn-outline-secondary" onclick="showSection('projects')">
                        Ver todos
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Proyecto</th>
                                    <th>Cliente</th>
                                    <th>Estado</th>
                                    <th>Avance</th>
                                    <th>Presupuesto</th>
                                </tr>
                            </thead>
                            <tbody id="recentProjectsTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Empty State -->
            <div class="card shadow-sm text-center" id="emptyOverview">
                <div class="card-body py-5">
                    <i class="bi bi-building display-1 text-muted mb-3"></i>
                    <h3>Bienvenido al Sistema</h3>
                    <p class="text-muted">Comienza creando tu primer proyecto</p>
                    <button class="btn btn-primary-custom mt-3" onclick="showSection('projects')">
                        <i class="bi bi-plus-circle me-2"></i>Crear Proyecto
                    </button>
                </div>
            </div>
        </div>

        <!-- Sección de Proyectos -->
        <div id="projects" class="content-section d-none">
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="mb-1">Gestión de Proyectos</h2>
                        <p class="text-muted mb-0">Administra todos tus proyectos de construcción</p>
                    </div>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalProject" onclick="openCreateProjectModal()">
                        <i class="bi bi-plus-circle me-2"></i>Nuevo Proyecto
                    </button>
                </div>
            </div>
            
            <!-- Projects List Card -->
            <div class="card shadow-sm d-none" id="projectsListCard">
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="searchProject" placeholder="🔍 Buscar proyecto..." onkeyup="filterProjects()">
                        </div>
                        <div class="col-md-6">
                            <select class="form-select" id="filterStatus" onchange="filterProjects()">
                                <option value="">Todos los estados</option>
                                <option value="active">Activos</option>
                                <option value="paused">Pausados</option>
                                <option value="completed">Completados</option>
                            </select>
                        </div>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Proyecto</th>
                                    <th>Cliente</th>
                                    <th>Estado</th>
                                    <th>Avance</th>
                                    <th>Presupuesto</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="projectsTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Empty Projects State -->
            <div class="card shadow-sm text-center" id="emptyProjects">
                <div class="card-body py-5">
                    <i class="bi bi-folder display-1 text-muted mb-3"></i>
                    <h3>Sin Proyectos Registrados</h3>
                    <p class="text-muted">Crea tu primer proyecto para comenzar</p>
                    <button class="btn btn-primary-custom mt-3" data-bs-toggle="modal" data-bs-target="#modalProject" onclick="openCreateProjectModal()">
                        <i class="bi bi-plus-circle me-2"></i>Crear Proyecto
                    </button>
                </div>
            </div>
        </div>

        <!-- Sección de Personal/Trabajadores -->
        <div id="workers" class="content-section d-none">
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="mb-1">Gestión de Personal</h2>
                        <p class="text-muted mb-0">Administra tu equipo de trabajo</p>
                    </div>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalWorker" onclick="openCreateWorkerModal()">
                        <i class="bi bi-person-plus me-2"></i>Agregar Personal
                    </button>
                </div>
            </div>
            
            <!-- Workers List Card -->
            <div class="card shadow-sm d-none" id="workersListCard">
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="searchWorker" placeholder="🔍 Buscar trabajador..." onkeyup="filterWorkers()">
                        </div>
                        <div class="col-md-6">
                            <select class="form-select" id="filterRole" onchange="filterWorkers()">
                                <option value="">Todos los roles</option>
                                <option value="trabajador">Trabajadores</option>
                                <option value="supervisor">Supervisores</option>
                            </select>
                        </div>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Proyecto Asignado</th>
                                    <th>Teléfono</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="workersTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Empty Workers State -->
            <div class="card shadow-sm text-center" id="emptyWorkers">
                <div class="card-body py-5">
                    <i class="bi bi-people display-1 text-muted mb-3"></i>
                    <h3>Sin Personal Registrado</h3>
                    <p class="text-muted">Agrega a tu primer trabajador para comenzar</p>
                    <button class="btn btn-primary-custom mt-3" data-bs-toggle="modal" data-bs-target="#modalWorker" onclick="openCreateWorkerModal()">
                        <i class="bi bi-person-plus me-2"></i>Agregar Personal
                    </button>
                </div>
            </div>
        </div>

        <!-- Sección de Reportes -->
        <div id="reports" class="content-section d-none">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="mb-1">Reportes y Análisis</h2>
                    <p class="text-muted mb-0">Genera reportes detallados de tus proyectos</p>
                </div>
            </div>
            
            <!-- Report Options -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-bar-chart-fill fs-3 text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">Análisis de Presupuesto</h5>
                                    <small class="text-muted">Gastos vs Presupuesto</small>
                                </div>
                            </div>
                            <p class="card-text text-muted">Analiza las desviaciones en el presupuesto de cada proyecto</p>
                            <button class="btn btn-primary-custom w-100" onclick="generateBudgetReport()">
                                <i class="bi bi-file-earmark-bar-graph me-2"></i>Generar Reporte
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-graph-up fs-3 text-success"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">Comparación de Proyectos</h5>
                                    <small class="text-muted">Avances y retrasos</small>
                                </div>
                            </div>
                            <p class="card-text text-muted">Identifica retrasos y compara el avance entre proyectos</p>
                            <button class="btn btn-primary-custom w-100" onclick="showSection('compare')">
                                <i class="bi bi-eye me-2"></i>Ver Comparación
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Budget Report Section -->
            <div id="budgetReportSection" class="card shadow-sm d-none">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart-fill me-2"></i>
                        Análisis de Presupuesto por Proyecto
                    </h5>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-danger" onclick="exportReportToPDF()">
                            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                        </button>
                        <button class="btn btn-sm btn-outline-success" onclick="exportReportToExcel()">
                            <i class="bi bi-file-earmark-excel me-1"></i>Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="budgetReportContent"></div>
                </div>
            </div>
        </div>

        <!-- Sección de Comparación -->
        <div id="compare" class="content-section d-none">
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1">Comparación de Proyectos</h2>
                        <p class="text-muted mb-0">Analiza el progreso y detecta retrasos</p>
                    </div>
                    <button class="btn btn-outline-secondary" onclick="showSection('reports')">
                        <i class="bi bi-arrow-left me-2"></i>Volver a Reportes
                    </button>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <div id="compareContent">
                        <div class="text-center py-5">
                            <i class="bi bi-graph-up display-1 text-muted mb-3"></i>
                            <p class="text-muted">Selecciona al menos 2 proyectos activos para comparar</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Notificaciones -->
        <div id="notifications" class="content-section d-none">
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="mb-1">Centro de Notificaciones</h2>
                        <p class="text-muted mb-0">Reportes de problemas y alertas del sistema</p>
                    </div>
                    <button class="btn btn-outline-secondary" onclick="markAllAsRead()">
                        <i class="bi bi-check-all me-2"></i>Marcar todas como leídas
                    </button>
                </div>
            </div>
            
            <!-- Notifications List -->
            <div class="card shadow-sm d-none" id="notificationsListCard">
                <div class="card-body" id="notificationsList"></div>
            </div>
            
            <!-- Empty Notifications State -->
            <div class="card shadow-sm text-center" id="emptyNotifications">
                <div class="card-body py-5">
                    <i class="bi bi-bell-slash display-1 text-muted mb-3"></i>
                    <h3>Sin Notificaciones</h3>
                    <p class="text-muted">No hay alertas o reportes pendientes en este momento</p>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALES -->
    
    <!-- Modal de Proyecto -->
    <div class="modal fade" id="modalProject" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProjectTitle">
                        <i class="bi bi-building me-2"></i>Nuevo Proyecto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="projectForm" onsubmit="saveProject(event)">
                    <div class="modal-body">
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
                                <label class="form-label">Estado</label>
                                <select class="form-select" id="projectStatus">
                                    <option value="active">Activo</option>
                                    <option value="paused">Pausado</option>
                                    <option value="completed">Completado</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Avance (%)</label>
                                <input type="number" class="form-control" id="projectProgress" min="0" max="100" value="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Presupuesto Inicial</label>
                                <input type="number" class="form-control" id="projectBudgetInitial" step="0.01" value="0" placeholder="0.00">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea class="form-control" id="projectDescription" rows="3" placeholder="Descripción opcional del proyecto..."></textarea>
                            </div>
                        </div>
                        <input type="hidden" id="projectId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-save me-2"></i>Guardar Proyecto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Presupuesto -->
    <div class="modal fade" id="modalBudget" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-cash-stack me-2"></i>Gestión de Presupuesto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="budgetForm" onsubmit="saveBudget(event)">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Presupuesto Total *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="budgetTotal" step="0.01" required>
                            </div>
                        </div>
                        <hr>
                        <h6 class="mb-3">Registrar Nuevo Gasto</h6>
                        <div class="mb-3">
                            <label class="form-label">Monto del Gasto</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="budgetExpense" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción del Gasto</label>
                            <input type="text" class="form-control" id="expenseDescription" placeholder="Ej: Materiales de construcción, Mano de obra...">
                        </div>
                        <input type="hidden" id="budgetProjectId">
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

    <!-- Modal de Ver Proyecto -->
    <div class="modal fade" id="modalView" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-eye me-2"></i>Detalles del Proyecto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="projectDetails"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary-custom" onclick="editCurrentProject()">
                        <i class="bi bi-pencil me-2"></i>Editar Proyecto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Trabajador -->
    <div class="modal fade" id="modalWorker" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalWorkerTitle">
                        <i class="bi bi-person-plus me-2"></i>Agregar Personal
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="workerForm" onsubmit="saveWorker(event)">
                    <div class="modal-body">
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
                                    <option value="">Seleccionar...</option>
                                    <option value="trabajador">Trabajador</option>
                                    <option value="supervisor">Supervisor</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contraseña *</label>
                                <input type="password" class="form-control" id="workerPassword" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Proyecto Asignado</label>
                                <select class="form-select" id="workerProject">
                                    <option value="">Sin asignar</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Especialidad</label>
                                <input type="text" class="form-control" id="workerSpecialty" placeholder="Ej: Albañilería, Electricidad, Plomería...">
                            </div>
                        </div>
                        <input type="hidden" id="workerId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-save me-2"></i>Guardar Personal
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
let projects = [];
let workers = [];
let notifications = [];
let currentProjectId = null;
let currentWorkerId = null;

// ============== INICIALIZACIÓN ==============
document.addEventListener('DOMContentLoaded', () => {
    loadProjects();
    loadWorkers();
    loadNotifications();
    updateUI();
    updateNotificationBadge();
    
    // Verificar notificaciones cada 30 segundos
    setInterval(updateNotificationBadge, 30000);
});

// ============== FUNCIONES DE CARGA Y GUARDADO ==============
function loadProjects() {
    const stored = localStorage.getItem('constructora_projects');
    if (stored) projects = JSON.parse(stored);
}

function saveProjects() {
    localStorage.setItem('constructora_projects', JSON.stringify(projects));
}

function loadWorkers() {
    const stored = localStorage.getItem('constructora_workers');
    if (stored) workers = JSON.parse(stored);
}

function saveWorkers() {
    localStorage.setItem('constructora_workers', JSON.stringify(workers));
}

function loadNotifications() {
    const stored = localStorage.getItem('constructora_notifications');
    if (stored) notifications = JSON.parse(stored);
}

function saveNotifications() {
    localStorage.setItem('constructora_notifications', JSON.stringify(notifications));
}

// ============== FUNCIONES DE NAVEGACIÓN ==============
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
    
    if (id === 'notifications') {
        updateNotificationsList();
    } else if (id === 'compare') {
        showProjectComparison();
    } else if (id === 'overview') {
        updateOverview();
    } else if (id === 'projects') {
        updateProjectsList();
    } else if (id === 'workers') {
        updateWorkersList();
    }
}

// ============== FUNCIONES DE UI ==============
function updateUI() {
    updateStats();
    updateOverview();
    updateProjectsList();
    updateWorkersList();
    updateProjectOptionsInWorkerModal();
}

function updateStats() {
    const totalProj = projects.length;
    const activeProj = projects.filter(p => p.status === 'active').length;
    const totalBudg = projects.reduce((sum, p) => sum + (parseFloat(p.budget) || 0), 0);
    const totalWork = workers.length;
    
    document.getElementById('totalProjects').textContent = totalProj;
    document.getElementById('activeProjects').textContent = activeProj;
    document.getElementById('totalBudget').textContent = '$' + totalBudg.toLocaleString('es-MX', {minimumFractionDigits: 2});
    document.getElementById('totalWorkers').textContent = totalWork;
}

function updateOverview() {
    if (projects.length === 0) {
        document.getElementById('emptyOverview').classList.remove('d-none');
        document.getElementById('recentProjectsCard').classList.add('d-none');
    } else {
        document.getElementById('emptyOverview').classList.add('d-none');
        document.getElementById('recentProjectsCard').classList.remove('d-none');
        
        const tbody = document.getElementById('recentProjectsTable');
        tbody.innerHTML = '';
        
        const recentProjects = [...projects].reverse().slice(0, 5);
        recentProjects.forEach(p => tbody.appendChild(createRowSimple(p)));
    }
}

function updateProjectsList() {
    if (projects.length === 0) {
        document.getElementById('emptyProjects').classList.remove('d-none');
        document.getElementById('projectsListCard').classList.add('d-none');
    } else {
        document.getElementById('emptyProjects').classList.add('d-none');
        document.getElementById('projectsListCard').classList.remove('d-none');
        filterProjects();
    }
}

function updateWorkersList() {
    if (workers.length === 0) {
        document.getElementById('emptyWorkers').classList.remove('d-none');
        document.getElementById('workersListCard').classList.add('d-none');
    } else {
        document.getElementById('emptyWorkers').classList.add('d-none');
        document.getElementById('workersListCard').classList.remove('d-none');
        filterWorkers();
    }
}

function updateProjectOptionsInWorkerModal() {
    const select = document.getElementById('workerProject');
    if (!select) return;
    
    select.innerHTML = '<option value="">Sin asignar</option>';
    projects.forEach(p => {
        const option = document.createElement('option');
        option.value = p.id;
        option.textContent = p.name;
        select.appendChild(option);
    });
}

// ============== FUNCIONES DE PROYECTOS ==============
function createRowSimple(p) {
    const row = document.createElement('tr');
    row.style.cursor = 'pointer';
    row.onclick = () => viewProject(p.id);
    
    const badge = getStatusBadge(p.status);
    const budgetDisplay = p.budget ? `$${parseFloat(p.budget).toLocaleString('es-MX')}` : '$0';
    
    row.innerHTML = `
        <td><strong>${p.name}</strong></td>
        <td>${p.client}</td>
        <td>${badge}</td>
        <td>
            <div class="d-flex align-items-center">
                <span class="me-2">${p.progress}%</span>
                <div class="progress flex-grow-1" style="height:8px; min-width:80px;">
                    <div class="progress-bar" style="width:${p.progress}%"></div>
                </div>
            </div>
        </td>
        <td>${budgetDisplay}</td>
    `;
    
    return row;
}

function createRowFull(p) {
    const row = document.createElement('tr');
    row.style.cursor = 'pointer';
    row.onclick = () => viewProject(p.id);
    
    const badge = getStatusBadge(p.status);
    const budgetDisplay = p.budget ? `$${parseFloat(p.budget).toLocaleString('es-MX')}` : '$0';
    
    row.innerHTML = `
        <td><strong>${p.name}</strong></td>
        <td>${p.client}</td>
        <td>${badge}</td>
        <td>
            <div class="d-flex align-items-center">
                <span class="me-2">${p.progress}%</span>
                <div class="progress flex-grow-1" style="height:8px; min-width:80px;">
                    <div class="progress-bar" style="width:${p.progress}%"></div>
                </div>
            </div>
        </td>
        <td>${budgetDisplay}</td>
        <td onclick="event.stopPropagation()" class="text-center">
            <button class="btn btn-sm btn-info btn-action me-1" onclick="viewProject(${p.id})" title="Ver detalles">
                <i class="bi bi-eye"></i>
            </button>
            <button class="btn btn-sm btn-success btn-action me-1" onclick="openBudgetModal(${p.id})" title="Gestionar presupuesto">
                <i class="bi bi-cash"></i>
            </button>
            <button class="btn btn-sm btn-warning btn-action me-1" onclick="editProject(${p.id})" title="Editar">
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-danger btn-action" onclick="confirmDelete(${p.id})" title="Eliminar">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    
    return row;
}

function getStatusBadge(status) {
    const badges = {
        'active': '<span class="badge badge-active">Activo</span>',
        'completed': '<span class="badge badge-completed">Completado</span>',
        'paused': '<span class="badge badge-paused">Pausado</span>'
    };
    return badges[status] || badges.active;
}

function getRoleBadge(role) {
    const badges = {
        'trabajador': '<span class="badge badge-trabajador">Trabajador</span>',
        'supervisor': '<span class="badge badge-supervisor">Supervisor</span>',
        'administrador': '<span class="badge badge-admin">Administrador</span>'
    };
    return badges[role] || badges.trabajador;
}

function filterProjects() {
    const search = document.getElementById('searchProject').value.toLowerCase();
    const status = document.getElementById('filterStatus').value;
    
    const filtered = projects.filter(p => {
        const matchSearch = p.name.toLowerCase().includes(search) || 
                          p.client.toLowerCase().includes(search);
        const matchStatus = !status || p.status === status;
        return matchSearch && matchStatus;
    });
    
    const tbody = document.getElementById('projectsTable');
    tbody.innerHTML = '';
    
    if (filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No se encontraron proyectos</td></tr>';
    } else {
        filtered.forEach(p => tbody.appendChild(createRowFull(p)));
    }
}

function openCreateProjectModal() {
    document.getElementById('modalProjectTitle').innerHTML = '<i class="bi bi-building me-2"></i>Nuevo Proyecto';
    document.getElementById('projectForm').reset();
    document.getElementById('projectProgress').value = '0';
    document.getElementById('projectBudgetInitial').value = '0';
    currentProjectId = null;
}

function saveProject(e) {
    e.preventDefault();
    
    const data = {
        name: document.getElementById('projectName').value,
        client: document.getElementById('projectClient').value,
        startDate: document.getElementById('projectStartDate').value,
        status: document.getElementById('projectStatus').value,
        progress: parseInt(document.getElementById('projectProgress').value) || 0,
        description: document.getElementById('projectDescription').value,
        budget: parseFloat(document.getElementById('projectBudgetInitial').value) || 0,
        spent: 0,
        expenses: []
    };
    
    if (currentProjectId) {
        const index = projects.findIndex(p => p.id === currentProjectId);
        const oldProject = projects[index];
        projects[index] = { 
            ...oldProject, 
            ...data,
            spent: oldProject.spent || 0,
            expenses: oldProject.expenses || []
        };
        showToast('success', 'Actualizado', 'Proyecto actualizado correctamente');
    } else {
        data.id = Date.now();
        projects.push(data);
        showToast('success', 'Creado', 'Proyecto creado correctamente');
    }
    
    saveProjects();
    bootstrap.Modal.getInstance(document.getElementById('modalProject')).hide();
    updateUI();
}

function editProject(id) {
    const project = projects.find(p => p.id === id);
    if (!project) return;
    
    currentProjectId = id;
    document.getElementById('modalProjectTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Proyecto';
    document.getElementById('projectName').value = project.name;
    document.getElementById('projectClient').value = project.client;
    document.getElementById('projectStartDate').value = project.startDate;
    document.getElementById('projectStatus').value = project.status;
    document.getElementById('projectProgress').value = project.progress;
    document.getElementById('projectDescription').value = project.description || '';
    document.getElementById('projectBudgetInitial').value = project.budget || 0;
    
    new bootstrap.Modal(document.getElementById('modalProject')).show();
}

function viewProject(id) {
    const project = projects.find(p => p.id === id);
    if (!project) return;
    
    currentProjectId = id;
    const container = document.getElementById('projectDetails');
    
    const budget = parseFloat(project.budget) || 0;
    const spent = parseFloat(project.spent) || 0;
    const budgetPercent = budget > 0 ? ((spent / budget) * 100).toFixed(1) : 0;
    const budgetColor = budgetPercent >= 100 ? 'danger' : budgetPercent >= 90 ? 'warning' : budgetPercent >= 75 ? 'info' : 'success';
    
    let expensesHTML = '';
    if (project.expenses && project.expenses.length > 0) {
        expensesHTML = '<div class="mt-4"><h6 class="mb-3"><i class="bi bi-receipt me-2"></i>Historial de Gastos:</h6><div class="table-responsive"><table class="table table-sm"><thead><tr><th>Fecha</th><th>Descripción</th><th class="text-end">Monto</th></tr></thead><tbody>';
        project.expenses.forEach(exp => {
            const date = new Date(exp.date).toLocaleDateString('es-MX');
            expensesHTML += `<tr>
                <td><small>${date}</small></td>
                <td>${exp.description}</td>
                <td class="text-end"><strong>$${parseFloat(exp.amount).toLocaleString('es-MX')}</strong></td>
            </tr>`;
        });
        expensesHTML += '</tbody></table></div></div>';
    } else {
        expensesHTML = '<div class="alert alert-info mt-4"><i class="bi bi-info-circle me-2"></i>No se han registrado gastos para este proyecto</div>';
    }
    
    container.innerHTML = `
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Proyecto</small>
                        <h5 class="mb-0">${project.name}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Cliente</small>
                        <h5 class="mb-0">${project.client}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Estado</small>
                        <div>${getStatusBadge(project.status)}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Fecha de Inicio</small>
                        <strong>${new Date(project.startDate).toLocaleDateString('es-MX')}</strong>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Avance del Proyecto</small>
                        <div class="d-flex align-items-center">
                            <strong class="me-2">${project.progress}%</strong>
                            <div class="progress flex-grow-1" style="height:8px">
                                <div class="progress-bar" style="width:${project.progress}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            ${project.description ? `
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Descripción</small>
                        <p class="mb-0">${project.description}</p>
                    </div>
                </div>
            </div>
            ` : ''}
        </div>
        
        <div class="card border-${budgetColor}">
            <div class="card-header bg-${budgetColor} bg-opacity-10 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Control de Presupuesto</h6>
                <button class="btn btn-sm btn-${budgetColor}" onclick="openBudgetModal(${project.id})">
                    <i class="bi bi-pencil me-1"></i>Gestionar
                </button>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-4 text-center">
                        <small class="text-muted d-block">Presupuesto Total</small>
                        <h4 class="mb-0 text-primary">$${budget.toLocaleString('es-MX')}</h4>
                    </div>
                    <div class="col-md-4 text-center">
                        <small class="text-muted d-block">Total Gastado</small>
                        <h4 class="mb-0 text-${budgetColor}">$${spent.toLocaleString('es-MX')}</h4>
                    </div>
                    <div class="col-md-4 text-center">
                        <small class="text-muted d-block">Disponible</small>
                        <h4 class="mb-0 text-success">$${(budget - spent).toLocaleString('es-MX')}</h4>
                    </div>
                </div>
                <div class="progress mb-2" style="height:20px">
                    <div class="progress-bar bg-${budgetColor}" style="width:${Math.min(budgetPercent, 100)}%">
                        ${budgetPercent}%
                    </div>
                </div>
                <small class="text-muted">Porcentaje de presupuesto utilizado</small>
                ${expensesHTML}
            </div>
        </div>
    `;
    
    new bootstrap.Modal(document.getElementById('modalView')).show();
}

function editCurrentProject() {
    bootstrap.Modal.getInstance(document.getElementById('modalView')).hide();
    setTimeout(() => editProject(currentProjectId), 300);
}

function openBudgetModal(id) {
    const project = projects.find(p => p.id === id);
    if (!project) return;
    
    currentProjectId = id;
    document.getElementById('budgetTotal').value = project.budget || 0;
    document.getElementById('budgetExpense').value = '';
    document.getElementById('expenseDescription').value = '';
    
    const viewModal = bootstrap.Modal.getInstance(document.getElementById('modalView'));
    if (viewModal) viewModal.hide();
    
    setTimeout(() => {
        new bootstrap.Modal(document.getElementById('modalBudget')).show();
    }, 300);
}

function saveBudget(e) {
    e.preventDefault();
    
    const project = projects.find(p => p.id === currentProjectId);
    if (!project) return;
    
    const budget = parseFloat(document.getElementById('budgetTotal').value) || 0;
    const expense = parseFloat(document.getElementById('budgetExpense').value) || 0;
    const description = document.getElementById('expenseDescription').value.trim();
    
    project.budget = budget;
    
    if (expense > 0 && description) {
        if (!project.expenses) project.expenses = [];
        project.expenses.push({
            amount: expense,
            description: description,
            date: new Date().toISOString()
        });
        project.spent = (project.spent || 0) + expense;
        
        const percentage = (project.spent / project.budget) * 100;
        if (percentage >= 90) {
            const notification = {
                id: Date.now(),
                workerId: null,
                projectId: project.id,
                type: 'alert',
                title: '⚠️ Alerta de Presupuesto',
                message: `El proyecto "${project.name}" ha superado el ${percentage.toFixed(1)}% del presupuesto. Gasto actual: $${project.spent.toLocaleString('es-MX')} de $${project.budget.toLocaleString('es-MX')}`,
                date: new Date().toISOString(),
                read: false
            };
            notifications.push(notification);
            saveNotifications();
            updateNotificationBadge();
        }
    }
    
    saveProjects();
    bootstrap.Modal.getInstance(document.getElementById('modalBudget')).hide();
    showToast('success', 'Actualizado', 'Presupuesto actualizado correctamente');
    updateUI();
}

function confirmDelete(id) {
    const project = projects.find(p => p.id === id);
    if (!project) return;
    
    showCustomAlert(
        '<i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:60px"></i>',
        'Confirmar Eliminación',
        `¿Estás seguro de eliminar el proyecto "${project.name}"? Esta acción no se puede deshacer.`,
        [
            { text: 'Cancelar', class: 'btn-secondary', action: closeCustomAlert },
            { text: 'Eliminar', class: 'btn-danger', action: () => deleteProject(id) }
        ]
    );
}

function deleteProject(id) {
    projects = projects.filter(p => p.id !== id);
    saveProjects();
    closeCustomAlert();
    showToast('success', 'Eliminado', 'Proyecto eliminado correctamente');
    updateUI();
}

// ============== FUNCIONES DE TRABAJADORES ==============
function filterWorkers() {
    const search = document.getElementById('searchWorker').value.toLowerCase();
    const role = document.getElementById('filterRole').value;
    
    const filtered = workers.filter(w => {
        const matchSearch = w.name.toLowerCase().includes(search) || 
                          w.email.toLowerCase().includes(search);
        const matchRole = !role || w.role === role;
        return matchSearch && matchRole;
    });
    
    const tbody = document.getElementById('workersTable');
    tbody.innerHTML = '';
    
    if (filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No se encontraron trabajadores</td></tr>';
    } else {
        filtered.forEach(w => tbody.appendChild(createWorkerRow(w)));
    }
}

function createWorkerRow(worker) {
    const row = document.createElement('tr');
    
    const projectName = worker.projectId ? 
        (projects.find(p => p.id === worker.projectId)?.name || 'N/A') : 
        '<span class="text-muted">Sin asignar</span>';
    
    row.innerHTML = `
        <td><strong>${worker.name}</strong></td>
        <td>${worker.email}</td>
        <td>${getRoleBadge(worker.role)}</td>
        <td>${projectName}</td>
        <td>${worker.phone}</td>
        <td class="text-center">
            <button class="btn btn-sm btn-warning btn-action me-1" onclick="editWorker(${worker.id})" title="Editar">
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-danger btn-action" onclick="confirmDeleteWorker(${worker.id})" title="Eliminar">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    
    return row;
}

function openCreateWorkerModal() {
    document.getElementById('modalWorkerTitle').innerHTML = '<i class="bi bi-person-plus me-2"></i>Agregar Personal';
    document.getElementById('workerForm').reset();
    currentWorkerId = null;
    updateProjectOptionsInWorkerModal();
}

function saveWorker(e) {
    e.preventDefault();
    
    const data = {
        name: document.getElementById('workerName').value,
        email: document.getElementById('workerEmail').value,
        phone: document.getElementById('workerPhone').value,
        role: document.getElementById('workerRole').value,
        password: document.getElementById('workerPassword').value,
        projectId: parseInt(document.getElementById('workerProject').value) || null,
        specialty: document.getElementById('workerSpecialty').value
    };
    
    if (currentWorkerId) {
        const index = workers.findIndex(w => w.id === currentWorkerId);
        workers[index] = { ...workers[index], ...data };
        showToast('success', 'Actualizado', 'Datos actualizados correctamente');
    } else {
        data.id = Date.now();
        workers.push(data);
        showToast('success', 'Creado', 'Personal agregado correctamente');
    }
    
    saveWorkers();
    bootstrap.Modal.getInstance(document.getElementById('modalWorker')).hide();
    updateUI();
}

function editWorker(id) {
    const worker = workers.find(w => w.id === id);
    if (!worker) return;
    
    currentWorkerId = id;
    document.getElementById('modalWorkerTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Trabajador';
    document.getElementById('workerName').value = worker.name;
    document.getElementById('workerEmail').value = worker.email;
    document.getElementById('workerPhone').value = worker.phone;
    document.getElementById('workerRole').value = worker.role;
    document.getElementById('workerPassword').value = worker.password;
    document.getElementById('workerProject').value = worker.projectId || '';
    document.getElementById('workerSpecialty').value = worker.specialty || '';
    
    updateProjectOptionsInWorkerModal();
    new bootstrap.Modal(document.getElementById('modalWorker')).show();
}

function confirmDeleteWorker(id) {
    const worker = workers.find(w => w.id === id);
    if (!worker) return;
    
    showCustomAlert(
        '<i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:60px"></i>',
        'Confirmar Eliminación',
        `¿Estás seguro de eliminar a "${worker.name}"? Esta acción no se puede deshacer.`,
        [
            { text: 'Cancelar', class: 'btn-secondary', action: closeCustomAlert },
            { text: 'Eliminar', class: 'btn-danger', action: () => deleteWorker(id) }
        ]
    );
}

function deleteWorker(id) {
    workers = workers.filter(w => w.id !== id);
    saveWorkers();
    closeCustomAlert();
    showToast('success', 'Eliminado', 'Trabajador eliminado correctamente');
    updateUI();
}

// ============== FUNCIONES DE REPORTES ==============
function generateBudgetReport() {
    const section = document.getElementById('budgetReportSection');
    const content = document.getElementById('budgetReportContent');
    
    if (projects.length === 0) {
        content.innerHTML = '<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>No hay proyectos registrados para generar el reporte</div>';
        section.classList.remove('d-none');
        return;
    }
    
    let html = '<div class="table-responsive"><table class="table table-hover" id="budgetReportTable"><thead><tr><th>Proyecto</th><th>Presupuesto</th><th>Gastado</th><th>% Utilizado</th><th>Desviación</th><th>Estado</th></tr></thead><tbody>';
    
    projects.forEach(p => {
        const budget = parseFloat(p.budget) || 0;
        const spent = parseFloat(p.spent) || 0;
        const percent = budget > 0 ? ((spent / budget) * 100).toFixed(1) : 0;
        const deviation = spent - budget;
        const statusColor = percent >= 100 ? 'danger' : percent >= 90 ? 'warning' : percent >= 75 ? 'info' : 'success';
        const statusText = percent >= 100 ? 'Excedido' : percent >= 90 ? 'Crítico' : percent >= 75 ? 'Alerta' : 'Normal';
        
        html += `<tr>
            <td><strong>${p.name}</strong><br><small class="text-muted">${p.client}</small></td>
            <td>$${budget.toLocaleString('es-MX')}</td>
            <td>$${spent.toLocaleString('es-MX')}</td>
            <td>
                <div class="progress" style="height:20px">
                    <div class="progress-bar bg-${statusColor}" style="width:${Math.min(percent, 100)}%">${percent}%</div>
                </div>
            </td>
            <td class="${deviation > 0 ? 'text-danger' : 'text-success'} fw-bold">${deviation > 0 ? '+' : ''}$${deviation.toLocaleString('es-MX')}</td>
            <td><span class="badge bg-${statusColor}">${statusText}</span></td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    
    const totalBudget = projects.reduce((sum, p) => sum + (parseFloat(p.budget) || 0), 0);
    const totalSpent = projects.reduce((sum, p) => sum + (parseFloat(p.spent) || 0), 0);
    const totalPercent = totalBudget > 0 ? ((totalSpent / totalBudget) * 100).toFixed(1) : 0;
    const totalDeviation = totalSpent - totalBudget;
    
    html += `<div class="row g-3 mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <small class="d-block opacity-75">Presupuesto Total</small>
                    <h3 class="mb-0">$${totalBudget.toLocaleString('es-MX')}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <small class="d-block opacity-75">Total Gastado</small>
                    <h3 class="mb-0">$${totalSpent.toLocaleString('es-MX')}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <small class="d-block opacity-75">% Utilizado</small>
                    <h3 class="mb-0">${totalPercent}%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-${totalDeviation > 0 ? 'danger' : 'success'} text-white">
                <div class="card-body text-center">
                    <small class="d-block opacity-75">Desviación</small>
                    <h3 class="mb-0">${totalDeviation > 0 ? '+' : ''}$${totalDeviation.toLocaleString('es-MX')}</h3>
                </div>
            </div>
        </div>
    </div>`;
    
    content.innerHTML = html;
    section.classList.remove('d-none');
    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ============== EXPORTAR A PDF ==============
function exportReportToPDF() {
    if (projects.length === 0) {
        showToast('warning', 'Sin datos', 'No hay proyectos para exportar');
        return;
    }

    showToast('info', 'Generando', 'Creando reporte PDF...');
    
    try {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        // Título
        doc.setFontSize(18);
        doc.text('Reporte de Presupuestos', 14, 20);
        
        doc.setFontSize(11);
        doc.text(`Fecha: ${new Date().toLocaleDateString('es-MX')}`, 14, 30);
        
        // Preparar datos para la tabla
        const tableData = projects.map(p => {
            const budget = parseFloat(p.budget) || 0;
            const spent = parseFloat(p.spent) || 0;
            const percent = budget > 0 ? ((spent / budget) * 100).toFixed(1) : 0;
            const deviation = spent - budget;
            const statusText = percent >= 100 ? 'Excedido' : percent >= 90 ? 'Crítico' : percent >= 75 ? 'Alerta' : 'Normal';
            
            return [
                p.name,
                p.client,
                `$${budget.toLocaleString('es-MX')}`,
                `$${spent.toLocaleString('es-MX')}`,
                `${percent}%`,
                `${deviation > 0 ? '+' : ''}$${deviation.toLocaleString('es-MX')}`,
                statusText
            ];
        });
        
        // Agregar tabla
        doc.autoTable({
            startY: 40,
            head: [['Proyecto', 'Cliente', 'Presupuesto', 'Gastado', '% Usado', 'Desviación', 'Estado']],
            body: tableData,
            theme: 'striped',
            styles: { fontSize: 8 },
            headStyles: { fillColor: [13, 39, 61] }
        });
        
        // Resumen
        const totalBudget = projects.reduce((sum, p) => sum + (parseFloat(p.budget) || 0), 0);
        const totalSpent = projects.reduce((sum, p) => sum + (parseFloat(p.spent) || 0), 0);
        const totalPercent = totalBudget > 0 ? ((totalSpent / totalBudget) * 100).toFixed(1) : 0;
        
        const finalY = doc.lastAutoTable.finalY + 10;
        doc.setFontSize(12);
        doc.text('Resumen Total:', 14, finalY);
        doc.setFontSize(10);
        doc.text(`Presupuesto Total: $${totalBudget.toLocaleString('es-MX')}`, 14, finalY + 8);
        doc.text(`Total Gastado: $${totalSpent.toLocaleString('es-MX')}`, 14, finalY + 16);
        doc.text(`Porcentaje Utilizado: ${totalPercent}%`, 14, finalY + 24);
        
        // Descargar
        doc.save(`reporte-presupuestos-${new Date().getTime()}.pdf`);
        showToast('success', 'Completado', 'Reporte PDF descargado');
        
    } catch (error) {
        console.error('Error al generar PDF:', error);
        showToast('error', 'Error', 'No se pudo generar el PDF');
    }
}

// ============== EXPORTAR A EXCEL ==============
function exportReportToExcel() {
    if (projects.length === 0) {
        showToast('warning', 'Sin datos', 'No hay proyectos para exportar');
        return;
    }

    showToast('info', 'Generando', 'Creando archivo Excel...');
    
    try {
        // Preparar datos
        const data = projects.map(p => {
            const budget = parseFloat(p.budget) || 0;
            const spent = parseFloat(p.spent) || 0;
            const percent = budget > 0 ? ((spent / budget) * 100).toFixed(1) : 0;
            const deviation = spent - budget;
            const statusText = percent >= 100 ? 'Excedido' : percent >= 90 ? 'Crítico' : percent >= 75 ? 'Alerta' : 'Normal';
            
            return {
                'Proyecto': p.name,
                'Cliente': p.client,
                'Fecha Inicio': new Date(p.startDate).toLocaleDateString('es-MX'),
                'Estado': p.status === 'active' ? 'Activo' : p.status === 'paused' ? 'Pausado' : 'Completado',
                'Avance (%)': p.progress,
                'Presupuesto': budget,
                'Gastado': spent,
                '% Utilizado': parseFloat(percent),
                'Desviación': deviation,
                'Estado Presupuesto': statusText
            };
        });
        
        // Agregar resumen
        const totalBudget = projects.reduce((sum, p) => sum + (parseFloat(p.budget) || 0), 0);
        const totalSpent = projects.reduce((sum, p) => sum + (parseFloat(p.spent) || 0), 0);
        const totalPercent = totalBudget > 0 ? ((totalSpent / totalBudget) * 100).toFixed(1) : 0;
        const totalDeviation = totalSpent - totalBudget;
        
        data.push({});
        data.push({
            'Proyecto': 'TOTALES',
            'Presupuesto': totalBudget,
            'Gastado': totalSpent,
            '% Utilizado': parseFloat(totalPercent),
            'Desviación': totalDeviation
        });
        
        // Crear hoja de cálculo
        const ws = XLSX.utils.json_to_sheet(data);
        
        // Ajustar anchos de columna
        const wscols = [
            {wch:25}, // Proyecto
            {wch:20}, // Cliente
            {wch:12}, // Fecha Inicio
            {wch:12}, // Estado
            {wch:10}, // Avance
            {wch:15}, // Presupuesto
            {wch:15}, // Gastado
            {wch:12}, // % Utilizado
            {wch:15}, // Desviación
            {wch:18}  // Estado Presupuesto
        ];
        ws['!cols'] = wscols;
        
        // Crear libro
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Reporte Presupuestos');
        
        // Descargar
        XLSX.writeFile(wb, `reporte-presupuestos-${new Date().getTime()}.xlsx`);
        showToast('success', 'Completado', 'Archivo Excel descargado');
        
    } catch (error) {
        console.error('Error al generar Excel:', error);
        showToast('error', 'Error', 'No se pudo generar el archivo Excel');
    }
}

function showProjectComparison() {
    const content = document.getElementById('compareContent');
    const activeProjects = projects.filter(p => p.status === 'active');
    
    if (activeProjects.length < 2) {
        content.innerHTML = `
            <div class="text-center py-5">
                <i class="bi bi-info-circle display-1 text-muted mb-3"></i>
                <h5>Se necesitan al menos 2 proyectos activos</h5>
                <p class="text-muted">Crea más proyectos para poder compararlos</p>
            </div>
        `;
        return;
    }
    
    let html = '<div class="table-responsive"><table class="table table-hover"><thead><tr><th>Proyecto</th><th>Cliente</th><th>Avance</th><th>Días Transcurridos</th><th>Estado</th></tr></thead><tbody>';
    
    activeProjects.forEach(p => {
        const startDate = new Date(p.startDate);
        const today = new Date();
        const daysElapsed = Math.floor((today - startDate) / (1000 * 60 * 60 * 24));
        
        let progressStatus = '';
        let progressColor = '';
        
        if (p.progress >= 75) {
            progressStatus = 'Adelantado';
            progressColor = 'success';
        } else if (p.progress >= 50) {
            progressStatus = 'En tiempo';
            progressColor = 'info';
        } else if (p.progress >= 25) {
            progressStatus = 'Con retraso';
            progressColor = 'warning';
        } else {
            progressStatus = 'Muy retrasado';
            progressColor = 'danger';
        }
        
        html += `<tr>
            <td><strong>${p.name}</strong></td>
            <td>${p.client}</td>
            <td>
                <div class="d-flex align-items-center">
                    <span class="me-2">${p.progress}%</span>
                    <div class="progress flex-grow-1" style="height:8px; min-width:100px;">
                        <div class="progress-bar bg-${progressColor}" style="width:${p.progress}%"></div>
                    </div>
                </div>
            </td>
            <td>${daysElapsed} días</td>
            <td><span class="badge bg-${progressColor}">${progressStatus}</span></td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    
    html += '<div class="mt-4"><h6 class="mb-3"><i class="bi bi-graph-up me-2"></i>Comparación Visual de Avances</h6><div class="row g-3">';
    
    activeProjects.forEach(p => {
        const progressColor = p.progress >= 75 ? 'success' : p.progress >= 50 ? 'info' : p.progress >= 25 ? 'warning' : 'danger';
        const budget = parseFloat(p.budget) || 0;
        const spent = parseFloat(p.spent) || 0;
        
        html += `<div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">${p.name}</h6>
                    <div class="mb-2">
                        <small class="text-muted">Avance del Proyecto</small>
                        <div class="progress mt-1" style="height:25px">
                            <div class="progress-bar bg-${progressColor}" style="width:${p.progress}%">
                                <strong>${p.progress}%</strong>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-3">
                        <small class="text-muted">Presupuesto: <strong>$${budget.toLocaleString('es-MX')}</strong></small>
                        <small class="text-muted">Gastado: <strong>$${spent.toLocaleString('es-MX')}</strong></small>
                    </div>
                </div>
            </div>
        </div>`;
    });
    
    html += '</div></div>';
    
    content.innerHTML = html;
}

// ============== FUNCIONES DE NOTIFICACIONES ==============
function updateNotificationBadge() {
    const unreadCount = notifications.filter(n => !n.read).length;
    const badge = document.getElementById('notificationBadge');
    
    if (unreadCount > 0) {
        badge.textContent = unreadCount;
        badge.classList.remove('d-none');
    } else {
        badge.classList.add('d-none');
    }
}

function updateNotificationsList() {
    const container = document.getElementById('notificationsList');
    
    if (notifications.length === 0) {
        document.getElementById('emptyNotifications').classList.remove('d-none');
        document.getElementById('notificationsListCard').classList.add('d-none');
    } else {
        document.getElementById('emptyNotifications').classList.add('d-none');
        document.getElementById('notificationsListCard').classList.remove('d-none');
        
        container.innerHTML = '';
        
        const sortedNotifications = [...notifications].sort((a, b) => new Date(b.date) - new Date(a.date));
        
        sortedNotifications.forEach(notif => {
            const card = createNotificationCard(notif);
            container.appendChild(card);
        });
    }
}

function createNotificationCard(notif) {
    const div = document.createElement('div');
    div.className = `card mb-3 notification-item ${notif.read ? 'read' : ''}`;
    
    const worker = workers.find(w => w.id === notif.workerId);
    const project = projects.find(p => p.id === notif.projectId);
    
    const date = new Date(notif.date);
    const formattedDate = date.toLocaleDateString('es-MX', { 
        day: '2-digit', 
        month: 'short', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    let icon = '<i class="bi bi-info-circle-fill text-primary fs-4"></i>';
    if (notif.type === 'problem') icon = '<i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>';
    if (notif.type === 'alert') icon = '<i class="bi bi-exclamation-circle-fill text-warning fs-4"></i>';
    
    div.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="d-flex align-items-start flex-grow-1">
                    <span class="me-3">${icon}</span>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${notif.title}</h6>
                        <small class="text-muted">
                            ${worker ? `<i class="bi bi-person me-1"></i>${worker.name}` : '<i class="bi bi-gear me-1"></i>Sistema'} • 
                            ${project ? `<i class="bi bi-building ms-2 me-1"></i>${project.name}` : ''} • 
                            <i class="bi bi-clock ms-2 me-1"></i>${formattedDate}
                        </small>
                    </div>
                </div>
                ${!notif.read ? '<span class="badge bg-danger ms-2">Nueva</span>' : ''}
            </div>
            <p class="mb-2 ms-5">${notif.message}</p>
            ${!notif.read ? `
                <button class="btn btn-sm btn-outline-primary ms-5" onclick="markAsRead(${notif.id})">
                    <i class="bi bi-check me-1"></i>Marcar como leída
                </button>
            ` : ''}
        </div>
    `;
    
    return div;
}

function markAsRead(id) {
    const notif = notifications.find(n => n.id === id);
    if (notif) {
        notif.read = true;
        saveNotifications();
        updateNotificationsList();
        updateNotificationBadge();
        showToast('success', 'Actualizado', 'Notificación marcada como leída');
    }
}

function markAllAsRead() {
    if (notifications.length === 0) {
        showToast('info', 'Información', 'No hay notificaciones para marcar');
        return;
    }
    
    notifications.forEach(n => n.read = true);
    saveNotifications();
    updateNotificationsList();
    updateNotificationBadge();
    showToast('success', 'Completado', 'Todas las notificaciones marcadas como leídas');
}

// ============== FUNCIONES DE UTILIDAD ==============
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