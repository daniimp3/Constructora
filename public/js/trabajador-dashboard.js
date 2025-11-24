// ============== VARIABLES GLOBALES ==============
let projects = [];
let allTasks = [];
let problems = [];
let currentProjectId = null;

// Token CSRF
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// ============== INICIALIZACIÓN ==============
document.addEventListener('DOMContentLoaded', () => {
    showToast('info', 'Sistema', 'Cargando dashboard...');
    
    if (!csrfToken) {
        showToast('error', 'Error Crítico', 'Token de seguridad no encontrado');
        return;
    }
    
    loadAllData();
    
    setTimeout(() => {
        showToast('success', 'Sistema', 'Dashboard listo');
    }, 1000);
});

// ============== CARGAR DATOS DESDE API ==============
async function loadAllData() {
    try {
        await Promise.all([
            loadStats(),
            loadProjects()
        ]);
        updateUI();
    } catch (error) {
        showToast('error', 'Error', 'No se pudieron cargar los datos');
    }
}

async function loadStats() {
    try {
        const response = await fetch('/api/worker/stats', {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) return;
        
        const data = await response.json();
        
        document.getElementById('totalProjects').textContent = data.total_projects || 0;
        document.getElementById('totalTasks').textContent = data.total_tasks || 0;
        document.getElementById('completedTasks').textContent = data.completed_tasks || 0;
        document.getElementById('pendingTasks').textContent = data.pending_tasks || 0;
    } catch (error) {
        // Silenciar error
    }
}

// ============== NAVEGACIÓN ==============
function showSection(id) {
    document.querySelectorAll('.content-section').forEach(s => s.classList.add('d-none'));
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    
    const section = document.getElementById(id);
    if (section) {
        section.classList.remove('d-none');
    }
    
    const links = document.querySelectorAll('.nav-link');
    links.forEach(link => {
        if (link.getAttribute('onclick') && link.getAttribute('onclick').includes(`'${id}'`)) {
            link.classList.add('active');
        }
    });
    
    if (id === 'projects') loadProjects();
    if (id === 'tasks') loadTasks();
    if (id === 'report') {
        loadProblemProjects();
        loadMyRecentProblems();
    }
}

// ============== UI UPDATES ==============
function updateUI() {
    updateRecentTasks();
}

async function updateRecentTasks() {
    try {
        const response = await fetch('/api/worker/my-tasks', {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) return;
        
        const myTasks = await response.json();
        allTasks = myTasks;
        
        const container = document.getElementById('recentTasks');
        
        if (!myTasks || myTasks.length === 0) {
            container.innerHTML = '<p class="text-muted text-center py-4">No hay tareas asignadas</p>';
            return;
        }
        
        myTasks.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        
        let html = '';
        myTasks.slice(0, 3).forEach(task => {
            html += `
                <div class="task-item card mb-2 ${task.status === 'completed' ? 'completed' : ''}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${task.title}</h6>
                                <small class="text-muted">${task.project?.name || 'Proyecto'}</small>
                            </div>
                            <div class="d-flex gap-2">
                                ${getTaskPriorityBadge(task.priority)}
                                ${getTaskStatusBadge(task.status)}
                            </div>
                        </div>
                        <div class="mt-2 d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>Vence: ${new Date(task.deadline).toLocaleDateString('es-MX')}
                            </small>
                            <button class="btn btn-sm btn-outline-primary" onclick="changeTaskStatus(${task.id})">
                                <i class="bi bi-pencil"></i> Cambiar Estado
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    } catch (error) {
        // Silenciar error
    }
}

// ============== PROYECTOS ==============
async function loadProjects() {
    try {
        const response = await fetch('/api/worker/my-tasks', {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) throw new Error('Error al cargar proyectos');
        
        const tasks = await response.json();
        allTasks = tasks;
        
        // Extraer proyectos únicos de las tareas
        const projectsMap = new Map();
        tasks.forEach(task => {
            if (task.project && !projectsMap.has(task.project.id)) {
                projectsMap.set(task.project.id, {
                    ...task.project,
                    tasks: []
                });
            }
            if (task.project) {
                projectsMap.get(task.project.id).tasks.push(task);
            }
        });
        
        projects = Array.from(projectsMap.values());
        
        const container = document.getElementById('projectsList');
        const empty = document.getElementById('emptyProjects');
        
        container.innerHTML = '';
        
        if (projects.length === 0) {
            if (empty) empty.classList.remove('d-none');
            return;
        }
        
        if (empty) empty.classList.add('d-none');
        
        projects.forEach(project => {
            const col = document.createElement('div');
            col.className = 'col-md-6';
            const progress = project.progress || 0;
            const myTasks = project.tasks || [];
            const myCompletedTasks = myTasks.filter(t => t.status === 'completed');
            
            col.innerHTML = `
                <div class="project-card card shadow-sm" onclick="viewProjectDetail(${project.id})" style="cursor:pointer;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">${project.name}</h5>
                                <small class="text-muted">${project.client || ''}</small>
                            </div>
                            <span class="badge badge-active">Activo</span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Avance del Proyecto</small>
                            <div class="d-flex align-items-center">
                                <span class="me-2"><strong>${progress}%</strong></span>
                                <div class="progress flex-grow-1" style="height:10px">
                                    <div class="progress-bar bg-success" style="width:${progress}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between text-muted small">
                            <span><i class="bi bi-list-task me-1"></i>${myTasks.length} mis tareas</span>
                            <span><i class="bi bi-check-circle me-1"></i>${myCompletedTasks.length} completadas</span>
                        </div>
                        <div class="text-center mt-3">
                            <small class="text-primary"><i class="bi bi-arrow-right-circle me-1"></i>Clic para ver detalles</small>
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(col);
        });
    } catch (error) {
        showToast('error', 'Error', 'No se pudieron cargar los proyectos');
    }
}

function viewProjectDetail(projectId) {
    const project = projects.find(p => p.id === projectId);
    if (!project) return;
    
    currentProjectId = projectId;
    
    document.getElementById('projectDetailTitle').textContent = project.name;
    document.getElementById('projectDetailClient').textContent = 'Cliente: ' + (project.client || 'N/A');
    
    const progress = project.progress || 0;
    document.getElementById('projectDetailProgress').textContent = progress;
    document.getElementById('projectDetailProgressBar').style.width = progress + '%';
    
    const myTasks = project.tasks || [];
    const myCompletedTasks = myTasks.filter(t => t.status === 'completed');
    
    document.getElementById('projectDetailMyTasks').textContent = myTasks.length;
    document.getElementById('projectDetailCompletedTasks').textContent = myCompletedTasks.length;
    
    const tasksList = document.getElementById('projectTasksList');
    const emptyTasks = document.getElementById('emptyProjectTasks');
    
    tasksList.innerHTML = '';
    
    if (myTasks.length === 0) {
        if (emptyTasks) emptyTasks.classList.remove('d-none');
        return;
    }
    
    if (emptyTasks) emptyTasks.classList.add('d-none');
    
    myTasks.forEach(task => {
        const taskCard = document.createElement('div');
        taskCard.className = `task-item card mb-3 ${task.status === 'completed' ? 'completed' : ''}`;
        
        taskCard.innerHTML = `
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                        <h5 class="mb-1">${task.title}</h5>
                        <p class="text-muted mb-2">${task.description || 'Sin descripción'}</p>
                    </div>
                    <div class="d-flex gap-2">
                        ${getTaskPriorityBadge(task.priority)}
                        ${getTaskStatusBadge(task.status)}
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">
                        <i class="bi bi-calendar me-1"></i>Vence: ${new Date(task.deadline).toLocaleDateString('es-MX')}
                    </small>
                    <button class="btn btn-sm btn-primary-custom" onclick="changeTaskStatus(${task.id})">
                        <i class="bi bi-pencil me-1"></i>Cambiar Estado
                    </button>
                </div>
            </div>
        `;
        
        tasksList.appendChild(taskCard);
    });
    
    showSection('project-detail');
}

// ============== TAREAS ==============
async function loadTasks() {
    try {
        const response = await fetch('/api/worker/my-tasks', {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) throw new Error('Error al cargar tareas');
        
        const myTasks = await response.json();
        allTasks = myTasks;
        
        const container = document.getElementById('tasksList');
        const empty = document.getElementById('emptyTasks');
        
        if (!myTasks || myTasks.length === 0) {
            container.innerHTML = '';
            if (empty) empty.classList.remove('d-none');
            return;
        }
        
        if (empty) empty.classList.add('d-none');
        renderTasks(myTasks);
    } catch (error) {
        showToast('error', 'Error', 'No se pudieron cargar las tareas');
    }
}

function filterTasks() {
    const search = document.getElementById('searchTask').value.toLowerCase();
    const status = document.getElementById('filterTaskStatus').value;
    
    const filtered = allTasks.filter(task => {
        const matchSearch = task.title.toLowerCase().includes(search) || 
                          (task.description && task.description.toLowerCase().includes(search));
        const matchStatus = !status || task.status === status;
        return matchSearch && matchStatus;
    });
    
    renderTasks(filtered);
}

function renderTasks(tasks) {
    const container = document.getElementById('tasksList');
    container.innerHTML = '';
    
    if (tasks.length === 0) {
        container.innerHTML = '<div class="alert alert-info">No se encontraron tareas con los filtros aplicados</div>';
        return;
    }
    
    tasks.forEach(task => {
        const card = document.createElement('div');
        card.className = `task-item card mb-3 ${task.status === 'completed' ? 'completed' : ''}`;
        
        card.innerHTML = `
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                        <h5 class="mb-1">${task.title}</h5>
                        <p class="text-muted mb-2">${task.description || 'Sin descripción'}</p>
                        <small class="text-muted"><i class="bi bi-building me-1"></i>${task.project?.name || 'Proyecto'}</small>
                    </div>
                    <div class="d-flex gap-2">
                        ${getTaskPriorityBadge(task.priority)}
                        ${getTaskStatusBadge(task.status)}
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">
                        <i class="bi bi-calendar me-1"></i>Vence: ${new Date(task.deadline).toLocaleDateString('es-MX')}
                    </small>
                    <button class="btn btn-sm btn-primary-custom" onclick="changeTaskStatus(${task.id})">
                        <i class="bi bi-pencil me-1"></i>Cambiar Estado
                    </button>
                </div>
            </div>
        `;
        
        container.appendChild(card);
    });
}

function changeTaskStatus(taskId) {
    // Buscar la tarea
    const task = allTasks.find(t => t.id === taskId);
    
    if (!task) {
        showToast('error', 'Error', 'Tarea no encontrada');
        return;
    }
    
    showCustomAlert(
        '<i class="bi bi-pencil-square text-primary" style="font-size:60px"></i>',
        'Cambiar Estado de Tarea',
        `<div class="mb-3"><strong>${task.title}</strong></div>
         <select class="form-select" id="newTaskStatus">
            <option value="pending" ${task.status === 'pending' ? 'selected' : ''}>Pendiente</option>
            <option value="in-progress" ${task.status === 'in-progress' ? 'selected' : ''}>En Progreso</option>
            <option value="completed" ${task.status === 'completed' ? 'selected' : ''}>Completada</option>
         </select>`,
        [
            { text: 'Cancelar', class: 'btn-secondary', action: closeCustomAlert },
            { text: 'Guardar', class: 'btn-primary-custom', action: () => saveTaskStatus(taskId) }
        ]
    );
}

async function saveTaskStatus(taskId) {
    const newStatus = document.getElementById('newTaskStatus').value;
    
    try {
        const response = await fetch(`/api/worker/tasks/${taskId}/status`, {
            method: 'PUT',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ status: newStatus })
        });
        
        if (!response.ok) throw new Error('Error al actualizar estado');
        
        const result = await response.json();
        
        closeCustomAlert();
        showToast('success', 'Actualizado', result.message || 'Estado actualizado correctamente');
        
        // Recargar datos
        await loadAllData();
        
        // Si estamos en proyecto, actualizar
        if (currentProjectId) {
            await loadProjects();
            viewProjectDetail(currentProjectId);
        }
        
        // Si estamos en tareas, recargar
        const currentSection = document.querySelector('.content-section:not(.d-none)')?.id;
        if (currentSection === 'tasks') {
            await loadTasks();
        }
    } catch (error) {
        closeCustomAlert();
        showToast('error', 'Error', 'No se pudo actualizar el estado');
    }
}

// ============== REPORTAR PROBLEMA ==============
async function loadProblemProjects() {
    try {
        const response = await fetch('/api/worker/my-tasks', {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) return;
        
        const tasks = await response.json();
        
        // Extraer proyectos únicos
        const projectsMap = new Map();
        tasks.forEach(task => {
            if (task.project && !projectsMap.has(task.project.id)) {
                projectsMap.set(task.project.id, task.project);
            }
        });
        
        const select = document.getElementById('problemProject');
        select.innerHTML = '<option value="">Seleccionar proyecto...</option>';
        
        projectsMap.forEach(project => {
            select.innerHTML += `<option value="${project.id}">${project.name}</option>`;
        });
    } catch (error) {
        // Silenciar error
    }
}

async function submitProblem(e) {
    e.preventDefault();
    e.stopPropagation();
    
    showToast('info', 'Enviando', 'Enviando reporte...');
    
    const data = {
        project_id: parseInt(document.getElementById('problemProject').value),
        category: document.getElementById('problemCategory').value,
        title: document.getElementById('problemTitle').value,
        description: document.getElementById('problemDescription').value,
        priority: document.getElementById('problemPriority').value,
        location: document.getElementById('problemLocation').value || null
    };
    
    if (!data.project_id) {
        showToast('warning', 'Atención', 'Debes seleccionar un proyecto');
        return;
    }
    
    try {
        const response = await fetch('/api/worker/problems', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Error al enviar reporte');
        }
        
        const result = await response.json();
        
        document.getElementById('problemForm').reset();
        showToast('success', 'Enviado', result.message || 'Reporte enviado al supervisor');
        loadMyRecentProblems();
        
        return false;
    } catch (error) {
        showToast('error', 'Error', error.message || 'No se pudo enviar el reporte');
        return false;
    }
}

async function loadMyRecentProblems() {
    try {
        const response = await fetch('/api/worker/my-problems', {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) return;
        
        const myProblems = await response.json();
        const container = document.getElementById('myRecentProblems');
        
        if (!myProblems || myProblems.length === 0) {
            container.innerHTML = '<p class="text-muted small text-center">No hay reportes</p>';
            return;
        }
        
        let html = '';
        myProblems.slice(0, 3).forEach(problem => {
            html += `
                <div class="border-start border-3 border-${getPriorityColor(problem.priority)} ps-2 mb-2">
                    <small class="d-block fw-semibold">${problem.title}</small>
                    <small class="text-muted">${new Date(problem.created_at).toLocaleDateString('es-MX')}</small>
                </div>
            `;
        });
        
        container.innerHTML = html;
    } catch (error) {
        // Silenciar error
    }
}

function getPriorityColor(priority) {
    const colors = {
        'low': 'info',
        'medium': 'warning',
        'high': 'orange',
        'urgent': 'danger'
    };
    return colors[priority] || 'secondary';
}

// ============== BADGES ==============
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

// Exponer funciones globalmente
window.showSection = showSection;
window.viewProjectDetail = viewProjectDetail;
window.changeTaskStatus = changeTaskStatus;
window.filterTasks = filterTasks;
window.submitProblem = submitProblem;
window.confirmLogout = confirmLogout;