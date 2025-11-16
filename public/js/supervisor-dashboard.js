// ============== VARIABLES GLOBALES ==============
let projects = [];
let workers = [];
let problems = [];
let attendance = [];
let currentProjectId = null;
let currentTaskId = null;

// ============== INICIALIZACIÓN ==============
document.addEventListener('DOMContentLoaded', () => {
    loadAllData();
    setTodayDate();
    
    const evidenceInput = document.getElementById('evidencePhoto');
    if (evidenceInput) {
        evidenceInput.addEventListener('change', previewEvidencePhoto);
    }
});

function setTodayDate() {
    const today = new Date().toISOString().split('T')[0];
    const dateInput = document.getElementById('attendanceFilterDate');
    if (dateInput) {
        dateInput.value = today;
        loadAttendance();
    }
}

function loadAllData() {
    loadProjects();
    loadWorkers();
    loadProblems();
}

// ============== CARGAR DATOS ==============
function loadProjects() {
    fetch('/supervisor/api/projects')
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
        })
        .catch(() => {
            workers = [];
        });
}

function loadProblems() {
    fetch('/supervisor/api/problems')
        .then(response => response.json())
        .then(data => {
            problems = data;
            updateNotificationBadge();
            renderNotifications();
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function updateUI() {
    renderRecentProjects();
    renderProjectsList();
}

// ============== VISTA GENERAL ==============
function renderRecentProjects() {
    const container = document.getElementById('recentProjects');
    if (!container) return;
    
    if (projects.length === 0) {
        container.innerHTML = '<p class="text-muted text-center py-4">No hay proyectos asignados</p>';
        return;
    }
    
    let html = '';
    projects.slice(0, 3).forEach(project => {
        const progress = project.progress || 0;
        
        html += `
            <div class="card mb-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-1">${project.name}</h6>
                            <small class="text-muted">${project.client}</small>
                        </div>
                        <span class="badge badge-active">Activo</span>
                    </div>
                    <div class="progress" style="height:8px">
                        <div class="progress-bar bg-success" style="width:${progress}%"></div>
                    </div>
                    <small class="text-muted">${progress}% completado</small>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
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
        const totalTasks = project.tasks ? project.tasks.length : 0;
        const completedTasks = project.tasks ? project.tasks.filter(t => t.status === 'completed').length : 0;
        
        col.innerHTML = `
            <div class="project-card card shadow-sm h-100" onclick="viewProjectDetail(${project.id})">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-1">${project.name}</h5>
                            <small class="text-muted">${project.client}</small>
                        </div>
                        <span class="badge badge-active">Activo</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Avance</small>
                        <div class="d-flex align-items-center">
                            <span class="me-2"><strong>${progress}%</strong></span>
                            <div class="progress flex-grow-1" style="height:10px">
                                <div class="progress-bar bg-success" style="width:${progress}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span><i class="bi bi-list-task me-1"></i>${totalTasks} tareas</span>
                        <span><i class="bi bi-check-circle me-1"></i>${completedTasks} completadas</span>
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
    document.getElementById('currentProgress').textContent = progress;
    document.getElementById('progressBar').style.width = progress + '%';
    
    renderProjectTasks(project);
    renderProjectEvidences(project);
    renderProjectMaterials(project);
    
    showSection('project-detail');
}

// ============== AVANCE DEL PROYECTO ==============
function updateProgress() {
    const newProgress = parseInt(document.getElementById('newProgress').value);
    
    if (isNaN(newProgress) || newProgress < 0 || newProgress > 100) {
        showToast('warning', 'Atención', 'El avance debe estar entre 0 y 100');
        return;
    }
    
    fetch(`/supervisor/projects/${currentProjectId}/progress`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ progress: newProgress })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('currentProgress').textContent = newProgress;
        document.getElementById('progressBar').style.width = newProgress + '%';
        document.getElementById('newProgress').value = '';
        showToast('success', 'Actualizado', 'Avance del proyecto actualizado');
        loadProjects();
    })
    .catch(error => {
        showToast('error', 'Error', 'No se pudo actualizar el avance');
    });
}

// ============== TAREAS ==============
function renderProjectTasks(project) {
    const container = document.getElementById('projectTasksList');
    if (!container) return;
    
    const tasks = project.tasks || [];
    
    if (tasks.length === 0) {
        container.innerHTML = '<p class="text-muted text-center py-4">No hay tareas asignadas</p>';
        return;
    }
    
    let html = '';
    tasks.forEach(task => {
        const priorityBadge = getTaskPriorityBadge(task.priority);
        const statusBadge = getTaskStatusBadge(task.status);
        const workerName = task.worker ? task.worker.name : 'Sin asignar';
        
        html += `
            <div class="task-item card mb-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${task.title}</h6>
                            <p class="text-muted small mb-2">${task.description}</p>
                            <small class="text-muted"><i class="bi bi-person me-1"></i>${workerName}</small>
                        </div>
                        <div class="d-flex gap-2">
                            ${priorityBadge}
                            ${statusBadge}
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>Vence: ${new Date(task.deadline).toLocaleDateString('es-MX')}
                        </small>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="editTask(${task.id})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="confirmDeleteTask(${task.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function openNewTask() {
    currentTaskId = null;
    document.getElementById('modalTaskTitle').textContent = 'Nueva Tarea';
    document.getElementById('taskForm').reset();
    document.getElementById('taskPriority').value = 'medium';
    document.getElementById('taskStatus').value = 'pending';
    loadWorkersForTask();
}

function editTask(taskId) {
    const project = projects.find(p => p.id === currentProjectId);
    if (!project) return;
    
    const task = project.tasks.find(t => t.id === taskId);
    if (!task) return;
    
    currentTaskId = taskId;
    document.getElementById('modalTaskTitle').textContent = 'Editar Tarea';
    document.getElementById('taskTitle').value = task.title;
    document.getElementById('taskWorker').value = task.worker_id;
    document.getElementById('taskPriority').value = task.priority;
    document.getElementById('taskStatus').value = task.status;
    document.getElementById('taskDeadline').value = task.deadline;
    document.getElementById('taskDescription').value = task.description;
    
    loadWorkersForTask();
    
    const modal = new bootstrap.Modal(document.getElementById('modalTask'));
    modal.show();
}

function saveTask(e) {
    e.preventDefault();
    
    const data = {
        title: document.getElementById('taskTitle').value,
        worker_id: parseInt(document.getElementById('taskWorker').value),
        priority: document.getElementById('taskPriority').value,
        status: document.getElementById('taskStatus').value,
        deadline: document.getElementById('taskDeadline').value,
        description: document.getElementById('taskDescription').value
    };
    
    const url = currentTaskId
        ? `/supervisor/tasks/${currentTaskId}`
        : `/supervisor/projects/${currentProjectId}/tasks`;
    
    const method = currentTaskId ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(() => {
        bootstrap.Modal.getInstance(document.getElementById('modalTask')).hide();
        showToast('success', currentTaskId ? 'Actualizado' : 'Creado', 'Tarea guardada correctamente');
        loadProjects();
        setTimeout(() => viewProjectDetail(currentProjectId), 500);
        currentTaskId = null;
    })
    .catch(error => {
        showToast('error', 'Error', 'No se pudo guardar la tarea');
    });
}

function confirmDeleteTask(taskId) {
    showCustomAlert(
        '<i class="bi bi-trash text-danger" style="font-size:60px"></i>',
        '¿Eliminar Tarea?',
        'Esta acción no se puede deshacer.',
        [
            { text: 'Cancelar', class: 'btn-secondary', action: closeCustomAlert },
            { text: 'Eliminar', class: 'btn-danger', action: () => deleteTask(taskId) }
        ]
    );
}

function deleteTask(taskId) {
    fetch(`/supervisor/tasks/${taskId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(() => {
        closeCustomAlert();
        showToast('success', 'Eliminado', 'Tarea eliminada correctamente');
        loadProjects();
        setTimeout(() => viewProjectDetail(currentProjectId), 500);
    })
    .catch(error => {
        showToast('error', 'Error', 'No se pudo eliminar la tarea');
    });
}

function loadWorkersForTask() {
    const select = document.getElementById('taskWorker');
    if (!select) return;
    
    const project = projects.find(p => p.id === currentProjectId);
    if (!project) return;
    
    const projectWorkers = workers.filter(w => w.project_id === currentProjectId && w.role === 'trabajador');
    
    let html = '<option value="">Seleccionar trabajador...</option>';
    projectWorkers.forEach(worker => {
        html += `<option value="${worker.id}">${worker.name}</option>`;
    });
    
    select.innerHTML = html;
}

// ============== EVIDENCIAS ==============
function renderProjectEvidences(project) {
    const container = document.getElementById('projectEvidencesList');
    if (!container) return;
    
    const evidences = project.evidences || [];
    
    if (evidences.length === 0) {
        container.innerHTML = '<p class="text-muted text-center py-4">No hay evidencias registradas</p>';
        return;
    }
    
    container.innerHTML = '';
    
    evidences.forEach(evidence => {
        const col = document.createElement('div');
        col.className = 'col-md-4';
        
        col.innerHTML = `
            <div class="evidence-card card h-100">
                <img src="/storage/${evidence.photo_path}" class="card-img-top" alt="${evidence.title}">
                <div class="card-body">
                    <h6 class="card-title">${evidence.title}</h6>
                    <p class="card-text small text-muted">${evidence.description || ''}</p>
                    <small class="text-muted">${new Date(evidence.created_at).toLocaleDateString('es-MX')}</small>
                </div>
                <div class="card-footer bg-white">
                    <button class="btn btn-sm btn-outline-danger w-100" onclick="confirmDeleteEvidence(${evidence.id})">
                        <i class="bi bi-trash me-1"></i>Eliminar
                    </button>
                </div>
            </div>
        `;
        
        container.appendChild(col);
    });
}

function previewEvidencePhoto(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('evidencePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
}

function saveEvidence(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('title', document.getElementById('evidenceTitle').value);
    formData.append('description', document.getElementById('evidenceDescription').value);
    formData.append('photo', document.getElementById('evidencePhoto').files[0]);
    
    fetch(`/supervisor/projects/${currentProjectId}/evidences`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(response => response.json())
    .then(() => {
        bootstrap.Modal.getInstance(document.getElementById('modalEvidence')).hide();
        document.getElementById('evidenceForm').reset();
        document.getElementById('evidencePreview').style.display = 'none';
        showToast('success', 'Subido', 'Evidencia subida correctamente');
        loadProjects();
        setTimeout(() => viewProjectDetail(currentProjectId), 500);
    })
    .catch(error => {
        showToast('error', 'Error', 'No se pudo subir la evidencia');
    });
}

function confirmDeleteEvidence(evidenceId) {
    showCustomAlert(
        '<i class="bi bi-trash text-danger" style="font-size:60px"></i>',
        '¿Eliminar Evidencia?',
        'Esta acción no se puede deshacer.',
        [
            { text: 'Cancelar', class: 'btn-secondary', action: closeCustomAlert },
            { text: 'Eliminar', class: 'btn-danger', action: () => deleteEvidence(evidenceId) }
        ]
    );
}

function deleteEvidence(evidenceId) {
    fetch(`/supervisor/evidences/${evidenceId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(() => {
        closeCustomAlert();
        showToast('success', 'Eliminado', 'Evidencia eliminada correctamente');
        loadProjects();
        setTimeout(() => viewProjectDetail(currentProjectId), 500);
    })
    .catch(error => {
        showToast('error', 'Error', 'No se pudo eliminar la evidencia');
    });
}

// ============== MATERIALES ==============
function renderProjectMaterials(project) {
    const container = document.getElementById('projectMaterialsList');
    if (!container) return;
    
    const materials = project.materials || [];
    
    if (materials.length === 0) {
        container.innerHTML = '<p class="text-muted text-center py-4">No hay materiales registrados</p>';
        return;
    }
    
    let html = '<div class="table-responsive"><table class="table table-hover"><thead><tr><th>Material</th><th>Cantidad</th><th>Costo</th><th>Proveedor</th><th>Acciones</th></tr></thead><tbody>';
    
    materials.forEach(material => {
        html += `
            <tr>
                <td>
                    <strong>${material.name}</strong>
                    ${material.notes ? `<br><small class="text-muted">${material.notes}</small>` : ''}
                </td>
                <td>${material.quantity} ${material.unit}</td>
                <td>$${parseFloat(material.cost).toLocaleString('es-MX')}</td>
                <td>${material.supplier || '-'}</td>
                <td>
                    <button class="btn btn-sm btn-outline-danger" onclick="confirmDeleteMaterial(${material.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

function saveMaterial(e) {
    e.preventDefault();
    
    const data = {
        name: document.getElementById('materialName').value,
        quantity: parseFloat(document.getElementById('materialQuantity').value),
        unit: document.getElementById('materialUnit').value,
        cost: parseFloat(document.getElementById('materialCost').value) || 0,
        supplier: document.getElementById('materialSupplier').value,
        notes: document.getElementById('materialNotes').value
    };
    
    fetch(`/supervisor/projects/${currentProjectId}/materials`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(() => {
        bootstrap.Modal.getInstance(document.getElementById('modalMaterial')).hide();
        document.getElementById('materialForm').reset();
        showToast('success', 'Registrado', 'Material registrado correctamente');
        loadProjects();
        setTimeout(() => viewProjectDetail(currentProjectId), 500);
    })
    .catch(error => {
        showToast('error', 'Error', 'No se pudo registrar el material');
    });
}

function confirmDeleteMaterial(materialId) {
    showCustomAlert(
        '<i class="bi bi-trash text-danger" style="font-size:60px"></i>',
        '¿Eliminar Material?',
        'Esta acción no se puede deshacer.',
        [
            { text: 'Cancelar', class: 'btn-secondary', action: closeCustomAlert },
            { text: 'Eliminar', class: 'btn-danger', action: () => deleteMaterial(materialId) }
        ]
    );
}

function deleteMaterial(materialId) {
    fetch(`/supervisor/materials/${materialId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(() => {
        closeCustomAlert();
        showToast('success', 'Eliminado', 'Material eliminado correctamente');
        loadProjects();
        setTimeout(() => viewProjectDetail(currentProjectId), 500);
    })
    .catch(error => {
        showToast('error', 'Error', 'No se pudo eliminar el material');
    });
}

// ============== NOTIFICACIONES ==============
function updateNotificationBadge() {
    const unreadCount = problems.filter(p => !p.read).length;
    const badge = document.getElementById('notificationBadge');
    
    if (badge) {
        if (unreadCount > 0) {
            badge.textContent = unreadCount;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }
}

function renderNotifications() {
    const container = document.getElementById('notificationsList');
    const empty = document.getElementById('emptyNotifications');
    
    if (!container) return;
    
    if (problems.length === 0) {
        container.innerHTML = '';
        empty?.classList.remove('d-none');
        return;
    }
    
    empty?.classList.add('d-none');
    container.innerHTML = '';
    
    problems.forEach(problem => {
        const card = document.createElement('div');
        card.className = 'card mb-2 shadow-sm';
        if (!problem.read) {
            card.classList.add('border-primary');
        }
        
        const priorityBadge = getTaskPriorityBadge(problem.priority);
        const workerName = problem.worker ? problem.worker.name : 'Desconocido';
        const projectName = problem.project ? problem.project.name : 'Sin proyecto';
        
        card.innerHTML = `
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">
                            ${!problem.read ? '<i class="bi bi-circle-fill text-primary me-2" style="font-size:8px"></i>' : ''}
                            ${problem.title}
                        </h6>
                        <small class="text-muted">
                            <i class="bi bi-person me-1"></i>${workerName} - 
                            <i class="bi bi-building ms-2 me-1"></i>${projectName}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge" style="background:#e0e7ff;color:#3730a3">${problem.category}</span>
                        ${priorityBadge}
                    </div>
                </div>
                <p class="text-muted small mb-2">${problem.description}</p>
                ${problem.location ? `<small class="text-muted"><i class="bi bi-geo-alt me-1"></i>${problem.location}</small>` : ''}
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">${new Date(problem.created_at).toLocaleString('es-MX')}</small>
                    <button class="btn btn-sm btn-outline-primary" onclick="viewNotificationDetail(${problem.id})">
                        Ver detalle
                    </button>
                </div>
            </div>
        `;
        
        container.appendChild(card);
    });
}

function viewNotificationDetail(problemId) {
    const problem = problems.find(p => p.id === problemId);
    if (!problem) return;
    
    // Marcar como leída
    if (!problem.read) {
        fetch(`/supervisor/problems/${problemId}/read`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(() => {
            loadProblems();
        });
    }
    
    const priorityBadge = getTaskPriorityBadge(problem.priority);
    const workerName = problem.worker ? problem.worker.name : 'Desconocido';
    const projectName = problem.project ? problem.project.name : 'Sin proyecto';
    
    document.getElementById('notificationModalTitle').textContent = problem.title;
    document.getElementById('notificationModalBody').innerHTML = `
        <div class="mb-3">
            <div class="d-flex gap-2 mb-2">
                <span class="badge" style="background:#e0e7ff;color:#3730a3">${problem.category}</span>
                ${priorityBadge}
            </div>
        </div>
        <div class="mb-3">
            <strong>Reportado por:</strong> ${workerName}<br>
            <strong>Proyecto:</strong> ${projectName}<br>
            ${problem.location ? `<strong>Ubicación:</strong> ${problem.location}<br>` : ''}
            <strong>Fecha:</strong> ${new Date(problem.created_at).toLocaleString('es-MX')}
        </div>
        <div class="mb-3">
            <strong>Descripción:</strong>
            <p class="mt-2">${problem.description}</p>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('modalNotification'));
    modal.show();
}

function markAllAsRead() {
    fetch('/supervisor/problems/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(() => {
        showToast('success', 'Actualizado', 'Todas las notificaciones marcadas como leídas');
        loadProblems();
    })
    .catch(error => {
        showToast('error', 'Error', 'No se pudieron marcar las notificaciones');
    });
}

// ============== ASISTENCIAS ==============
function prepareAttendanceModal() {
    const select = document.getElementById('attendanceWorkerId');
    if (!select) return;
    
    // Obtener trabajadores de los proyectos del supervisor
    let projectWorkers = [];
    projects.forEach(project => {
        const projectWorkersFiltered = workers.filter(w => w.project_id === project.id && w.role === 'trabajador');
        projectWorkers = projectWorkers.concat(projectWorkersFiltered);
    });
    
    let html = '<option value="">Seleccionar...</option>';
    projectWorkers.forEach(worker => {
        html += `<option value="${worker.id}">${worker.name}</option>`;
    });
    
    select.innerHTML = html;
    
    // Establecer fecha de hoy
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('attendanceDate').value = today;
}

function loadAttendance() {
    const date = document.getElementById('attendanceFilterDate')?.value;
    if (!date) return;
    
    fetch(`/supervisor/api/attendances?date=${date}`)
        .then(response => response.json())
        .then(data => {
            attendance = data;
            renderAttendanceList();
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function renderAttendanceList() {
    const container = document.getElementById('attendanceList');
    if (!container) return;
    
    if (attendance.length === 0) {
        container.innerHTML = '<p class="text-muted text-center py-4">No hay registros de asistencia para esta fecha</p>';
        return;
    }
    
    let html = '<div class="table-responsive"><table class="table table-hover"><thead><tr><th>Trabajador</th><th>Estado</th><th>Hora</th><th>Notas</th></tr></thead><tbody>';
    
    attendance.forEach(att => {
        const statusBadge = getAttendanceStatusBadge(att.status);
        const workerName = att.worker ? att.worker.name : 'Desconocido';
        const time = att.time || '-';
        
        html += `
            <tr>
                <td>${workerName}</td>
                <td>${statusBadge}</td>
                <td>${time}</td>
                <td>${att.notes || '-'}</td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

function saveAttendance(e) {
    e.preventDefault();
    
    const data = {
        worker_id: parseInt(document.getElementById('attendanceWorkerId').value),
        date: document.getElementById('attendanceDate').value,
        status: document.getElementById('attendanceStatus').value,
        time: document.getElementById('attendanceTime').value,
        notes: document.getElementById('attendanceNotes').value
    };
    
    fetch('/supervisor/attendances', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(() => {
        bootstrap.Modal.getInstance(document.getElementById('modalAttendance')).hide();
        document.getElementById('attendanceForm').reset();
        showToast('success', 'Registrado', 'Asistencia registrada correctamente');
        loadAttendance();
    })
    .catch(error => {
        showToast('error', 'Error', 'No se pudo registrar la asistencia');
    });
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

function getAttendanceStatusBadge(status) {
    const badges = {
        'present': '<span class="badge badge-completed">Presente</span>',
        'late': '<span class="badge badge-medium">Tarde</span>',
        'absent': '<span class="badge badge-urgent">Ausente</span>'
    };
    return badges[status] || badges.present;
}