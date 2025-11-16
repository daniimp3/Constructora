// ============== VARIABLES GLOBALES ==============
let projects = [];
let problems = [];
let currentProjectId = null;
let currentTaskId = null;
let currentTaskProjectId = null;

// ============== INICIALIZACIÓN ==============
document.addEventListener('DOMContentLoaded', () => {
    loadAllData();
    loadProblemProjects();
});

// ============== CARGAR DATOS ==============
function loadAllData() {
    loadProjects();
    loadTasks();
    loadProblems();
}

function loadProjects() {
    fetch('/worker/api/projects')
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

function loadTasks() {
    fetch('/worker/api/tasks')
        .then(response => response.json())
        .then(data => {
            // Las tareas ya vienen cargadas en los proyectos
            updateUI();
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function loadProblems() {
    fetch('/worker/api/problems')
        .then(response => response.json())
        .then(data => {
            problems = data;
            loadMyRecentProblems();
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function updateUI() {
    renderRecentTasks();
    renderProjectsList();
    renderAllTasks();
}

// ============== VISTA GENERAL ==============
function renderRecentTasks() {
    const container = document.getElementById('recentTasks');
    if (!container) return;
    
    const allTasks = getAllMyTasks();
    
    if (allTasks.length === 0) {
        container.innerHTML = '<p class="text-muted text-center py-4">No hay tareas asignadas</p>';
        return;
    }
    
    // Ordenar por fecha de creación
    allTasks.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    
    let html = '';
    allTasks.slice(0, 3).forEach(task => {
        const priorityBadge = getTaskPriorityBadge(task.priority);
        const statusBadge = getTaskStatusBadge(task.status);
        const projectName = task.project ? task.project.name : 'Sin proyecto';
        
        html += `
            <div class="task-item card mb-2 ${task.status === 'completed' ? 'completed' : ''}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">${task.title}</h6>
                            <small class="text-muted">${projectName}</small>
                        </div>
                        <div class="d-flex gap-2">
                            ${priorityBadge}
                            ${statusBadge}
                        </div>
                    </div>
                    <div class="mt-2 d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>Vence: ${new Date(task.deadline).toLocaleDateString('es-MX')}
                        </small>
                        <button class="btn btn-sm btn-outline-primary" onclick="changeTaskStatus(${task.project_id}, ${task.id})">
                            <i class="bi bi-pencil"></i> Cambiar Estado
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function getAllMyTasks() {
    let allTasks = [];
    
    projects.forEach(project => {
        if (project.tasks && project.tasks.length > 0) {
            const tasksWithProject = project.tasks.map(task => ({
                ...task,
                project_id: project.id,
                project: { name: project.name }
            }));
            allTasks = allTasks.concat(tasksWithProject);
        }
    });
    
    return allTasks;
}

// ============== PROYECTOS ==============
function renderProjectsList() {
    const container = document.getElementById('projectsList');
    const empty = document.getElementById('emptyProjects');
    
    if (!container) return;
    
    if (projects.length === 0) {
        container.innerHTML = '';
        empty?.classList.remove('d-none');
        return;
    }
    
    empty?.classList.add('d-none');
    container.innerHTML = '';
    
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
                            <small class="text-muted">${project.client}</small>
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
}

function viewProjectDetail(projectId) {
    const project = projects.find(p => p.id === projectId);
    if (!project) return;
    
    currentProjectId = projectId;
    
    document.getElementById('projectDetailTitle').textContent = project.name;
    document.getElementById('projectDetailClient').textContent = 'Cliente: ' + project.client;
    
    const progress = project.progress || 0;
    document.getElementById('projectDetailProgress').textContent = progress;
    document.getElementById('projectDetailProgressBar').style.width = progress + '%';
    
    const myTasks = project.tasks || [];
    const myCompletedTasks = myTasks.filter(t => t.status === 'completed');
    
    document.getElementById('projectDetailMyTasks').textContent = myTasks.length;
    document.getElementById('projectDetailCompletedTasks').textContent = myCompletedTasks.length;
    
    renderProjectTasks(myTasks, projectId);
    
    showSection('project-detail');
}

function renderProjectTasks(tasks, projectId) {
    const tasksList = document.getElementById('projectTasksList');
    const emptyTasks = document.getElementById('emptyProjectTasks');
    
    if (!tasksList) return;
    
    if (tasks.length === 0) {
        tasksList.innerHTML = '';
        emptyTasks?.classList.remove('d-none');
        return;
    }
    
    emptyTasks?.classList.add('d-none');
    tasksList.innerHTML = '';
    
    tasks.forEach(task => {
        const taskCard = document.createElement('div');
        taskCard.className = `task-item card mb-3 ${task.status === 'completed' ? 'completed' : ''}`;
        
        const priorityBadge = getTaskPriorityBadge(task.priority);
        const statusBadge = getTaskStatusBadge(task.status);
        
        taskCard.innerHTML = `
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                        <h5 class="mb-1">${task.title}</h5>
                        <p class="text-muted mb-2">${task.description}</p>
                    </div>
                    <div class="d-flex gap-2">
                        ${priorityBadge}
                        ${statusBadge}
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">
                        <i class="bi bi-calendar me-1"></i>Vence: ${new Date(task.deadline).toLocaleDateString('es-MX')}
                    </small>
                    <button class="btn btn-sm btn-primary-custom" onclick="changeTaskStatus(${projectId}, ${task.id})">
                        <i class="bi bi-pencil me-1"></i>Cambiar Estado
                    </button>
                </div>
            </div>
        `;
        
        tasksList.appendChild(taskCard);
    });
}

// ============== TAREAS ==============
function renderAllTasks() {
    const container = document.getElementById('tasksList');
    const empty = document.getElementById('emptyTasks');
    
    if (!container) return;
    
    const allTasks = getAllMyTasks();
    
    if (allTasks.length === 0) {
        container.innerHTML = '';
        empty?.classList.remove('d-none');
        return;
    }
    
    empty?.classList.add('d-none');
    filterTasks();
}

function filterTasks() {
    const search = document.getElementById('searchTask')?.value.toLowerCase() || '';
    const status = document.getElementById('filterTaskStatus')?.value || '';
    
    const allTasks = getAllMyTasks();
    
    const filtered = allTasks.filter(task => {
        const matchSearch = task.title.toLowerCase().includes(search) || 
                          task.description.toLowerCase().includes(search);
        const matchStatus = !status || task.status === status;
        return matchSearch && matchStatus;
    });
    
    renderTasksList(filtered);
}

function renderTasksList(tasks) {
    const container = document.getElementById('tasksList');
    if (!container) return;
    
    if (tasks.length === 0) {
        container.innerHTML = '<div class="alert alert-info">No se encontraron tareas con los filtros aplicados</div>';
        return;
    }
    
    container.innerHTML = '';
    
    tasks.forEach(task => {
        const card = document.createElement('div');
        card.className = `task-item card mb-3 ${task.status === 'completed' ? 'completed' : ''}`;
        
        const priorityBadge = getTaskPriorityBadge(task.priority);
        const statusBadge = getTaskStatusBadge(task.status);
        const projectName = task.project ? task.project.name : 'Sin proyecto';
        
        card.innerHTML = `
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                        <h5 class="mb-1">${task.title}</h5>
                        <p class="text-muted mb-2">${task.description}</p>
                        <small class="text-muted"><i class="bi bi-building me-1"></i>${projectName}</small>
                    </div>
                    <div class="d-flex gap-2">
                        ${priorityBadge}
                        ${statusBadge}
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">
                        <i class="bi bi-calendar me-1"></i>Vence: ${new Date(task.deadline).toLocaleDateString('es-MX')}
                    </small>
                    <button class="btn btn-sm btn-primary-custom" onclick="changeTaskStatus(${task.project_id}, ${task.id})">
                        <i class="bi bi-pencil me-1"></i>Cambiar Estado
                    </button>
                </div>
            </div>
        `;
        
        container.appendChild(card);
    });
}

function changeTaskStatus(projectId, taskId) {
    const project = projects.find(p => p.id === projectId);
    if (!project || !project.tasks) return;
    
    const task = project.tasks.find(t => t.id === taskId);
    if (!task) return;
    
    currentTaskId = taskId;
    currentTaskProjectId = projectId;
    
    document.getElementById('taskStatusTitle').textContent = task.title;
    document.getElementById('newTaskStatus').value = task.status;
    
    const modal = new bootstrap.Modal(document.getElementById('modalTaskStatus'));
    modal.show();
}

function confirmTaskStatusChange() {
    const newStatus = document.getElementById('newTaskStatus').value;
    
    fetch(`/worker/tasks/${currentTaskId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(() => {
        bootstrap.Modal.getInstance(document.getElementById('modalTaskStatus')).hide();
        showToast('success', 'Actualizado', 'Estado de la tarea actualizado correctamente');
        loadAllData();
        
        // Si estamos en el detalle del proyecto, actualizar la vista
        const currentSection = document.querySelector('.content-section:not(.d-none)')?.id;
        if (currentSection === 'project-detail' && currentTaskProjectId) {
            setTimeout(() => viewProjectDetail(currentTaskProjectId), 500);
        }
    })
    .catch(error => {
        showToast('error', 'Error', 'No se pudo actualizar el estado');
    });
}

// ============== REPORTAR PROBLEMA ==============
function loadProblemProjects() {
    const select = document.getElementById('problemProject');
    if (!select) return;
    
    if (projects.length === 0) {
        select.innerHTML = '<option value="">Sin proyectos asignados</option>';
        return;
    }
    
    let html = '<option value="">Seleccionar proyecto...</option>';
    projects.forEach(project => {
        html += `<option value="${project.id}">${project.name}</option>`;
    });
    
    select.innerHTML = html;
}

function submitProblem(e) {
    e.preventDefault();
    
    const data = {
        project_id: parseInt(document.getElementById('problemProject').value),
        category: document.getElementById('problemCategory').value,
        title: document.getElementById('problemTitle').value,
        description: document.getElementById('problemDescription').value,
        priority: document.getElementById('problemPriority').value,
        location: document.getElementById('problemLocation').value
    };
    
    fetch('/worker/problems', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(() => {
        document.getElementById('problemForm').reset();
        showToast('success', 'Enviado', 'Tu reporte ha sido enviado al supervisor');
        loadProblems();
    })
    .catch(error => {
        showToast('error', 'Error', 'No se pudo enviar el reporte');
    });
}

function loadMyRecentProblems() {
    const container = document.getElementById('myRecentProblems');
    if (!container) return;
    
    if (problems.length === 0) {
        container.innerHTML = '<p class="text-muted small text-center">No hay reportes</p>';
        return;
    }
    
    // Ordenar por fecha
    problems.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    
    let html = '';
    problems.slice(0, 3).forEach(problem => {
        const priorityColor = getPriorityColor(problem.priority);
        
        html += `
            <div class="border-start border-3 border-${priorityColor} ps-2 mb-2">
                <small class="d-block fw-semibold">${problem.title}</small>
                <small class="text-muted">${new Date(problem.created_at).toLocaleDateString('es-MX')}</small>
            </div>
        `;
    });
    
    container.innerHTML = html;
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

// ============== UTILIDADES ==============
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