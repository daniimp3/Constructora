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
                    AD
                </div>
                <div class="ms-3">
                    <div class="text-white fw-semibold">Administrador</div>
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
                    <h2 class="mb-1">Reportes por Proyecto</h2>
                    <p class="text-muted mb-0">Selecciona un proyecto para generar su reporte</p>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Seleccionar Proyecto</label>
                            <select class="form-select" id="reportProjectSelect" onchange="loadProjectReport()">
                                <option value="">Selecciona un proyecto...</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div class="btn-group w-100" id="exportButtons" style="display:none;">
                                <button class="btn btn-outline-danger" onclick="exportProjectToPDF()">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                                </button>
                                <button class="btn btn-outline-success" onclick="exportProjectToExcel()">
                                    <i class="bi bi-file-earmark-excel me-1"></i>Excel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="projectReportSection" class="d-none">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0" id="reportProjectTitle"></h5>
                    </div>
                    <div class="card-body" id="projectReportContent"></div>
                </div>
            </div>
        </div>
    </div>

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
                            <label class="form-label">Contraseña *</label>
                            <input type="text" class="form-control" id="workerPassword" required placeholder="Contraseña">
                            <small class="text-muted" id="passwordHint">Se generará automáticamente</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono *</label>
                            <input type="tel" class="form-control" id="workerPhone" required>
                        </div>
                        <div class="col-md-6">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
let projects = [];
let workers = [];
let currentProjectId = null;
let currentWorkerId = null;

document.addEventListener('DOMContentLoaded', () => {
    loadProjects();
    loadWorkers();
    updateUI();
});

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

function generatePassword() {
    return 'Pass' + Math.random().toString(36).slice(-8);
}

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
    
    if (id === 'overview') updateOverview();
    if (id === 'projects') updateProjectsList();
    if (id === 'workers') updateWorkersList();
    if (id === 'reports') loadReportProjects();
}

function updateUI() {
    updateStats();
    updateOverview();
    updateProjectsList();
    updateWorkersList();
    updateProjectOptionsInWorkerModal();
    updateSupervisorOptions();
}

function updateStats() {
    document.getElementById('totalProjects').textContent = projects.length;
    document.getElementById('activeProjects').textContent = projects.filter(p => p.status === 'active').length;
    document.getElementById('totalBudget').textContent = '$' + projects.reduce((sum, p) => sum + (parseFloat(p.budget) || 0), 0).toLocaleString('es-MX', {minimumFractionDigits: 2});
    document.getElementById('totalWorkers').textContent = workers.length;
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
        [...projects].reverse().slice(0, 5).forEach(p => {
            const row = document.createElement('tr');
            row.style.cursor = 'pointer';
            const progress = p.progress || 0;
            row.innerHTML = `
                <td><strong>${p.name}</strong></td>
                <td>${p.client}</td>
                <td>${getStatusBadge(p.status)}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <span class="me-2">${progress}%</span>
                        <div class="progress flex-grow-1" style="height:8px; min-width:80px;">
                            <div class="progress-bar" style="width:${progress}%"></div>
                        </div>
                    </div>
                </td>
                <td>$${(p.budget || 0).toLocaleString('es-MX')}</td>
            `;
            tbody.appendChild(row);
        });
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

function filterProjects() {
    const search = document.getElementById('searchProject').value.toLowerCase();
    const status = document.getElementById('filterStatus').value;
    
    const filtered = projects.filter(p => {
        const matchSearch = p.name.toLowerCase().includes(search) || p.client.toLowerCase().includes(search);
        const matchStatus = !status || p.status === status;
        return matchSearch && matchStatus;
    });
    
    const tbody = document.getElementById('projectsTable');
    tbody.innerHTML = '';
    
    if (filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No se encontraron proyectos</td></tr>';
    } else {
        filtered.forEach(p => {
            const row = document.createElement('tr');
            row.style.cursor = 'pointer';
            const progress = p.progress || 0;
            
            row.innerHTML = `
                <td><strong>${p.name}</strong></td>
                <td>${p.client}</td>
                <td>${getStatusBadge(p.status)}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <span class="me-2">${progress}%</span>
                        <div class="progress flex-grow-1" style="height:8px; min-width:80px;">
                            <div class="progress-bar" style="width:${progress}%"></div>
                        </div>
                    </div>
                </td>
                <td>$${(p.budget || 0).toLocaleString('es-MX')}</td>
                <td onclick="event.stopPropagation()" class="text-center">
                    <button class="btn btn-sm btn-warning btn-action me-1" onclick="editProject(${p.id})" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-action" onclick="confirmDelete(${p.id})" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }
}

function filterWorkers() {
    const search = document.getElementById('searchWorker').value.toLowerCase();
    const role = document.getElementById('filterRole').value;
    
    const filtered = workers.filter(w => {
        const matchSearch = w.name.toLowerCase().includes(search) || w.email.toLowerCase().includes(search);
        const matchRole = !role || w.role === role;
        return matchSearch && matchRole;
    });
    
    const tbody = document.getElementById('workersTable');
    tbody.innerHTML = '';
    
    if (filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No se encontraron trabajadores</td></tr>';
    } else {
        filtered.forEach(w => {
            const row = document.createElement('tr');
            const projectName = w.projectId ? (projects.find(p => p.id === w.projectId)?.name || 'N/A') : '<span class="text-muted">Sin asignar</span>';
            
            row.innerHTML = `
                <td><strong>${w.name}</strong></td>
                <td>${w.email}</td>
                <td>${getRoleBadge(w.role)}</td>
                <td>${projectName}</td>
                <td>${w.phone}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-info btn-action me-1" onclick="viewWorkerDetails(${w.id})" title="Ver detalles">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning btn-action me-1" onclick="editWorker(${w.id})" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-action" onclick="confirmDeleteWorker(${w.id})" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }
}

function updateProjectOptionsInWorkerModal() {
    const select = document.getElementById('workerProject');
    if (!select) return;
    select.innerHTML = '<option value="">Sin asignar</option>';
    projects.forEach(p => {
        select.innerHTML += `<option value="${p.id}">${p.name}</option>`;
    });
}

function updateSupervisorOptions() {
    const select = document.getElementById('projectSupervisor');
    if (!select) return;
    select.innerHTML = '<option value="">Sin asignar</option>';
    workers.filter(w => w.role === 'supervisor').forEach(s => {
        select.innerHTML += `<option value="${s.id}">${s.name}</option>`;
    });
}

function openCreateProjectModal() {
    document.getElementById('modalProjectTitle').innerHTML = '<i class="bi bi-building me-2"></i>Nuevo Proyecto';
    document.getElementById('projectForm').reset();
    document.getElementById('projectBudgetInitial').value = '0';
    currentProjectId = null;
    updateSupervisorOptions();
}

function saveProject(e) {
    e.preventDefault();
    
    const data = {
        name: document.getElementById('projectName').value,
        client: document.getElementById('projectClient').value,
        startDate: document.getElementById('projectStartDate').value,
        status: document.getElementById('projectStatus').value,
        description: document.getElementById('projectDescription').value,
        budget: parseFloat(document.getElementById('projectBudgetInitial').value) || 0,
        supervisorId: parseInt(document.getElementById('projectSupervisor').value) || null,
        spent: 0,
        expenses: [],
        progress: 0,
        tasks: [],
        evidences: [],
        materials: []
    };
    
    if (currentProjectId) {
        const index = projects.findIndex(p => p.id === currentProjectId);
        const oldProject = projects[index];
        projects[index] = { ...oldProject, ...data, spent: oldProject.spent || 0, expenses: oldProject.expenses || [], progress: oldProject.progress || 0, tasks: oldProject.tasks || [], evidences: oldProject.evidences || [], materials: oldProject.materials || [] };
        showToast('success', 'Actualizado', 'Proyecto actualizado');
    } else {
        data.id = Date.now();
        projects.push(data);
        showToast('success', 'Creado', 'Proyecto creado');
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
    document.getElementById('projectDescription').value = project.description || '';
    document.getElementById('projectBudgetInitial').value = project.budget || 0;
    document.getElementById('projectSupervisor').value = project.supervisorId || '';
    
    updateSupervisorOptions();
    new bootstrap.Modal(document.getElementById('modalProject')).show();
}

function confirmDelete(id) {
    const project = projects.find(p => p.id === id);
    if (!project) return;
    
    showCustomAlert(
        '<i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:60px"></i>',
        'Confirmar Eliminación',
        `¿Eliminar el proyecto "${project.name}"?`,
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
    showToast('success', 'Eliminado', 'Proyecto eliminado');
    updateUI();
}

function openCreateWorkerModal() {
    document.getElementById('modalWorkerTitle').innerHTML = '<i class="bi bi-person-plus me-2"></i>Agregar Personal';
    document.getElementById('workerForm').reset();
    document.getElementById('workerEmail').value = '';
    document.getElementById('workerPassword').value = generatePassword(); // Genera contraseña automática
    document.getElementById('workerPassword').readOnly = true; // Solo lectura al crear
    document.getElementById('passwordHint').textContent = 'Contraseña generada automáticamente';
    currentWorkerId = null;
    updateProjectOptionsInWorkerModal();
}

function saveWorker(e) {
    e.preventDefault();
    
    const email = document.getElementById('workerEmail').value.trim();
    const password = document.getElementById('workerPassword').value.trim();
    
    // Validar que el email no esté duplicado (excepto si es edición del mismo usuario)
    const emailExists = workers.some(w => w.email === email && w.id !== currentWorkerId);
    if (emailExists) {
        showToast('warning', 'Email duplicado', 'Este email ya está registrado');
        return;
    }
    
    // Validar contraseña
    if (!password || password.length < 4) {
        showToast('warning', 'Contraseña inválida', 'La contraseña debe tener al menos 4 caracteres');
        return;
    }
    
    const data = {
        name: document.getElementById('workerName').value,
        email: email,
        phone: document.getElementById('workerPhone').value,
        role: document.getElementById('workerRole').value,
        password: password,
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

function viewWorkerDetails(id) {
    const worker = workers.find(w => w.id === id);
    if (!worker) return;
    
    currentWorkerId = id;
    const project = projects.find(p => p.id === worker.projectId);
    
    const content = document.getElementById('workerDetailsContent');
    content.innerHTML = `
        <div class="mb-3">
            <label class="text-muted small">Nombre</label>
            <h6>${worker.name}</h6>
        </div>
        <div class="mb-3">
            <label class="text-muted small">Email</label>
            <h6>${worker.email}</h6>
        </div>
        <div class="mb-3">
            <label class="text-muted small">Contraseña</label>
            <h6><span class="password-cell">${worker.password}</span></h6>
        </div>
        <div class="mb-3">
            <label class="text-muted small">Rol</label>
            <div>${getRoleBadge(worker.role)}</div>
        </div>
        <div class="mb-3">
            <label class="text-muted small">Teléfono</label>
            <h6>${worker.phone}</h6>
        </div>
        <div class="mb-3">
            <label class="text-muted small">Proyecto Asignado</label>
            <h6>${project ? project.name : 'Sin asignar'}</h6>
        </div>
        ${worker.specialty ? `
        <div class="mb-3">
            <label class="text-muted small">Especialidad</label>
            <h6>${worker.specialty}</h6>
        </div>
        ` : ''}
    `;
    
    new bootstrap.Modal(document.getElementById('modalWorkerDetails')).show();
}

function editWorkerFromDetails() {
    bootstrap.Modal.getInstance(document.getElementById('modalWorkerDetails')).hide();
    setTimeout(() => editWorker(currentWorkerId), 300);
}

function editWorker(id) {
    const worker = workers.find(w => w.id === id);
    if (!worker) return;
    
    currentWorkerId = id;
    document.getElementById('modalWorkerTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Personal';
    document.getElementById('workerName').value = worker.name;
    document.getElementById('workerRole').value = worker.role;
    document.getElementById('workerEmail').value = worker.email;
    document.getElementById('workerPhone').value = worker.phone;
    document.getElementById('workerPassword').value = worker.password;
    document.getElementById('workerPassword').readOnly = false; // Permitir edición
    document.getElementById('passwordHint').textContent = 'Puedes modificar la contraseña';
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
        `¿Eliminar a "${worker.name}"?`,
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
    showToast('success', 'Eliminado', 'Trabajador eliminado');
    updateUI();
}

function loadReportProjects() {
    const select = document.getElementById('reportProjectSelect');
    select.innerHTML = '<option value="">Selecciona un proyecto...</option>';
    projects.forEach(p => {
        select.innerHTML += `<option value="${p.id}">${p.name} - ${p.client}</option>`;
    });
}

function loadProjectReport() {
    const projectId = parseInt(document.getElementById('reportProjectSelect').value);
    if (!projectId) {
        document.getElementById('projectReportSection').classList.add('d-none');
        document.getElementById('exportButtons').style.display = 'none';
        return;
    }
    
    const project = projects.find(p => p.id === projectId);
    if (!project) return;
    
    currentProjectId = projectId;
    document.getElementById('exportButtons').style.display = 'flex';
    document.getElementById('projectReportSection').classList.remove('d-none');
    document.getElementById('reportProjectTitle').textContent = `Reporte: ${project.name}`;
    
    const budget = parseFloat(project.budget) || 0;
    const spent = parseFloat(project.spent) || 0;
    const progress = project.progress || 0;
    const supervisor = workers.find(w => w.id === project.supervisorId);
    const projectWorkers = workers.filter(w => w.projectId === projectId);
    
    let html = `
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <small class="text-muted">Estado</small>
                        <h5 class="mt-2">${getStatusBadge(project.status)}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <small class="text-muted">Avance</small>
                        <h5 class="mt-2">${progress}%</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <small class="text-muted">Presupuesto</small>
                        <h5 class="mt-2">$${budget.toLocaleString('es-MX')}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <small class="text-muted">Gastado</small>
                        <h5 class="mt-2 text-danger">$${spent.toLocaleString('es-MX')}</h5>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="table-responsive mb-4">
            <h6 class="mb-3">Información General</h6>
            <table class="table table-bordered">
                <tr><th width="200">Cliente</th><td>${project.client}</td></tr>
                <tr><th>Fecha de Inicio</th><td>${new Date(project.startDate).toLocaleDateString('es-MX')}</td></tr>
                <tr><th>Supervisor</th><td>${supervisor ? supervisor.name : 'Sin asignar'}</td></tr>
                <tr><th>Personal Asignado</th><td>${projectWorkers.length} trabajadores</td></tr>
            </table>
        </div>
    `;
    
    document.getElementById('projectReportContent').innerHTML = html;
}

function exportProjectToPDF() {
    const project = projects.find(p => p.id === currentProjectId);
    if (!project) {
        showToast('warning', 'Error', 'Selecciona un proyecto');
        return;
    }
    
    showToast('info', 'Generando', 'Creando PDF...');
    
    try {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        doc.setFontSize(18);
        doc.text(`Reporte: ${project.name}`, 14, 20);
        doc.setFontSize(11);
        doc.text(`Cliente: ${project.client}`, 14, 30);
        doc.text(`Fecha: ${new Date().toLocaleDateString('es-MX')}`, 14, 37);
        
        const budget = parseFloat(project.budget) || 0;
        const spent = parseFloat(project.spent) || 0;
        const progress = project.progress || 0;
        
        let yPos = 50;
        doc.setFontSize(14);
        doc.text('Información General', 14, yPos);
        yPos += 10;
        
        doc.setFontSize(10);
        doc.text(`Estado: ${project.status}`, 14, yPos);
        doc.text(`Avance: ${progress}%`, 14, yPos + 7);
        doc.text(`Presupuesto: $${budget.toLocaleString('es-MX')}`, 14, yPos + 14);
        doc.text(`Gastado: $${spent.toLocaleString('es-MX')}`, 14, yPos + 21);
        
        doc.save(`reporte-${project.name.toLowerCase().replace(/\s+/g, '-')}.pdf`);
        showToast('success', 'Completado', 'PDF descargado');
    } catch (error) {
        showToast('error', 'Error', 'No se pudo generar el PDF');
    }
}

function exportProjectToExcel() {
    const project = projects.find(p => p.id === currentProjectId);
    if (!project) {
        showToast('warning', 'Error', 'Selecciona un proyecto');
        return;
    }
    
    showToast('info', 'Generando', 'Creando Excel...');
    
    try {
        const data = [
            { Campo: 'Proyecto', Valor: project.name },
            { Campo: 'Cliente', Valor: project.client },
            { Campo: 'Fecha Inicio', Valor: new Date(project.startDate).toLocaleDateString('es-MX') },
            { Campo: 'Estado', Valor: project.status },
            { Campo: 'Avance', Valor: `${project.progress || 0}%` },
            { Campo: 'Presupuesto', Valor: project.budget || 0 },
            { Campo: 'Gastado', Valor: project.spent || 0 }
        ];
        
        const ws = XLSX.utils.json_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Información');
        
        XLSX.writeFile(wb, `reporte-${project.name.toLowerCase().replace(/\s+/g, '-')}.xlsx`);
        showToast('success', 'Completado', 'Excel descargado');
    } catch (error) {
        showToast('error', 'Error', 'No se pudo generar Excel');
    }
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
        'supervisor': '<span class="badge badge-supervisor">Supervisor</span>'
    };
    return badges[role] || badges.trabajador;
}

function confirmLogout() {
    showCustomAlert(
        '<i class="bi bi-box-arrow-right text-warning" style="font-size:60px"></i>',
        'Cerrar Sesión',
        '¿Seguro que deseas cerrar sesión?',
        [
            { text: 'Cancelar', class: 'btn-secondary', action: closeCustomAlert },
            { text: 'Cerrar Sesión', class: 'btn-primary-custom', action: logout }
        ]
    );
}

function logout() {
    closeCustomAlert();
    showToast('success', 'Cerrando sesión', 'Hasta pronto...');
    setTimeout(() => document.getElementById('logout-form').submit(), 1500);
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
    
    const icons = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };
    document.getElementById('toastIcon').textContent = icons[type] || icons.info;
    document.getElementById('toastTitle').textContent = title;
    document.getElementById('toastMessage').textContent = message;
    
    toastElement.show();
}
    </script>
</body>
</html>