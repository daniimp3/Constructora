<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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
        
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-completed { background: #dbeafe; color: #1e40af; }
        .badge-paused { background: #fef3c7; color: #92400e; }
        .badge-trabajador { background: #e0e7ff; color: #3730a3; }
        .badge-supervisor { background: #fce7f3; color: #831843; }
        
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

        .password-cell {
            font-family: monospace;
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 4px;
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
            <p class="company-subtitle mb-0">Panel de Administración</p>
        </div>
        
        <div class="bg-dark bg-opacity-25 m-3 p-3 rounded">
            <div class="d-flex align-items-center">
                <div class="bg-gradient rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                     style="width:45px;height:45px;background:linear-gradient(135deg,#3b82f6,#8b5cf6)">
                    {{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 2)) : 'AD' }}
                </div>
                <div class="ms-3">
                    <div class="text-white fw-semibold">{{ Auth::check() ? Auth::user()->name : 'Administrador' }}</div>
                    <small class="text-white-50">Administrador</small>
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
            
            <!-- Recent Projects -->
            <div class="card shadow-sm d-none" id="recentProjectsCard">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Proyectos Recientes</h5>
                    <button class="btn btn-sm btn-outline-secondary" onclick="showSection('projects')">Ver todos</button>
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
            
            <!-- Projects List -->
            <div class="card shadow-sm d-none" id="projectsListCard">
                <div class="card-body">
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

        <!-- Detalle del Proyecto -->
        <div id="project-detail" class="content-section d-none">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <button class="btn btn-sm btn-outline-secondary mb-3" onclick="showSection('projects')">
                        <i class="bi bi-arrow-left me-1"></i>Volver a Proyectos
                    </button>
                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                        <div>
                            <h2 class="mb-1" id="projectDetailName">Proyecto</h2>
                            <p class="text-muted mb-0"><i class="bi bi-person me-1"></i>Cliente: <span id="projectDetailClient">N/A</span></p>
                        </div>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <span class="badge badge-active" id="projectDetailStatusBadge">Activo</span>
                            <div class="btn-group">
                                <button class="btn btn-warning" onclick="editProjectFromDetail()" title="Editar">
                                    <i class="bi bi-pencil me-1"></i>Editar
                                </button>
                                <button class="btn btn-outline-danger" onclick="exportProjectDetailPDF()" title="Exportar PDF">
                                    <i class="bi bi-file-pdf"></i>
                                </button>
                                <button class="btn btn-outline-success" onclick="exportProjectDetailExcel()" title="Exportar Excel">
                                    <i class="bi bi-file-excel"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Información General -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <small class="text-muted">Avance del Proyecto</small>
                            <h3 class="mt-2 mb-0"><span id="projectDetailProgress">0</span>%</h3>
                            <div class="progress mt-2" style="height:10px">
                                <div class="progress-bar" id="projectDetailProgressBar" style="width:0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <small class="text-muted">Presupuesto</small>
                            <h4 class="mt-2 mb-0 text-primary" id="projectDetailBudget">$0</h4>
                            <small class="text-muted">Asignado</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <small class="text-muted">Gastado</small>
                            <h4 class="mt-2 mb-0 text-danger" id="projectDetailSpent">$0</h4>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <small class="text-muted">Disponible</small>
                            <h4 class="mt-2 mb-0 text-success" id="projectDetailAvailable">$0</h4>
                            <small class="text-muted">Restante</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Detalles Completos -->
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información del Proyecto</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <th width="150">Fecha de Inicio:</th>
                                    <td id="projectDetailStartDate">-</td>
                                </tr>
                                <tr>
                                    <th>Supervisor:</th>
                                    <td id="projectDetailSupervisor">Sin asignar</td>
                                </tr>
                                <tr>
                                    <th>Estado:</th>
                                    <td id="projectDetailStatusText">-</td>
                                </tr>
                                <tr>
                                    <th>Descripción:</th>
                                    <td id="projectDetailDescription">Sin descripción</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Análisis Financiero</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">% Presupuesto Utilizado</small>
                                    <small class="fw-bold" id="projectDetailPercentUsed">0%</small>
                                </div>
                                <div class="progress" style="height:20px">
                                    <div class="progress-bar" id="projectDetailPercentUsedBar" style="width:0%">0%</div>
                                </div>
                            </div>
                            <div class="alert alert-light mb-0">
                                <div class="d-flex justify-content-between">
                                    <span>Desviación:</span>
                                    <strong id="projectDetailDeviation" class="text-success">$0</strong>
                                </div>
                                <small class="text-muted d-block mt-1" id="projectDetailDeviationText">Dentro del presupuesto</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tareas, Gastos, Personal -->
            <div class="row g-3 mt-3">
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="bi bi-list-task me-2"></i>Tareas</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total:</span>
                                <strong id="projectDetailTotalTasks">0</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Completadas:</span>
                                <strong class="text-success" id="projectDetailCompletedTasks">0</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Pendientes:</span>
                                <strong class="text-warning" id="projectDetailPendingTasks">0</strong>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Gastos</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Gastos:</span>
                                <strong id="projectDetailTotalExpenses">0</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Monto:</span>
                                <strong class="text-danger" id="projectDetailExpensesAmount">$0</strong>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="bi bi-people me-2"></i>Personal</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Trabajadores:</span>
                                <strong id="projectDetailWorkers">0</strong>
                            </div>
                            <div id="projectDetailWorkersList" class="mt-3">
                                <small class="text-muted">Sin personal asignado</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sección de Personal -->
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
            
            <div class="card shadow-sm d-none" id="workersListCard">
                <div class="card-body">
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
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Proyecto</th>
                                    <th>Teléfono</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="workersTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            
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
            
            <!-- Cards de Opciones de Reporte -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm h-100" style="border-left: 4px solid #3b82f6;">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-graph-up-arrow fs-1 text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">📊 Reporte de Gastos vs Presupuesto</h5>
                                    <p class="text-muted small mb-0">Analiza desviaciones en el presupuesto de cada proyecto</p>
                                </div>
                            </div>
                            <button class="btn btn-primary w-100" onclick="showBudgetReport()">
                                <i class="bi bi-eye me-2"></i>Ver Reporte
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card shadow-sm h-100" style="border-left: 4px solid #10b981;">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-bar-chart-line fs-1 text-success"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">📈 Comparación de Proyectos</h5>
                                    <p class="text-muted small mb-0">Identifica retrasos y compara avances</p>
                                </div>
                            </div>
                            <button class="btn btn-success w-100" onclick="showProjectComparison()">
                                <i class="bi bi-eye me-2"></i>Comparar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Análisis de Presupuesto por Proyecto -->
            <div id="budgetReportSection" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Análisis de Presupuesto por Proyecto</h5>
                        <div class="btn-group">
                            
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Proyecto</th>
                                        <th>Presupuesto</th>
                                        <th>Gastado</th>
                                        <th>% Utilizado</th>
                                        <th>Desviación</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="budgetReportTable"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Resumen de Presupuestos -->
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="card-body text-center py-4">
                                <h6 class="text-white-50 mb-2">Presupuesto Total</h6>
                                <h2 class="mb-0" id="totalBudgetSummary">$0</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <div class="card-body text-center py-4">
                                <h6 class="text-white-50 mb-2">Total Gastado</h6>
                                <h2 class="mb-0" id="totalSpentSummary">$0</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <div class="card-body text-center py-4">
                                <h6 class="text-white-50 mb-2">% Utilizado</h6>
                                <h2 class="mb-0" id="percentUsedSummary">0%</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Comparación de Proyectos -->
            <div id="projectComparisonSection" class="d-none">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Comparación de Proyectos</h5>
                        <button class="btn btn-sm btn-outline-secondary" onclick="hideProjectComparison()">
                            <i class="bi bi-x-circle me-1"></i>Cerrar
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
                                        <th>Fecha Inicio</th>
                                        <th>Presupuesto</th>
                                    </tr>
                                </thead>
                                <tbody id="projectComparisonTable"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- MODALES Y ALERTAS  -->

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
                                <label class="form-label">Presupuesto Inicial</label>
                                <input type="number" class="form-control" id="projectBudgetInitial" step="0.01" value="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Supervisor Asignado</label>
                                <select class="form-select" id="projectSupervisor">
                                    <option value="">Sin asignar</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea class="form-control" id="projectDescription" rows="3"></textarea>
                            </div>
                        </div>
                        <input type="hidden" id="projectId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-save me-2"></i>Guardar Proyecto
                        </button>
                    </div>
                </form>
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
                                <label class="form-label">Rol *</label>
                                <select class="form-select" id="workerRole" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="trabajador">Trabajador</option>
                                    <option value="supervisor">Supervisor</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" id="workerEmail" required placeholder="ejemplo@constructora.com">
                                <small class="text-muted">Crea un email único para el usuario</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono *</label>
                                <input type="tel" class="form-control" id="workerPhone" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Proyecto Asignado</label>
                                <select class="form-select" id="workerProject">
                                    <option value="">Sin asignar</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Especialidad</label>
                                <input type="text" class="form-control" id="workerSpecialty" placeholder="Ej: Albañilería, Electricidad...">
                            </div>
                        </div>
                        <input type="hidden" id="workerId">
                        <input type="hidden" id="workerPassword">
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

    <!-- Modal Ver Detalles del Trabajador -->
    <div class="modal fade" id="modalWorkerDetails" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-person me-2"></i>Detalles del Personal
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="workerDetailsContent"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-warning" onclick="editWorkerFromDetails()">
                        <i class="bi bi-pencil me-2"></i>Editar
                    </button>
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
    // Pasar los datos de PHP a JavaScript
    window.initialProjects = @json($projects);
    window.initialWorkers = @json($workers);
    window.initialStats = @json($stats);
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/admin-dashboard.js') }}"></script>
</body>
</html>

