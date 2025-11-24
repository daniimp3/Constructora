// ============== VARIABLES GLOBALES ==============
let projects = [];
let workers = [];
let currentProjectId = null;
let currentWorkerId = null;

// Token CSRF
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// ============== INICIALIZACIÓN ==============
document.addEventListener('DOMContentLoaded', () => {
    loadAllData();
});

// ============== CARGAR DATOS DESDE API ==============
async function loadAllData() {
    try {
        await Promise.all([
            loadProjects(),
            loadWorkers(),
            loadStats()
        ]);
        updateUI();
    } catch (error) {
        console.error('Error cargando datos:', error);
        showToast('error', 'Error', 'No se pudieron cargar los datos');
    }
}

async function loadProjects() {
    try {
        // Usar datos iniciales de PHP
        if (window.initialProjects) {
            projects = window.initialProjects;
            window.initialProjects = null;
            return;
        }
        
        // Fallback a API (para cuando se recarga dinámicamente)
        const response = await fetch('/api/admin/projects', {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) throw new Error('Error al cargar proyectos');
        projects = await response.json();
    } catch (error) {
        console.error('Error:', error);
        projects = [];
    }
}

async function loadWorkers() {
    try {
        // Usar datos iniciales de PHP
        if (window.initialWorkers) {
            workers = window.initialWorkers;
            window.initialWorkers = null;
            return;
        }
        
        // Fallback a API
        const response = await fetch('/api/admin/workers', {
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) throw new Error('Error al cargar trabajadores');
        workers = await response.json();
    } catch (error) {
        console.error('Error:', error);
        workers = [];
    }
}

async function loadStats() {
    try {
        // Usar datos iniciales de PHP
        if (window.initialStats) {
            const data = window.initialStats;
            window.initialStats = null;
            
            document.getElementById('totalProjects').textContent = data.total_projects || 0;
            document.getElementById('activeProjects').textContent = data.active_projects || 0;
            document.getElementById('totalBudget').textContent = '$' + (data.total_budget || 0).toLocaleString('es-MX', {minimumFractionDigits: 2});
            document.getElementById('totalWorkers').textContent = data.total_workers || 0;
            return;
        }
        
        // Fallback a API
        const response = await fetch('/api/admin/stats', {
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) throw new Error('Error al cargar estadísticas');
        
        const data = await response.json();
        
        document.getElementById('totalProjects').textContent = data.total_projects || 0;
        document.getElementById('activeProjects').textContent = data.active_projects || 0;
        document.getElementById('totalBudget').textContent = '$' + (data.total_budget || 0).toLocaleString('es-MX', {minimumFractionDigits: 2});
        document.getElementById('totalWorkers').textContent = data.total_workers || 0;
    } catch (error) {
        console.error('Error:', error);
    }
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
    
    if (id === 'overview') updateOverview();
    if (id === 'projects') updateProjectsList();
    if (id === 'workers') updateWorkersList();
    if (id === 'reports') loadReportData();
}

// ============== UI UPDATES ==============
function updateUI() {
    updateStats();
    updateOverview();
    updateProjectsList();
    updateWorkersList();
    updateProjectOptionsInWorkerModal();
    updateSupervisorOptions();
}

function updateStats() {
    const totalProjects = projects.length;
    const activeProjects = projects.filter(p => p.status === 'active').length;
    const totalBudget = projects.reduce((sum, p) => sum + (parseFloat(p.budget) || 0), 0);
    const totalWorkers = workers.length;
    
    document.getElementById('totalProjects').textContent = totalProjects;
    document.getElementById('activeProjects').textContent = activeProjects;
    document.getElementById('totalBudget').textContent = '$' + totalBudget.toLocaleString('es-MX', {minimumFractionDigits: 2});
    document.getElementById('totalWorkers').textContent = totalWorkers;
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
        
        recentProjects.forEach(p => {
            const row = document.createElement('tr');
            row.style.cursor = 'pointer';
            row.onclick = () => viewProjectDetail(p.id);
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
                <td>$${(parseFloat(p.budget) || 0).toLocaleString('es-MX')}</td>
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

// ============== PROYECTOS ==============
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
                <td onclick="viewProjectDetail(${p.id})"><strong>${p.name}</strong></td>
                <td onclick="viewProjectDetail(${p.id})">${p.client}</td>
                <td onclick="viewProjectDetail(${p.id})">${getStatusBadge(p.status)}</td>
                <td onclick="viewProjectDetail(${p.id})">
                    <div class="d-flex align-items-center">
                        <span class="me-2">${progress}%</span>
                        <div class="progress flex-grow-1" style="height:8px; min-width:80px;">
                            <div class="progress-bar" style="width:${progress}%"></div>
                        </div>
                    </div>
                </td>
                <td onclick="viewProjectDetail(${p.id})">$${(parseFloat(p.budget) || 0).toLocaleString('es-MX')}</td>
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
    const supervisors = workers.filter(w => w.role === 'supervisor');
    supervisors.forEach(s => {
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

async function saveProject(e) {
    e.preventDefault();
    
    const formData = {
        name: document.getElementById('projectName').value,
        client: document.getElementById('projectClient').value,
        start_date: document.getElementById('projectStartDate').value,
        status: document.getElementById('projectStatus').value,
        description: document.getElementById('projectDescription').value,
        budget: parseFloat(document.getElementById('projectBudgetInitial').value) || 0,
        supervisor_id: parseInt(document.getElementById('projectSupervisor').value) || null
    };
    
    try {
        let url, method;
        
        if (currentProjectId) {
            url = `/admin/projects/${currentProjectId}`;
            method = 'POST'; // Laravel usa POST con _method
            formData._method = 'PUT'; 
        } else {
            url = '/admin/projects';
            method = 'POST';
        }
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest' 
            },
            body: JSON.stringify(formData)
        });
        
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ message: 'Error desconocido' }));
            throw new Error(errorData.message || 'Error al guardar proyecto');
        }
        
        const result = await response.json();
        
        bootstrap.Modal.getInstance(document.getElementById('modalProject')).hide();
        showToast('success', currentProjectId ? 'Actualizado' : 'Creado', result.message || 'Proyecto guardado');
        
        setTimeout(() => window.location.reload(), 1000);
    } catch (error) {
        console.error('Error completo:', error);
        showToast('error', 'Error', error.message || 'No se pudo guardar el proyecto');
    }
}

async function editProject(id) {
    const project = projects.find(p => p.id === id);
    if (!project) return;
    
    currentProjectId = id;
    document.getElementById('modalProjectTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Proyecto';
    document.getElementById('projectName').value = project.name;
    document.getElementById('projectClient').value = project.client;
    document.getElementById('projectStartDate').value = project.start_date;
    document.getElementById('projectStatus').value = project.status;
    document.getElementById('projectDescription').value = project.description || '';
    document.getElementById('projectBudgetInitial').value = project.budget || 0;
    document.getElementById('projectSupervisor').value = project.supervisor_id || '';
    
    updateSupervisorOptions();
    new bootstrap.Modal(document.getElementById('modalProject')).show();
}

function confirmDelete(id) {
    const project = projects.find(p => p.id === id);
    if (!project) return;
    
    showCustomAlert(
        '<i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:60px"></i>',
        'Confirmar Eliminación',
        `¿Eliminar el proyecto "${project.name}"? Esta acción no se puede deshacer.`,
        [
            { text: 'Cancelar', class: 'btn-secondary', action: closeCustomAlert },
            { text: 'Eliminar', class: 'btn-danger', action: () => deleteProject(id) }
        ]
    );
}

async function deleteProject(id) {
    try {
        const response = await fetch(`/admin/projects/${id}`, {
            method: 'POST', 
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ _method: 'DELETE' }) 
        });
        
        if (!response.ok) throw new Error('Error al eliminar proyecto');
        
        const result = await response.json();
        
        closeCustomAlert();
        showToast('success', 'Eliminado', result.message || 'Proyecto eliminado');
        await loadAllData();
    } catch (error) {
        console.error('Error:', error);
        closeCustomAlert();
        showToast('error', 'Error', 'No se pudo eliminar el proyecto');
    }
}

// ============== DETALLE DE PROYECTO ==============
async function viewProjectDetail(id) {
    try {
        const response = await fetch(`/admin/projects/${id}`, {
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) throw new Error('Error al cargar proyecto');
        
        const project = await response.json();
        currentProjectId = id;
        
        // Información básica
        document.getElementById('projectDetailName').textContent = project.name;
        document.getElementById('projectDetailClient').textContent = project.client;
        document.getElementById('projectDetailStatusBadge').className = 'badge ' + getStatusBadgeClass(project.status);
        document.getElementById('projectDetailStatusBadge').textContent = getStatusText(project.status);
        
        // Avance
        const progress = project.progress || 0;
        document.getElementById('projectDetailProgress').textContent = progress;
        document.getElementById('projectDetailProgressBar').style.width = progress + '%';
        
        // Presupuesto
        const budget = parseFloat(project.budget) || 0;
        const spent = parseFloat(project.spent) || 0;
        const available = budget - spent;
        const percentUsed = budget > 0 ? ((spent / budget) * 100).toFixed(1) : 0;
        
        document.getElementById('projectDetailBudget').textContent = '$' + budget.toLocaleString('es-MX', {minimumFractionDigits: 2});
        document.getElementById('projectDetailSpent').textContent = '$' + spent.toLocaleString('es-MX', {minimumFractionDigits: 2});
        document.getElementById('projectDetailAvailable').textContent = '$' + available.toLocaleString('es-MX', {minimumFractionDigits: 2});
        
        // Análisis financiero
        document.getElementById('projectDetailPercentUsed').textContent = percentUsed + '%';
        document.getElementById('projectDetailPercentUsedBar').style.width = Math.min(percentUsed, 100) + '%';
        document.getElementById('projectDetailPercentUsedBar').textContent = percentUsed + '%';
        
        // Color de la barra según el porcentaje
        const progressBar = document.getElementById('projectDetailPercentUsedBar');
        progressBar.className = 'progress-bar';
        if (percentUsed >= 100) {
            progressBar.classList.add('bg-danger');
        } else if (percentUsed >= 90) {
            progressBar.classList.add('bg-warning');
        } else {
            progressBar.classList.add('bg-success');
        }
        
        // Desviación
        const deviation = spent - budget;
        const deviationEl = document.getElementById('projectDetailDeviation');
        deviationEl.textContent = (deviation > 0 ? '-' : '+') + '$' + Math.abs(deviation).toLocaleString('es-MX', {minimumFractionDigits: 2});
        deviationEl.className = deviation > 0 ? 'text-danger' : 'text-success';
        
        const deviationText = document.getElementById('projectDetailDeviationText');
        if (deviation > 0) {
            deviationText.textContent = 'Excedido del presupuesto';
            deviationText.className = 'text-danger d-block mt-1';
        } else {
            deviationText.textContent = 'Dentro del presupuesto';
            deviationText.className = 'text-success d-block mt-1';
        }
        
        // Información del proyecto
        document.getElementById('projectDetailStartDate').textContent = new Date(project.start_date).toLocaleDateString('es-MX');
        
        const supervisor = project.supervisor;
        document.getElementById('projectDetailSupervisor').textContent = supervisor ? supervisor.name : 'Sin asignar';
        
        document.getElementById('projectDetailStatusText').textContent = getStatusText(project.status);
        document.getElementById('projectDetailDescription').textContent = project.description || 'Sin descripción';
        
        // Tareas
        const tasks = project.tasks || [];
        const completedTasks = tasks.filter(t => t.status === 'completed').length;
        const pendingTasks = tasks.filter(t => t.status !== 'completed').length;
        
        document.getElementById('projectDetailTotalTasks').textContent = tasks.length;
        document.getElementById('projectDetailCompletedTasks').textContent = completedTasks;
        document.getElementById('projectDetailPendingTasks').textContent = pendingTasks;
        
        // Gastos
        const expenses = project.expenses || [];
        document.getElementById('projectDetailTotalExpenses').textContent = expenses.length;
        document.getElementById('projectDetailExpensesAmount').textContent = '$' + spent.toLocaleString('es-MX', {minimumFractionDigits: 2});
        
        // Personal
        const projectWorkers = project.workers || [];
        document.getElementById('projectDetailWorkers').textContent = projectWorkers.length;
        
        const workersList = document.getElementById('projectDetailWorkersList');
        if (projectWorkers.length === 0) {
            workersList.innerHTML = '<small class="text-muted">Sin personal asignado</small>';
        } else {
            workersList.innerHTML = projectWorkers.slice(0, 3).map(w => 
                `<div class="d-flex align-items-center mb-1">
                    <i class="bi bi-person-circle me-2"></i>
                    <small>${w.name}</small>
                </div>`
            ).join('');
            
            if (projectWorkers.length > 3) {
                workersList.innerHTML += `<small class="text-muted">+${projectWorkers.length - 3} más...</small>`;
            }
        }
        
        // Cambiar a la vista de detalle
        document.querySelectorAll('.content-section').forEach(s => s.classList.add('d-none'));
        document.getElementById('project-detail').classList.remove('d-none');
    } catch (error) {
        console.error('Error:', error);
        showToast('error', 'Error', 'No se pudo cargar el proyecto');
    }
}

function editProjectFromDetail() {
    if (currentProjectId) {
        editProject(currentProjectId);
    }
}

function exportProjectDetailPDF() {
    const id = currentProjectId;
    window.location.href = `/projects/${id}/export/pdf`;
}

function exportProjectDetailExcel() {
    const id = currentProjectId;
    window.location.href = `/projects/${id}/export/excel`;
}

// ============== PERSONAL ==============
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
            const project = projects.find(p => p.id === w.project_id);
            const projectName = project ? project.name : '<span class="text-muted">Sin asignar</span>';
            
            row.innerHTML = `
                <td><strong>${w.name}</strong></td>
                <td>${w.email}</td>
                <td>${getRoleBadge(w.role)}</td>
                <td>${projectName}</td>
                <td>${w.phone || 'N/A'}</td>
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

function openCreateWorkerModal() {
    document.getElementById('modalWorkerTitle').innerHTML = '<i class="bi bi-person-plus me-2"></i>Agregar Personal';
    document.getElementById('workerForm').reset();
    currentWorkerId = null;
    updateProjectOptionsInWorkerModal();
}

async function saveWorker(e) {
    e.preventDefault();
    
    const projectId = document.getElementById('workerProject').value;
    
    const formData = {
        name: document.getElementById('workerName').value,
        email: document.getElementById('workerEmail').value.trim(),
        phone: document.getElementById('workerPhone').value,
        role: document.getElementById('workerRole').value,
        project_id: projectId === '' ? null : parseInt(projectId),
        specialty: document.getElementById('workerSpecialty').value
    };
    
    try {
        let url, method;
        
        if (currentWorkerId) {
            url = `/admin/workers/${currentWorkerId}`;
            method = 'POST';
            formData._method = 'PUT'; 
        } else {
            url = '/admin/workers';
            method = 'POST';
        }
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest' 
            },
            body: JSON.stringify(formData)
        });
        
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ message: 'Error desconocido' }));
            throw new Error(errorData.message || 'Error al guardar trabajador');
        }
        
        const result = await response.json();
        
        bootstrap.Modal.getInstance(document.getElementById('modalWorker')).hide();
        
        //  Si es un usuario nuevo, mostrar detalles con contraseña
        if (result.password) {
            showToast('success', 'Creado', 'Usuario creado correctamente');
            
            // Agregar el nuevo trabajador al array
            workers.push(result.worker);
            
            // Mostrar modal de detalles con la contraseña
            setTimeout(() => {
                viewWorkerDetailsWithPassword(result.worker.id, result.password);
            }, 500);
        } else {
            showToast('success', 'Actualizado', result.message || 'Trabajador actualizado');
            setTimeout(() => window.location.reload(), 1000);
        }
    } catch (error) {
        console.error('Error completo:', error);
        showToast('error', 'Error', error.message || 'No se pudo guardar el trabajador');
    }
}

async function viewWorkerDetails(id) {
    const worker = workers.find(w => w.id === id);
    if (!worker) return;
    
    currentWorkerId = id;
    const project = projects.find(p => p.id === worker.project_id);
    
    const content = document.getElementById('workerDetailsContent');
    
    // Cargar contenido básico primero
    content.innerHTML = `
        <div class="mb-3">
            <label class="text-muted small">Nombre</label>
            <h6>${worker.name}</h6>
        </div>
        <div class="mb-3">
            <label class="text-muted small">Email</label>
            <h6>${worker.email}</h6>
        </div>
        <div class="mb-3" id="passwordSection">
            <label class="text-muted small">🔑 Contraseña Temporal</label>
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label class="text-muted small">Rol</label>
            <div>${getRoleBadge(worker.role)}</div>
        </div>
        <div class="mb-3">
            <label class="text-muted small">Teléfono</label>
            <h6>${worker.phone || 'N/A'}</h6>
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
    
    // Mostrar el modal
    new bootstrap.Modal(document.getElementById('modalWorkerDetails')).show();
    
    // Cargar contraseña temporal
    try {
        const response = await fetch(`/api/admin/workers/${id}/temporary-password`, {
    method: 'GET',
    credentials: 'same-origin', // ← Cambiar de 'include' a 'same-origin'
    headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest' // ← Agregar esta línea
    }
});
        
        const passwordSection = document.getElementById('passwordSection');
        
        if (response.ok) {
            const data = await response.json();
            
            passwordSection.innerHTML = `
                <label class="text-muted small">🔑 Contraseña Temporal</label>
                <div class="alert alert-success">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <h4 class="mb-0 font-monospace">${data.password}</h4>
                            <small class="text-muted d-block mt-1">
                                <i class="bi bi-clock me-1"></i>Creada: ${data.created_at}
                            </small>
                            ${data.viewed_at ? `
                                <small class="text-muted d-block">
                                    <i class="bi bi-eye me-1"></i>Vista: ${data.viewed_at}
                                </small>
                            ` : ''}
                        </div>
                        <button class="btn btn-sm btn-primary" onclick="copyPasswordToClipboard('${data.password}')">
                            <i class="bi bi-clipboard"></i> Copiar
                        </button>
                    </div>
                </div>
            `;
        } else {
            passwordSection.innerHTML = `
                <label class="text-muted small">🔑 Contraseña</label>
                <div class="alert alert-info py-2">
                    <small>
                        <i class="bi bi-info-circle me-1"></i>
                        No hay contraseña temporal disponible. Por seguridad, las contraseñas están encriptadas.
                    </small>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error al cargar contraseña:', error);
        const passwordSection = document.getElementById('passwordSection');
        passwordSection.innerHTML = `
            <label class="text-muted small">🔑 Contraseña</label>
            <div class="alert alert-warning py-2">
                <small><i class="bi bi-exclamation-triangle me-1"></i>No se pudo cargar la información de contraseña.</small>
            </div>
        `;
    }
}


function copyPasswordToClipboard(password) {
    const tempInput = document.createElement('input');
    tempInput.value = password;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);
    
    showToast('success', 'Copiado', 'Contraseña copiada al portapapeles');
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
    document.getElementById('workerPhone').value = worker.phone || '';
    document.getElementById('workerProject').value = worker.project_id || '';
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
        `¿Eliminar a "${worker.name}"? Esta acción no se puede deshacer.`,
        [
            { text: 'Cancelar', class: 'btn-secondary', action: closeCustomAlert },
            { text: 'Eliminar', class: 'btn-danger', action: () => deleteWorker(id) }
        ]
    );
}

async function deleteWorker(id) {
    try {
        const response = await fetch(`/admin/workers/${id}`, {
            method: 'POST', 
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ _method: 'DELETE' }) 
        });
        
        if (!response.ok) throw new Error('Error al eliminar trabajador');
        
        const result = await response.json();
        
        closeCustomAlert();
        showToast('success', 'Eliminado', result.message || 'Trabajador eliminado');
        await loadAllData();
    } catch (error) {
        console.error('Error:', error);
        closeCustomAlert();
        showToast('error', 'Error', 'No se pudo eliminar el trabajador');
    }
}

//  REPORTES 
async function loadReportData() {
    // No recargar proyectos, usar los que ya tenemos
    if (projects.length === 0) {
        // Si no hay proyectos cargados, recargar todo
        await loadAllData();
    }
}

function showBudgetReport() {
    document.getElementById('budgetReportSection').classList.remove('d-none');
    document.getElementById('projectComparisonSection').classList.add('d-none');
    loadBudgetReport();
}

function showProjectComparison() {
    document.getElementById('projectComparisonSection').classList.remove('d-none');
    document.getElementById('budgetReportSection').classList.add('d-none');
    loadProjectComparison();
}

function hideProjectComparison() {
    document.getElementById('projectComparisonSection').classList.add('d-none');
}

function loadBudgetReport() {
    const tbody = document.getElementById('budgetReportTable');
    tbody.innerHTML = '';
    
    let totalBudget = 0;
    let totalSpent = 0;
    
    if (projects.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No hay proyectos para analizar</td></tr>';
        return;
    }
    
    projects.forEach(project => {
        const budget = parseFloat(project.budget) || 0;
        const spent = parseFloat(project.spent) || 0;
        const percentUsed = budget > 0 ? ((spent / budget) * 100).toFixed(1) : 0;
        const deviation = spent - budget;
        
        totalBudget += budget;
        totalSpent += spent;
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><strong>${project.name}</strong></td>
            <td>$${budget.toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>
            <td>$${spent.toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>
            <td>
                <div class="d-flex align-items-center">
                    <span class="me-2">${percentUsed}%</span>
                    <div class="progress flex-grow-1" style="height:8px; min-width:60px;">
                        <div class="progress-bar ${percentUsed > 90 ? 'bg-danger' : percentUsed > 75 ? 'bg-warning' : 'bg-success'}" 
                             style="width:${Math.min(percentUsed, 100)}%"></div>
                    </div>
                </div>
            </td>
            <td class="${deviation > 0 ? 'text-danger' : 'text-success'}">
                ${deviation > 0 ? '-' : ''}$${Math.abs(deviation).toLocaleString('es-MX', {minimumFractionDigits: 2})}
            </td>
            <td>${getBudgetStatusBadge(percentUsed)}</td>
        `;
        tbody.appendChild(row);
    });
    
    // Actualizar resumen
    const totalPercentUsed = totalBudget > 0 ? ((totalSpent / totalBudget) * 100).toFixed(1) : 0;
    document.getElementById('totalBudgetSummary').textContent = '$' + totalBudget.toLocaleString('es-MX', {minimumFractionDigits: 2});
    document.getElementById('totalSpentSummary').textContent = '$' + totalSpent.toLocaleString('es-MX', {minimumFractionDigits: 2});
    document.getElementById('percentUsedSummary').textContent = totalPercentUsed + '%';
}

function getBudgetStatusBadge(percentUsed) {
    if (percentUsed >= 100) {
        return '<span class="badge" style="background:#fee2e2;color:#991b1b">Excedido</span>';
    } else if (percentUsed >= 90) {
        return '<span class="badge" style="background:#fef3c7;color:#92400e">Crítico</span>';
    } else if (percentUsed >= 75) {
        return '<span class="badge" style="background:#fed7aa;color:#9a3412">Alerta</span>';
    } else if (percentUsed >= 50) {
        return '<span class="badge" style="background:#dbeafe;color:#1e40af">Bueno</span>';
    } else {
        return '<span class="badge" style="background:#dcfce7;color:#166534">Normal</span>';
    }
}

function loadProjectComparison() {
    const tbody = document.getElementById('projectComparisonTable');
    tbody.innerHTML = '';
    
    if (projects.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No hay proyectos para comparar</td></tr>';
        return;
    }
    
    projects.forEach(project => {
        const budget = parseFloat(project.budget) || 0;
        const progress = project.progress || 0;
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><strong>${project.name}</strong></td>
            <td>${project.client}</td>
            <td>${getStatusBadge(project.status)}</td>
            <td>
                <div class="d-flex align-items-center">
                    <span class="me-2">${progress}%</span>
                    <div class="progress flex-grow-1" style="height:8px; min-width:80px;">
                        <div class="progress-bar" style="width:${progress}%"></div>
                    </div>
                </div>
            </td>
            <td>${new Date(project.start_date).toLocaleDateString('es-MX')}</td>
            <td>$${budget.toLocaleString('es-MX')}</td>
        `;
        tbody.appendChild(row);
    });
}

function exportBudgetReportPDF(budgetId) {
    window.open('/budgets/' + budgetId + '/export/pdf', '_blank');
}

function exportBudgetReportExcel(budgetId) {
    window.open('/budgets/' + budgetId + '/export/excel', '_blank');
}

// ============== BADGES ==============
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

// ============== UTILIDADES ==============
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
    
    const icons = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };
    document.getElementById('toastIcon').textContent = icons[type] || icons.info;
    document.getElementById('toastTitle').textContent = title;
    document.getElementById('toastMessage').textContent = message;
    
    toastElement.show();
}