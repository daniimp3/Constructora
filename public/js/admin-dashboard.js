// ============== VARIABLES GLOBALES ==============
let projects = [];
let workers = [];
let currentProjectId = null;
let currentWorkerId = null;

// ============== INICIALIZACIÓN ==============
document.addEventListener('DOMContentLoaded', () => {
    loadProjects();
    loadWorkers();
});

// ============== CARGAR DATOS ==============
function loadProjects() {
    fetch('/admin/api/projects')
        .then(response => response.json())
        .then(data => {
            projects = data;
            updateUI();
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Error', 'No se pudieron cargar los proyectos');
        });
}

function loadWorkers() {
    fetch('/admin/api/workers')
        .then(response => response.json())
        .then(data => {
            workers = data;
            updateUI();
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Error', 'No se pudo cargar el personal');
        });
}

function updateUI() {
    filterProjects();
    filterWorkers();
    renderRecentProjects();
    loadSupervisors();
    loadProjectsForWorkers();
}

// ============== PROYECTOS ==============
function renderRecentProjects() {
    const container = document.getElementById('recentProjects');
    if (!container) return;
    
    if (projects.length === 0) {
        container.innerHTML = '<p class="text-muted text-center py-4">No hay proyectos registrados</p>';
        return;
    }
    
    let html = '';
    projects.slice(0, 3).forEach(project => {
        const statusBadge = getStatusBadge(project.status);
        const supervisorName = project.supervisor ? project.supervisor.name : 'Sin asignar';
        
        html += `
            <div class="card mb-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">${project.name}</h6>
                            <small class="text-muted">${project.client}</small>
                        </div>
                        ${statusBadge}
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="bi bi-person me-1"></i>${supervisorName}
                        </small>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function filterProjects() {
    const search = document.getElementById('searchProject')?.value.toLowerCase() || '';
    const status = document.getElementById('filterProjectStatus')?.value || '';
    
    const filtered = projects.filter(project => {
        const matchSearch = project.name.toLowerCase().includes(search) || 
                          project.client.toLowerCase().includes(search);
        const matchStatus = !status || project.status === status;
        return matchSearch && matchStatus;
    });
    
    renderProjects(filtered);
}

function renderProjects(filteredProjects) {
    const container = document.getElementById('projectsList');
    const empty = document.getElementById('emptyProjects');
    
    if (!container) return;
    
    if (filteredProjects.length === 0) {
        container.innerHTML = '';
        empty?.classList.remove('d-none');
        return;
    }
    
    empty?.classList.add('d-none');
    container.innerHTML = '';
    
    filteredProjects.forEach(project => {
        const card = document.createElement('div');
        card.className = 'col-md-6';
        
        const statusBadge = getStatusBadge(project.status);
        const supervisorName = project.supervisor ? project.supervisor.name : 'Sin asignar';
        
        card.innerHTML = `
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-1">${project.name}</h5>
                            <small class="text-muted">${project.client}</small>
                        </div>
                        ${statusBadge}
                    </div>
                    <p class="text-muted small mb-3">${project.description || 'Sin descripción'}</p>
                    <div class="d-flex justify-content-between text-muted small mb-3">
                        <span><i class="bi bi-calendar me-1"></i>${new Date(project.start_date).toLocaleDateString('es-MX')}</span>
                        <span><i class="bi bi-cash-stack me-1"></i>$${parseFloat(project.budget).toLocaleString('es-MX')}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Supervisor: ${supervisorName}</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary flex-fill" onclick="editProject(${project.id})">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="confirmDeleteProject(${project.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(card);
    });
}

function openNewProject() {
    currentProjectId = null;
    document.getElementById('modalProjectTitle').textContent = 'Nuevo Proyecto';
    document.getElementById('projectForm').reset();
    document.getElementById('projectStatus').value = 'active';
    loadSupervisors();
}

function editProject(id) {
    const project = projects.find(p => p.id === id);
    if (!project) return;
    
    currentProjectId = id;
    document.getElementById('modalProjectTitle').textContent = 'Editar Proyecto';
    document.getElementById('projectName').value = project.name;
    document.getElementById('projectClient').value = project.client;
    document.getElementById('projectStartDate').value = project.start_date;
    document.getElementById('projectStatus').value = project.status;
    document.getElementById('projectDescription').value = project.description || '';
    document.getElementById('projectBudgetInitial').value = project.budget;
    document.getElementById('projectSupervisor').value = project.supervisor_id || '';
    
    loadSupervisors();
    
    const modal = new bootstrap.Modal(document.getElementById('modalProject'));
    modal.show();
}

function saveProject(e) {
    e.preventDefault();
    
    const data = {
        name: document.getElementById('projectName').value,
        client: document.getElementById('projectClient').value,
        start_date: document.getElementById('projectStartDate').value,
        status: document.getElementById('projectStatus').value,
        description: document.getElementById('projectDescription').value,
        budget: parseFloat(document.getElementById('projectBudgetInitial').value) || 0,
        supervisor_id: parseInt(document.getElementById('projectSupervisor').value) || null
    };
    
    const url = currentProjectId 
        ? `/admin/projects/${currentProjectId}`
        : '/admin/projects';
    
    const method = currentProjectId ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('modalProject')).hide();
        showToast('success', currentProjectId ? 'Actualizado' : 'Creado', 'Proyecto guardado correctamente');
        loadProjects();
        currentProjectId = null;
    })
    .catch(error => {
        showToast('error', 'Error', 'No se pudo guardar el proyecto');
    });
}

function confirmDeleteProject(id) {
    showCustomAlert(
        '<i class="bi bi-trash text-danger" style="font-size:60px"></i>',
        '¿Eliminar Proyecto?',
        'Esta acción no se puede deshacer. Se eliminarán todas las tareas, evidencias y materiales asociados.',
        [
            { text: 'Cancelar', class: 'btn-secondary', action: closeCustomAlert },
            { text: 'Eliminar', class: 'btn-danger', action: () => deleteProject(id) }
        ]
    );
}

function deleteProject(id) {
    fetch(`/admin/projects/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(() => {
        closeCustomAlert();
        showToast('success', 'Eliminado', 'Proyecto eliminado correctamente');
        loadProjects();
    })
    .catch(error => {
        showToast('error', 'Error', 'No se pudo eliminar el proyecto');
    });
}

function loadSupervisors() {
    const select = document.getElementById('projectSupervisor');
    if (!select) return;
    
    const supervisors = workers.filter(w => w.role === 'supervisor');
    
    let html = '<option value="">Sin asignar</option>';
    supervisors.forEach(supervisor => {
        html += `<option value="${supervisor.id}">${supervisor.name}</option>`;
    });
    
    select.innerHTML = html;
}

// ============== PERSONAL ==============
function filterWorkers() {
    const search = document.getElementById('searchWorker')?.value.toLowerCase() || '';
    const role = document.getElementById('filterWorkerRole')?.value || '';
    
    const filtered = workers.filter(worker => {
        const matchSearch = worker.name.toLowerCase().includes(search) || 
                          worker.email.toLowerCase().includes(search);
        const matchRole = !role || worker.role === role;
        return matchSearch && matchRole;
    });
    
    renderWorkers(filtered);
}

function renderWorkers(filteredWorkers) {
    const container = document.getElementById('workersList');
    const empty = document.getElementById('emptyWorkers');
    
    if (!container) return;
    
    if (filteredWorkers.length === 0) {
        container.innerHTML = '';
        empty?.classList.remove('d-none');
        return;
    }
    
    empty?.classList.add('d-none');
    container.innerHTML = '';
    
    filteredWorkers.forEach(worker => {
        const card = document.createElement('div');
        card.className = 'col-md-6';
        
        const roleBadge = worker.role === 'supervisor' 
            ? '<span class="badge" style="background:#dbeafe;color:#1e40af">Supervisor</span>'
            : '<span class="badge" style="background:#fef3c7;color:#92400e">Trabajador</span>';
        
        const projectName = worker.project ? worker.project.name : 'Sin asignar';
        
        card.innerHTML = `
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-1">${worker.name}</h5>
                            <small class="text-muted">${worker.email}</small>
                        </div>
                        ${roleBadge}
                    </div>
                    <div class="text-muted small mb-3">
                        <div><i class="bi bi-telephone me-2"></i>${worker.phone || 'Sin teléfono'}</div>
                        <div><i class="bi bi-building me-2"></i>${projectName}</div>
                        ${worker.specialty ? `<div><i class="bi bi-tools me-2"></i>${worker.specialty}</div>` : ''}
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary flex-fill" onclick="editWorker(${worker.id})">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="confirmDeleteWorker(${worker.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(card);
    });
}

function openNewWorker() {
    currentWorkerId = null;
    document.getElementById('modalWorkerTitle').textContent = 'Agregar Personal';
    document.getElementById('workerForm').reset();
    document.getElementById('workerRole').value = 'trabajador';
    document.getElementById('passwordField').style.display = 'none';
    loadProjectsForWorkers();
}

function editWorker(id) {
    const worker = workers.find(w => w.id === id);
    if (!worker) return;
    
    currentWorkerId = id;
    document.getElementById('modalWorkerTitle').textContent = 'Editar Personal';
    document.getElementById('workerName').value = worker.name;
    document.getElementById('workerEmail').value = worker.email;
    document.getElementById('workerPhone').value = worker.phone || '';
    document.getElementById('workerRole').value = worker.role;
    document.getElementById('workerProject').value = worker.project_id || '';
    document.getElementById('workerSpecialty').value = worker.specialty || '';
    document.getElementById('passwordField').style.display = 'block';
    document.getElementById('workerPassword').value = '';
    
    loadProjectsForWorkers();
    
    const modal = new bootstrap.Modal(document.getElementById('modalWorker'));
    modal.show();
}

function saveWorker(e) {
    e.preventDefault();
    
    const data = {
        name: document.getElementById('workerName').value,
        email: document.getElementById('workerEmail').value,
        phone: document.getElementById('workerPhone').value,
        role: document.getElementById('workerRole').value,
        project_id: parseInt(document.getElementById('workerProject').value) || null,
        specialty: document.getElementById('workerSpecialty').value
    };
    
    // Solo incluir password si es edición y se proporcionó
    if (currentWorkerId) {
        const password = document.getElementById('workerPassword').value;
        if (password) {
            data.password = password;
        }
    }
    
    const url = currentWorkerId 
        ? `/admin/workers/${currentWorkerId}`
        : '/admin/workers';
    
    const method = currentWorkerId ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        // Si es nuevo trabajador, mostrar la contraseña generada
        if (data.plain_password) {
            showCustomAlert(
                '<i class="bi bi-check-circle text-success" style="font-size:60px"></i>',
                'Trabajador Creado',
                `<div class="text-start">
                    <p><strong>Email:</strong> ${data.email}</p>
                    <p><strong>Contraseña:</strong> <code>${data.plain_password}</code></p>
                    <p class="text-danger small">⚠️ Guarda esta contraseña, solo se muestra una vez.</p>
                </div>`,
                [
                    { text: 'Entendido', class: 'btn-primary-custom', action: closeCustomAlert }
                ]
            );
        }
        
        bootstrap.Modal.getInstance(document.getElementById('modalWorker')).hide();
        showToast('success', currentWorkerId ? 'Actualizado' : 'Creado', 'Personal guardado correctamente');
        loadWorkers();
        currentWorkerId = null;
    })
    .catch(error => {
        showToast('error', 'Error', 'No se pudo guardar el trabajador');
    });
}

function confirmDeleteWorker(id) {
    showCustomAlert(
        '<i class="bi bi-trash text-danger" style="font-size:60px"></i>',
        '¿Eliminar Trabajador?',
        'Esta acción no se puede deshacer.',
        [
            { text: 'Cancelar', class: 'btn-secondary', action: closeCustomAlert },
            { text: 'Eliminar', class: 'btn-danger', action: () => deleteWorker(id) }
        ]
    );
}

function deleteWorker(id) {
    fetch(`/admin/workers/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(() => {
        closeCustomAlert();
        showToast('success', 'Eliminado', 'Trabajador eliminado correctamente');
        loadWorkers();
    })
    .catch(error => {
        showToast('error', 'Error', 'No se pudo eliminar el trabajador');
    });
}

function loadProjectsForWorkers() {
    const select = document.getElementById('workerProject');
    if (!select) return;
    
    let html = '<option value="">Sin asignar</option>';
    projects.forEach(project => {
        html += `<option value="${project.id}">${project.name}</option>`;
    });
    
    select.innerHTML = html;
}

// ============== UTILIDADES ==============
function getStatusBadge(status) {
    const badges = {
        'active': '<span class="badge badge-active">Activo</span>',
        'paused': '<span class="badge badge-paused">Pausado</span>',
        'completed': '<span class="badge badge-completed">Completado</span>'
    };
    return badges[status] || badges.active;
}