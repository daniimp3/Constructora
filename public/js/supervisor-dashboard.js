// ============== VARIABLES GLOBALES ==============
let projects = [];
//let workers = [];
let problems = [];
let attendance = [];
let currentProjectId = null;
let currentTaskId = null;
let currentEvidenceId = null;
let currentMaterialId = null;

console.log('🔍 Workers disponibles:', window.workers || workers);
console.log('🔍 CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));

// Token CSRF
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// ============== INICIALIZACIÓN ==============
document.addEventListener('DOMContentLoaded', () => {
    
    // Verificar token CSRF
    if (!csrfToken) {
        console.error('❌ Token CSRF no encontrado');
        showToast('error', 'Error', 'Token de seguridad no encontrado');
        return;
    }
    
    // Cargar datos iniciales
    loadAllData();
    
    // Configurar fecha de hoy en asistencias
    setTodayDate();
    
    // Event listeners para formularios                      
    // setupEventListeners
    
});

// ============== CONFIGURAR EVENT LISTENERS ==============
/*function setupEventListeners() {
    // Formulario de tareas
    const taskForm = document.getElementById('taskForm');
    if (taskForm) {
        taskForm.addEventListener('submit', saveTask, { once: false });
        
    }
    
    // Formulario de asistencia
    const attendanceForm = document.getElementById('attendanceForm');
    if (attendanceForm) {
        attendanceForm.addEventListener('submit', saveAttendance, { once: false });
        
    }
    
    // Cambio de fecha en asistencias
    const attendanceDateInput = document.getElementById('attendanceDate');
    if (attendanceDateInput) {
        attendanceDateInput.addEventListener('change', loadAttendance, { once: false });
    }
    
    // Preview de foto de evidencia
    const evidencePhotoInput = document.getElementById('evidencePhoto');
    if (evidencePhotoInput) {
        evidencePhotoInput.addEventListener('change', previewEvidencePhoto, { once: false });
    }
    
    // Formulario de evidencias
    const evidenceForm = document.getElementById('evidenceForm');
    if (evidenceForm) {
        evidenceForm.addEventListener('submit', saveEvidence, { once: false });
    }
    
    // Formulario de materiales
    const materialForm = document.getElementById('materialForm');
    if (materialForm) {
        materialForm.addEventListener('submit', saveMaterial, { once: false });
    }
    
}*/

function setTodayDate() {
    const today = new Date().toISOString().split('T')[0];
    const dateInput = document.getElementById('attendanceDate');
    if (dateInput) {
        dateInput.value = today;
    }
}

// ============== CARGAR DATOS DESDE API ==============
async function loadAllData() {
    
    try {
        await Promise.all([
            loadStats(),
            loadProjects(),
            loadNotifications(),
            loadWorkers()
        ]);
        
        updateUI();
        await checkBudgetAlerts();
        
    } catch (error) {
        console.error('❌ Error cargando datos:', error);
        showToast('error', 'Error', 'No se pudieron cargar los datos del sistema');
    }
}

async function loadStats() {
    try {
        const response = await fetch('/api/supervisor/stats', {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) {
            console.warn('⚠️ Error al cargar estadísticas');
            return;
        }
        
        const data = await response.json();
        
        document.getElementById('totalProjects').textContent = data.total_projects || 0;
        document.getElementById('totalTasks').textContent = data.total_tasks || 0;
        document.getElementById('completedTasks').textContent = data.completed_tasks || 0;
        document.getElementById('pendingNotifications').textContent = data.pending_notifications || 0;
        
    } catch (error) {
        console.error('❌ Error en loadStats:', error);
    }
}

// ============== CARGAR TRABAJADORES ==============
async function loadWorkers() {
    try {
        const response = await fetch('/api/supervisor/workers', {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) {
            workers = [];
            window.workers = [];
            return;
        }
        
        const data = await response.json();
        workers = data;
        window.workers = data;
        
    } catch (error) {
        workers = [];
        window.workers = [];
    }
}

// ============== ALERTAS DE PRESUPUESTO ==============
async function checkBudgetAlerts() {
    try {
        const response = await fetch('/api/supervisor/projects/check-budgets', {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) return;
        
        const data = await response.json();
        
        // Proyectos con presupuesto excedido
        if (data.has_exceeded && data.exceeded_projects && data.exceeded_projects.length > 0) {
            let message = '<div class="text-start"><strong>⚠️ PROYECTOS CON PRESUPUESTO EXCEDIDO:</strong><ul class="mt-2">';
            
            data.exceeded_projects.forEach(project => {
                const exceeded = project.exceeded_amount;
                message += `<li><strong>${project.name}</strong><br>
                    Presupuesto: $${project.budget.toLocaleString('es-MX')}<br>
                    Gastado: $${project.spent.toLocaleString('es-MX')}<br>
                    <span class="text-danger fw-bold">Excedido: $${exceeded.toLocaleString('es-MX')} (${project.percentage}%)</span>
                </li>`;
            });
            
            message += '</ul></div>';
            
            showCustomAlert(
                '<i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:60px"></i>',
                'ALERTA CRÍTICA - Presupuesto Excedido',
                message,
                [
                    { text: 'Entendido', class: 'btn-danger', action: closeCustomAlert }
                ]
            );
        }
        // Proyectos en advertencia (90% o más)
        else if (data.has_warnings && data.warning_projects && data.warning_projects.length > 0) {
            let message = '<div class="text-start"><strong>⚠️ ADVERTENCIA DE PRESUPUESTO:</strong><ul class="mt-2">';
            
            data.warning_projects.forEach(project => {
                message += `<li><strong>${project.name}</strong><br>
                    Presupuesto usado: <span class="text-warning fw-bold">${project.percentage}%</span><br>
                    Disponible: $${project.remaining.toLocaleString('es-MX')}
                </li>`;
            });
            
            message += '</ul><p class="mt-3 small text-muted">Estos proyectos están próximos a exceder su presupuesto.</p></div>';
            
            showCustomAlert(
                '<i class="bi bi-exclamation-circle-fill text-warning" style="font-size:60px"></i>',
                'Advertencia de Presupuesto',
                message,
                [
                    { text: 'Entendido', class: 'btn-warning', action: closeCustomAlert }
                ]
            );
        }
    } catch (error) {
        console.error('❌ Error verificando presupuestos:', error);
    }
}

// ============== NAVEGACIÓN ==============
function showSection(id) {
    
    // Ocultar todas las secciones
    document.querySelectorAll('.content-section').forEach(s => s.classList.add('d-none'));
    
    // Remover clase active de todos los links
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    
    // Mostrar sección seleccionada
    const section = document.getElementById(id);
    if (section) {
        section.classList.remove('d-none');
    }
    
    // Activar link correspondiente
    const links = document.querySelectorAll('.nav-link');
    links.forEach(link => {
        if (link.getAttribute('onclick') && link.getAttribute('onclick').includes(`'${id}'`)) {
            link.classList.add('active');
        }
    });
    
    // Cargar datos según la sección
    if (id === 'projects') loadProjects();
    if (id === 'notifications') loadNotifications();
    if (id === 'attendance') loadAttendance();
}

function showProjectTab(tabName) {
    
    // Ocultar todas las pestañas
    document.querySelectorAll('.project-tab').forEach(t => t.classList.add('d-none'));
    
    // Remover active de todos los botones
    document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
    
    // Mostrar pestaña seleccionada
    const tab = document.getElementById(`tab-${tabName}`);
    if (tab) {
        tab.classList.remove('d-none');
    }
    
    // Activar botón
    if (event && event.target) {
        event.target.classList.add('active');
    }
    
    // Cargar datos de la pestaña
    if (tabName === 'tasks') loadProjectTasks();
    if (tabName === 'evidences') loadProjectEvidences();
    if (tabName === 'materials') loadProjectMaterials();
}

// ============== UI UPDATES ==============
function updateUI() {
    updateRecentProjects();
    updateNotificationBadge();
}

async function updateRecentProjects() {
    try {
        const response = await fetch('/api/supervisor/projects/recent', {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) {
            console.warn('⚠️ Error al cargar proyectos recientes');
            return;
        }
        
        const myProjects = await response.json();
        const container = document.getElementById('recentProjects');
        
        if (!container) return;
        
        if (!myProjects || myProjects.length === 0) {
            container.innerHTML = '<p class="text-muted text-center py-4">No hay proyectos asignados</p>';
            return;
        }
        
        let html = '';
        myProjects.slice(0, 3).forEach(project => {
            const progress = project.progress || 0;
            html += `
                <div class="project-card card mb-3" onclick="viewProjectDetail(${project.id})">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0">${project.name}</h6>
                            ${getStatusBadge(project.status)}
                        </div>
                        <p class="text-muted small mb-2">Cliente: ${project.client || 'N/A'}</p>
                        <div class="d-flex align-items-center">
                            <span class="me-2">${progress}%</span>
                            <div class="progress flex-grow-1" style="height:8px">
                                <div class="progress-bar" style="width:${progress}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    } catch (error) {
        console.error('❌ Error en updateRecentProjects:', error);
    }
}

async function updateNotificationBadge() {
    try {
        const response = await fetch('/api/supervisor/problems/unread', {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) return;
        
        const data = await response.json();
        const unreadCount = data.count || 0;
        
        const badge = document.getElementById('notificationBadge');
        if (badge) {
            if (unreadCount > 0) {
                badge.textContent = unreadCount;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }
    } catch (error) {
        console.error('❌ Error actualizando badge:', error);
    }
}

// ============== PROYECTOS ==============
async function loadProjects() {
    
    try {
        const response = await fetch('/api/supervisor/my-projects', {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) {
            throw new Error('Error al cargar proyectos');
        }
        
        projects = await response.json();
        renderProjects();
    } catch (error) {
        console.error('❌ Error cargando proyectos:', error);
        showToast('error', 'Error', 'No se pudieron cargar los proyectos');
    }
}

function renderProjects() {
    const container = document.getElementById('projectsList');
    const empty = document.getElementById('emptyProjects');
    
    if (!container) return;
    
    if (!projects || projects.length === 0) {
        container.innerHTML = '';
        if (empty) empty.classList.remove('d-none');
        return;
    }
    
    if (empty) empty.classList.add('d-none');
    container.innerHTML = '';
    
    projects.forEach(project => {
        const col = document.createElement('div');
        col.className = 'col-md-6';
        const progress = project.progress || 0;
        const tasksCount = project.tasks ? project.tasks.length : 0;
        const completedTasksCount = project.tasks ? project.tasks.filter(t => t.status === 'completed').length : 0;
        
        col.innerHTML = `
            <div class="project-card card shadow-sm" onclick="viewProjectDetail(${project.id})" style="cursor: pointer;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-1">${project.name}</h5>
                            <small class="text-muted">${project.client || 'N/A'}</small>
                        </div>
                        ${getStatusBadge(project.status)}
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Avance del Proyecto</small>
                        <div class="d-flex align-items-center">
                            <span class="me-2"><strong>${progress}%</strong></span>
                            <div class="progress flex-grow-1" style="height:10px">
                                <div class="progress-bar" style="width:${progress}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span><i class="bi bi-list-task me-1"></i>${tasksCount} tareas</span>
                        <span><i class="bi bi-check-circle me-1"></i>${completedTasksCount} completadas</span>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(col);
    });
    
}

async function viewProjectDetail(id) {
    
    try {
        const response = await fetch(`/api/supervisor/projects/${id}`, {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) throw new Error('Error al cargar proyecto');
        
        const project = await response.json();
        currentProjectId = id;
        
        // Actualizar información del proyecto
        document.getElementById('projectDetailTitle').textContent = project.name;
        document.getElementById('projectDetailClient').textContent = 'Cliente: ' + (project.client || 'N/A');
        document.getElementById('projectDetailStatus').textContent = getStatusText(project.status);
        document.getElementById('projectDetailStatus').className = 'badge ' + getStatusBadgeClass(project.status);
        
        const progress = project.progress || 0;
        document.getElementById('currentProgress').textContent = progress;
        document.getElementById('newProgress').value = '';
        
        const budget = parseFloat(project.budget) || 0;
        document.getElementById('projectBudget').textContent = '$' + budget.toLocaleString('es-MX');
        
        const totalTasks = project.tasks ? project.tasks.length : 0;
        const completedTasks = project.tasks ? project.tasks.filter(t => t.status === 'completed').length : 0;
        document.getElementById('projectTotalTasks').textContent = totalTasks;
        document.getElementById('projectCompletedTasks').textContent = completedTasks;
        
        showSection('project-detail');
        showProjectTab('tasks');
        
    } catch (error) {
        console.error('❌ Error:', error);
        showToast('error', 'Error', 'No se pudo cargar el proyecto');
    }
}

async function updateProgress() {
    const newProgress = parseInt(document.getElementById('newProgress').value);
    
    if (isNaN(newProgress) || newProgress < 0 || newProgress > 100) {
        showToast('warning', 'Atención', 'El avance debe estar entre 0 y 100');
        return;
    }
    
    
    try {
        const response = await fetch(`/api/supervisor/projects/${currentProjectId}/progress`, {
            method: 'PUT',
            credentials: "include",
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ progress: newProgress })
        });
        
        if (!response.ok) throw new Error('Error al actualizar progreso');
        
        await response.json();
        
        document.getElementById('currentProgress').textContent = newProgress;
        document.getElementById('newProgress').value = '';
        
        showToast('success', 'Actualizado', 'Avance del proyecto actualizado correctamente');
        await loadAllData();
        
    } catch (error) {
        console.error('❌ Error:', error);
        showToast('error', 'Error', 'No se pudo actualizar el progreso');
    }
}

// ============== TAREAS ==============
async function loadProjectTasks() {
    try {
        const response = await fetch(`/api/supervisor/projects/${currentProjectId}/tasks`, {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) throw new Error('Error al cargar tareas');
        
        const data = await response.json();
        const tasks = data.tasks || [];
        
        const container = document.getElementById('tasksList');
        const empty = document.getElementById('emptyTasks');
        
        if (!container) return;
        
        // LIMPIAR CONTENEDOR PRIMERO
        container.innerHTML = '';
        
        if (tasks.length === 0) {
            if (empty) empty.classList.remove('d-none');
            return;
        }
        
        if (empty) empty.classList.add('d-none');
        
        tasks.forEach(task => {
            const card = document.createElement('div');
            card.className = `task-item card mb-2 ${task.status === 'completed' ? 'completed' : ''}`;
            
            card.innerHTML = `
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${task.title}</h6>
                            <small class="text-muted">${task.description || 'Sin descripción'}</small>
                            <div class="mt-2">
                                <small class="text-muted">Asignado a: <strong>${task.worker?.name || 'N/A'}</strong></small>
                            </div>
                        </div>
                        <div class="d-flex gap-2 align-items-start">
                            ${getTaskPriorityBadge(task.priority)}
                            ${getTaskStatusBadge(task.status)}
                            <div class="btn-group">
                                <button class="btn btn-sm btn-warning btn-action" onclick="event.stopPropagation(); editTask(${task.id})" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-action" onclick="event.stopPropagation(); confirmDeleteTask(${task.id})" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>Vence: ${new Date(task.deadline).toLocaleDateString('es-MX')}
                        </small>
                    </div>
                </div>
            `;
            
            container.appendChild(card);
        });
        
    } catch (error) {
        showToast('error', 'Error', 'No se pudieron cargar las tareas');
    }
}

async function openCreateTaskModal() {
    showToast('info', 'Cargando', 'Preparando formulario...');
    
    document.getElementById('modalTaskTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Nueva Tarea';
    document.getElementById('taskForm').reset();
    currentTaskId = null;
    
    try {
        // Cargar trabajadores del proyecto actual
        const response = await fetch(`/api/supervisor/projects/${currentProjectId}`, {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) throw new Error('Error al cargar proyecto');
        
        const project = await response.json();
        const projectWorkers = project.workers || [];
        
        const select = document.getElementById('taskWorker');
        if (select) {
            select.innerHTML = '<option value="">Seleccionar trabajador...</option>';
            
            // Si hay trabajadores en el proyecto
            if (projectWorkers.length > 0) {
                projectWorkers.forEach(w => {
                    select.innerHTML += `<option value="${w.id}">${w.name}</option>`;
                });
            } else if (window.workers && window.workers.length > 0) {
                // Usar trabajadores globales si no hay en el proyecto
                window.workers.forEach(w => {
                    select.innerHTML += `<option value="${w.id}">${w.name}</option>`;
                });
            }
        }
    } catch (error) {
        showToast('error', 'Error', 'No se pudieron cargar los trabajadores');
    }
    
    const modal = new bootstrap.Modal(document.getElementById('modalTask'));
    modal.show();
}

async function saveTask(e) {  
    e.preventDefault();
    e.stopPropagation();
    
    showToast('info', 'Guardando', 'Guardando tarea...'); // <-- Cambiar console.log por toast
    
    const data = {
        project_id: currentProjectId,
        title: document.getElementById('taskTitle').value,
        worker_id: parseInt(document.getElementById('taskWorker').value),
        priority: document.getElementById('taskPriority').value,
        status: document.getElementById('taskStatus').value,
        deadline: document.getElementById('taskDeadline').value,
        description: document.getElementById('taskDescription').value
    };
    
    // Validaciones
    if (!data.title) {
        showToast('warning', 'Atención', 'El título es obligatorio');
        return;
    }
    
    if (!data.worker_id) {
        showToast('warning', 'Atención', 'Debes asignar un trabajador');
        return;
    }
    
    if (!data.deadline) {
        showToast('warning', 'Atención', 'La fecha límite es obligatoria');
        return;
    }
    
    try {
        const url = currentTaskId 
            ? `/api/supervisor/tasks/${currentTaskId}`
            : '/api/supervisor/tasks';
        
        const method = currentTaskId ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
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
            throw new Error(errorData.message || 'Error al guardar tarea');
        }
        
        const result = await response.json();
        
        // Cerrar modal (CAMBIA 'modalMaterial' por 'modalTask')
        const modalElement = document.getElementById('modalTask'); // <-- AQUÍ estaba el error
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
            
            // Quitar el fondo gris
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        }
        
        showToast('success', 'Guardado', result.message || 'Tarea guardada correctamente');
        
        await loadProjectTasks();
        await loadStats();
        
        
        
    } catch (error) {
        showToast('error', 'Error', error.message || 'No se pudo guardar la tarea');
    }
    return false;
}


async function editTask(id) {
    showToast('info', 'Cargando', 'Cargando datos de la tarea...');
    
    try {
        // Cargar proyecto completo con trabajadores
        const projectResponse = await fetch(`/api/supervisor/projects/${currentProjectId}`, {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!projectResponse.ok) throw new Error('Error al cargar proyecto');
        
        const project = await projectResponse.json();
        
        if (!project || !project.tasks) {
            showToast('error', 'Error', 'No se encontró la tarea');
            return;
        }
        
        const task = project.tasks.find(t => t.id === id);
        
        if (!task) {
            showToast('error', 'Error', 'No se encontró la tarea');
            return;
        }
        
        currentTaskId = id;
        
        // Convertir fecha al formato correcto (YYYY-MM-DD)
        let deadlineDate = '';
        if (task.deadline) {
            // Si viene como "2025-11-23 00:00:00" o "2025-11-23"
            deadlineDate = task.deadline.split(' ')[0].split('T')[0]; // Toma solo la parte de la fecha
        }
        
        // Llenar formulario con TODOS los datos
        document.getElementById('modalTaskTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Tarea';
        document.getElementById('taskTitle').value = task.title || '';
        document.getElementById('taskPriority').value = task.priority || 'medium';
        document.getElementById('taskStatus').value = task.status || 'pending';
        document.getElementById('taskDeadline').value = deadlineDate;
        document.getElementById('taskDescription').value = task.description || '';
        
        // Cargar trabajadores en el select
        const select = document.getElementById('taskWorker');
        select.innerHTML = '<option value="">Seleccionar trabajador...</option>';
        
        const projectWorkers = project.workers || [];
        const workersToUse = projectWorkers.length > 0 ? projectWorkers : (window.workers || []);
        
        if (workersToUse.length > 0) {
            workersToUse.forEach(w => {
                const selected = w.id === task.worker_id ? 'selected' : '';
                select.innerHTML += `<option value="${w.id}" ${selected}>${w.name}</option>`;
            });
        } else {
            showToast('warning', 'Advertencia', 'No hay trabajadores disponibles');
        }
        
        const modal = new bootstrap.Modal(document.getElementById('modalTask'));
        modal.show();
        
    } catch (error) {
        showToast('error', 'Error', error.message || 'No se pudo cargar la tarea');
    }
}

function confirmDeleteTask(id) {
    const project = projects.find(p => p.id === currentProjectId);
    if (!project || !project.tasks) return;
    
    const task = project.tasks.find(t => t.id === id);
    if (!task) return;
    
    showCustomAlert(
        '<i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:60px"></i>',
        'Confirmar Eliminación',
        `¿Estás seguro de eliminar la tarea "${task.title}"?<br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
        [
            { text: 'Cancelar', class: 'btn-secondary', action: closeCustomAlert },
            { text: 'Eliminar', class: 'btn-danger', action: () => deleteTask(id) }
        ]
    );
}

async function deleteTask(id) {
    
    
    try {
        const response = await fetch(`/api/supervisor/tasks/${id}`, {
            method: 'DELETE',
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) throw new Error('Error al eliminar tarea');
        
        closeCustomAlert();
        showToast('success', 'Eliminado', 'Tarea eliminada correctamente');
        
        await loadProjectTasks();
        await loadAllData();
        await viewProjectDetail(currentProjectId);
        
    } catch (error) {
        console.error('❌ Error:', error);
        showToast('error', 'Error', 'No se pudo eliminar la tarea');
    }
}

// ============== EVIDENCIAS ==============
async function loadProjectEvidences() {
    
    try {
        const response = await fetch(`/api/supervisor/projects/${currentProjectId}/evidences`, {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) {
            console.warn('⚠️ No se pudieron cargar evidencias');
            renderEmptyEvidences();
            return;
        }
        
        const evidences = await response.json();
        renderEvidences(evidences);
        
    } catch (error) {
        console.error('❌ Error cargando evidencias:', error);
        renderEmptyEvidences();
    }
}

function renderEvidences(evidences) {
    const container = document.getElementById('evidencesList');
    const empty = document.getElementById('emptyEvidences');
    
    if (!container) return;
    
    // LIMPIAR CONTENEDOR PRIMERO
    container.innerHTML = '';
    
    if (!evidences || evidences.length === 0) {
        if (empty) empty.classList.remove('d-none');
        return;
    }
    
    if (empty) empty.classList.add('d-none');
    
    evidences.forEach(evidence => {
        const col = document.createElement('div');
        col.className = 'col-md-4';
        col.innerHTML = `
            <div class="card shadow-sm h-100 evidence-card">
                <img src="${evidence.photo_url || evidence.photo_path || evidence.photo}" class="card-img-top" style="height:200px;object-fit:cover;" alt="${evidence.title}">
                <div class="card-body">
                    <h6 class="card-title">${evidence.title}</h6>
                    <p class="card-text text-muted small">${evidence.description || 'Sin descripción'}</p>
                    <small class="text-muted"><i class="bi bi-calendar me-1"></i>${new Date(evidence.date || evidence.created_at).toLocaleDateString('es-MX')}</small>
                </div>
                <div class="card-footer bg-white">
                    <button class="btn btn-sm btn-danger w-100" onclick="confirmDeleteEvidence(${evidence.id})">
                        <i class="bi bi-trash me-1"></i>Eliminar
                    </button>
                </div>
            </div>
        `;
        container.appendChild(col);
    });
}

function renderEmptyEvidences() {
    const container = document.getElementById('evidencesList');
    const empty = document.getElementById('emptyEvidences');
    
    if (container) container.innerHTML = '';
    if (empty) empty.classList.remove('d-none');
}

function openCreateEvidenceModal() {
    
    document.getElementById('evidenceForm').reset();
    document.getElementById('evidencePreview').classList.add('d-none');
    currentEvidenceId = null;
    
    const modal = new bootstrap.Modal(document.getElementById('modalEvidence'));
    modal.show();
}

function previewEvidencePhoto(e) {
    const file = e.target.files[0];
    if (file) {
        // Validar tipo de archivo
        if (!file.type.startsWith('image/')) {
            showToast('warning', 'Atención', 'Por favor selecciona una imagen válida');
            e.target.value = '';
            return;
        }
        
        // Validar tamaño (máximo 5MB)
        if (file.size > 5 * 1024 * 1024) {
            showToast('warning', 'Atención', 'La imagen no debe superar los 5MB');
            e.target.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('evidencePreviewImg').src = event.target.result;
            document.getElementById('evidencePreview').classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
}

async function saveEvidence(e) {
    e.preventDefault();
    e.stopPropagation();
    
    showToast('info', 'Guardando', 'Subiendo evidencia...');
    
    const title = document.getElementById('evidenceTitle').value;
    const description = document.getElementById('evidenceDescription').value;
    const photoInput = document.getElementById('evidencePhoto');
    
    // Validaciones
    if (!title) {
        showToast('warning', 'Atención', 'El título es obligatorio');
        return;
    }
    
    if (!photoInput.files || photoInput.files.length === 0) {
        showToast('warning', 'Atención', 'Debes seleccionar una foto');
        return;
    }
    
    try {
        // Crear FormData para enviar archivo
        const formData = new FormData();
        formData.append('project_id', currentProjectId);
        formData.append('title', title);
        formData.append('description', description);
        formData.append('photo', photoInput.files[0]);
        formData.append('date', new Date().toISOString().split('T')[0]);
        
        const response = await fetch('/api/supervisor/evidences', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Error al guardar evidencia');
        }
        
        const result = await response.json();
        
        // Cerrar modal correctamente
        const modalElement = document.getElementById('modalEvidence');
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) {
            modalInstance.hide();
        }
        
        // Limpiar backdrop y efectos del modal
        setTimeout(() => {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        }, 300);
        
        showToast('success', 'Guardado', 'Evidencia guardada correctamente');
        
        // Limpiar formulario
        document.getElementById('evidenceForm').reset();
        document.getElementById('evidencePreview').classList.add('d-none');
        
        await loadProjectEvidences();
        
    } catch (error) {
        showToast('error', 'Error', error.message || 'No se pudo guardar la evidencia');
    }
    return false;
}


function confirmDeleteEvidence(id) {
    showCustomAlert(
        '<i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:60px"></i>',
        'Confirmar Eliminación',
        '¿Estás seguro de eliminar esta evidencia?<br><small class="text-muted">Esta acción no se puede deshacer.</small>',
        [
            { text: 'Cancelar', class: 'btn-secondary', action: closeCustomAlert },
            { text: 'Eliminar', class: 'btn-danger', action: () => deleteEvidence(id) }
        ]
    );
}


async function deleteEvidence(id) {
    
    try {
        const response = await fetch(`/api/supervisor/evidences/${id}`, {
            method: 'DELETE',
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) throw new Error('Error al eliminar evidencia');
        
        closeCustomAlert();
        showToast('success', 'Eliminado', 'Evidencia eliminada correctamente');
        
        await loadProjectEvidences();
        
    } catch (error) {
        console.error('❌ Error:', error);
        showToast('error', 'Error', 'No se pudo eliminar la evidencia');
    }
}

// ============== MATERIALES ==============
async function loadProjectMaterials() {
    
    try {
        const response = await fetch(`/api/supervisor/projects/${currentProjectId}/materials`, {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) {
            console.warn('⚠️ No se pudieron cargar materiales');
            renderEmptyMaterials();
            return;
        }
        
        const materials = await response.json();
        renderMaterials(materials);
        
    } catch (error) {
        console.error('❌ Error cargando materiales:', error);
        renderEmptyMaterials();
    }
}

function renderMaterials(materials) {
    const container = document.getElementById('materialsList');
    const empty = document.getElementById('emptyMaterials');
    
    if (!container) return;
    
    // LIMPIAR CONTENEDOR PRIMERO
    container.innerHTML = '';
    
    if (!materials || materials.length === 0) {
        if (empty) empty.classList.remove('d-none');
        return;
    }
    
    if (empty) empty.classList.add('d-none');
    
    materials.forEach(material => {
        const col = document.createElement('div');
        col.className = 'col-md-6';
        const quantity = parseFloat(material.quantity) || 0;
        const cost = parseFloat(material.cost) || 0;
        const totalCost = (quantity * cost).toFixed(2);
        
        col.innerHTML = `
            <div class="card material-item shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0">${material.name}</h6>
                        <button class="btn btn-sm btn-danger btn-action" onclick="confirmDeleteMaterial(${material.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <small class="text-muted">Cantidad:</small><br>
                            <strong>${quantity} ${material.unit || 'unidades'}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Costo Total:</small><br>
                            <strong class="text-primary">$${parseFloat(totalCost).toLocaleString('es-MX')}</strong>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(col);
    });
}

function renderEmptyMaterials() {
    const container = document.getElementById('materialsList');
    const empty = document.getElementById('emptyMaterials');
    
    if (container) container.innerHTML = '';
    if (empty) empty.classList.remove('d-none');
}

function openCreateMaterialModal() {
    
    document.getElementById('materialForm').reset();
    currentMaterialId = null;
    
    const modal = new bootstrap.Modal(document.getElementById('modalMaterial'));
    modal.show();
}

async function saveMaterial(e) {
     e.preventDefault();
    e.stopPropagation();
    
    showToast('info', 'Guardando', 'Registrando material...');
    
    const data = {
        project_id: currentProjectId,
        name: document.getElementById('materialName').value,
        quantity: parseFloat(document.getElementById('materialQuantity').value),
        unit: document.getElementById('materialUnit').value,
        cost: parseFloat(document.getElementById('materialCost').value)
    };
    
    // Validaciones
    if (!data.name) {
        showToast('warning', 'Atención', 'El nombre es obligatorio');
        return;
    }
    
    if (!data.quantity || data.quantity <= 0) {
        showToast('warning', 'Atención', 'La cantidad debe ser mayor a 0');
        return;
    }
    
    if (!data.cost || data.cost < 0) {
        showToast('warning', 'Atención', 'El costo debe ser mayor o igual a 0');
        return;
    }
    
    try {
        const response = await fetch('/api/supervisor/materials', {
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
            throw new Error(errorData.message || 'Error al guardar material');
        }
        
        const result = await response.json();
        
        // Cerrar modal
        const modalElement = document.getElementById('modalMaterial');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
            
            // Quitar el fondo gris
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        }
        
        showToast('success', 'Guardado', 'Material guardado correctamente');
        
        await loadProjectMaterials();
        await checkBudgetAlerts();
        
    } catch (error) {
        showToast('error', 'Error', error.message || 'No se pudo guardar el material');
    }
    return false;
}

function confirmDeleteMaterial(id) {
    showCustomAlert(
        '<i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:60px"></i>',
        'Confirmar Eliminación',
        '¿Estás seguro de eliminar este material?<br><small class="text-muted">Esta acción no se puede deshacer.</small>',
        [
            { text: 'Cancelar', class: 'btn-secondary', action: closeCustomAlert },
            { text: 'Eliminar', class: 'btn-danger', action: () => deleteMaterial(id) }
        ]
    );
}

async function deleteMaterial(id) {
    
    try {
        const response = await fetch(`/api/supervisor/materials/${id}`, {
            method: 'DELETE',
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) throw new Error('Error al eliminar material');
        
        closeCustomAlert();
        showToast('success', 'Eliminado', 'Material eliminado correctamente');
        
        await loadProjectMaterials();
        await checkBudgetAlerts();
        
    } catch (error) {
        console.error('❌ Error:', error);
        showToast('error', 'Error', 'No se pudo eliminar el material');
    }
}

// ============== NOTIFICACIONES ==============
async function loadNotifications() {
    
    try {
        const response = await fetch('/api/supervisor/problems', {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) throw new Error('Error al cargar notificaciones');
        
        const myNotifications = await response.json();
        
        const container = document.getElementById('notificationsList');
        const empty = document.getElementById('emptyNotifications');
        
        if (!container) return;
        
        if (!myNotifications || myNotifications.length === 0) {
            container.innerHTML = '';
            if (empty) empty.classList.remove('d-none');
            return;
        }
        
        if (empty) empty.classList.add('d-none');
        container.innerHTML = '';
        
        myNotifications.forEach(notification => {
            const card = document.createElement('div');
            card.className = `notification-item card mb-3 ${notification.read ? 'read' : ''}`;
            card.style.cursor = 'pointer';
            card.onclick = () => viewNotificationDetail(notification.id);
            
            card.innerHTML = `
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0">${notification.title}</h6>
                        ${getTaskPriorityBadge(notification.priority)}
                    </div>
                    <p class="text-muted small mb-2">${notification.description}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-person me-1"></i>${notification.worker?.name || 'Desconocido'} - ${notification.project?.name || 'N/A'}
                        </small>
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>${new Date(notification.created_at).toLocaleDateString('es-MX')}
                        </small>
                    </div>
                </div>
            `;
            
            container.appendChild(card);
        });
        
    } catch (error) {
        console.error('❌ Error cargando notificaciones:', error);
    }
}

async function viewNotificationDetail(id) {
    
    try {
        const response = await fetch(`/api/supervisor/problems/${id}`, {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) throw new Error('Error al cargar detalle');
        
        const notification = await response.json();
        
        const content = document.getElementById('notificationContent');
        if (content) {
            content.innerHTML = `
                <div class="mb-3">
                    <label class="text-muted small">Título</label>
                    <h6>${notification.title}</h6>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Categoría</label>
                    <p>${notification.category || 'N/A'}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Prioridad</label>
                    <div>${getTaskPriorityBadge(notification.priority)}</div>
                </div>
                ${notification.location ? `
                <div class="mb-3">
                    <label class="text-muted small">Ubicación</label>
                    <p>${notification.location}</p>
                </div>
                ` : ''}
                <div class="mb-3">
                    <label class="text-muted small">Descripción</label>
                    <p>${notification.description}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Reportado por</label>
                    <p>${notification.worker?.name || 'Desconocido'}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Proyecto</label>
                    <p>${notification.project?.name || 'N/A'}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Fecha</label>
                    <p>${new Date(notification.created_at).toLocaleString('es-MX')}</p>
                </div>
            `;
        }
        
        updateNotificationBadge();
        await loadStats();
        
        const modal = new bootstrap.Modal(document.getElementById('modalNotification'));
        modal.show();
        
    } catch (error) {
        console.error('❌ Error:', error);
    }
}

async function markAllAsRead() {
    
    try {
        const response = await fetch('/api/supervisor/problems/read-all', {
            method: 'PUT',
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) throw new Error('Error al marcar como leídas');
        
        updateNotificationBadge();
        await loadStats();
        loadNotifications();
        
        showToast('success', 'Actualizado', 'Todas las notificaciones marcadas como leídas');
        
        console.log('✅ Notificaciones marcadas como leídas');
    } catch (error) {
        console.error('❌ Error:', error);
    }
}

// ============== ASISTENCIAS ==============
async function loadAttendance() {
    const dateInput = document.getElementById('attendanceDate');
    if (!dateInput || !dateInput.value) {
        return;
    }
    
    const date = dateInput.value;
    
    try {
        const response = await fetch(`/api/supervisor/attendance?date=${date}`, {
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) {
            renderEmptyAttendance();
            return;
        }
        
        const attendances = await response.json();
        renderAttendance(attendances);
        
    } catch (error) {
        showToast('error', 'Error', 'No se pudieron cargar las asistencias');
        renderEmptyAttendance();
    }
}

function renderEmptyAttendance() {
    const attendanceTable = document.getElementById('attendanceTable');
    if (attendanceTable) {
        attendanceTable.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No hay trabajadores para mostrar</td></tr>';
    }
}
function renderAttendance(attendances) {
    const attendanceTable = document.getElementById('attendanceTable');
    if (!attendanceTable) return;
    
    attendanceTable.innerHTML = '';


     showToast('info', 'Debug', `Workers: ${window.workers ? window.workers.length : 0}, Attendances: ${attendances ? attendances.length : 0}`);
    console.log('👥 Workers disponibles:', window.workers);
    console.log('📋 Asistencias:', attendances);
    
    attendanceTable.innerHTML = '';
    
    // Usar trabajadores de window.workers
    const workersToShow = window.workers || workers || [];
    
    if (workersToShow.length === 0) {
        attendanceTable.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No hay trabajadores registrados</td></tr>';
        return;
    }
    
    workersToShow.forEach(worker => {
        const attendance = attendances.find(a => a.worker_id === worker.id);
        const tr = document.createElement('tr');
        
        tr.innerHTML = `
            <td>${worker.name}</td>
            <td>${worker.project_name || 'N/A'}</td>
            <td>${attendance ? getAttendanceBadge(attendance.status) : '<span class="text-muted">Sin registro</span>'}</td>
            <td>${attendance ? (attendance.time || '-') : '-'}</td>
            <td>${attendance ? (attendance.notes || '-') : '-'}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-primary" onclick="openAttendanceModal(${worker.id})">
                    <i class="bi bi-pencil"></i>
                </button>
            </td>
        `;
        
        attendanceTable.appendChild(tr);
    });
}

function renderEmptyAttendance() {
    const attendanceTable = document.getElementById('attendanceTable');
    if (attendanceTable) {
        attendanceTable.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No hay asistencias para esta fecha</td></tr>';
    }
}

function openAttendanceModal(workerId) {
    showToast('info', 'Cargando', 'Preparando registro de asistencia...');
    
    const workersToUse = window.workers || workers || [];
    const worker = workersToUse.find(w => w.id === workerId);
    
    if (!worker) {
        showToast('error', 'Error', 'Trabajador no encontrado');
        return;
    }
    
    document.getElementById('attendanceWorkerId').value = worker.id;
    document.getElementById('attendanceWorkerName').value = worker.name;
    document.getElementById('attendanceStatus').value = 'present';
    document.getElementById('attendanceTime').value = '';
    document.getElementById('attendanceNotes').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('modalAttendance'));
    modal.show();
}

async function saveAttendance(event) {
    event.preventDefault();
    
    console.log('💾 Guardando asistencia...');
    
    const data = {
        worker_id: parseInt(document.getElementById('attendanceWorkerId').value),
        date: document.getElementById('attendanceDate').value,
        status: document.getElementById('attendanceStatus').value,
        time: document.getElementById('attendanceTime').value,
        notes: document.getElementById('attendanceNotes').value
    };
    
    // Validaciones
    if (!data.worker_id) {
        showToast('warning', 'Atención', 'Trabajador no válido');
        return;
    }
    
    if (!data.date) {
        showToast('warning', 'Atención', 'La fecha es obligatoria');
        return;
    }
    
    try {
        const response = await fetch('/api/supervisor/attendance', {
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
            throw new Error(errorData.message || 'Error al guardar asistencia');
        }
        
        const result = await response.json();
        
        // Cerrar modal
        const modalElement = document.getElementById('modalAttendance');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) modal.hide();
        
        showToast('success', 'Guardado', 'Asistencia guardada correctamente');
        
        await loadAttendance();
        
        console.log('✅ Asistencia guardada');
    } catch (error) {
        console.error('❌ Error guardando asistencia:', error);
        showToast('error', 'Error', error.message || 'No se pudo guardar la asistencia');
    }
    return false;
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

function getStatusBadge(status) {
    const badges = {
        'active': '<span class="badge badge-active">Activo</span>',
        'paused': '<span class="badge badge-paused">Pausado</span>',
        'completed': '<span class="badge badge-completed">Completado</span>'
    };
    return badges[status] || badges.active;
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

function getAttendanceBadge(status) {
    const badges = {
        'present': '<span class="badge" style="background:#dcfce7;color:#166534">Presente</span>',
        'absent': '<span class="badge" style="background:#fee2e2;color:#991b1b">Ausente</span>',
        'late': '<span class="badge" style="background:#fef3c7;color:#92400e">Retardo</span>'
    };
    return badges[status] || '<span class="text-muted">Sin registro</span>';
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
        const form = document.getElementById('logout-form');
        if (form) form.submit();
    }, 1500);
}

function showCustomAlert(icon, title, message, actions) {
    const alertElement = document.getElementById('customAlert');
    if (!alertElement) return;
    
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
    
    alertElement.classList.add('show');
}

function closeCustomAlert() {
    const alertElement = document.getElementById('customAlert');
    if (alertElement) {
        alertElement.classList.remove('show');
    }
}

function showToast(type, title, message) {
    const toast = document.getElementById('toast');
    if (!toast) return;
    
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

// ============== EXPONER FUNCIONES GLOBALMENTE ==============
window.showSection = showSection;
window.showProjectTab = showProjectTab;
window.viewProjectDetail = viewProjectDetail;
window.updateProgress = updateProgress;
window.openCreateTaskModal = openCreateTaskModal;
window.editTask = editTask;
window.confirmDeleteTask = confirmDeleteTask;
window.deleteTask = deleteTask;
window.openCreateEvidenceModal = openCreateEvidenceModal;
window.confirmDeleteEvidence = confirmDeleteEvidence;
window.openCreateMaterialModal = openCreateMaterialModal;
window.confirmDeleteMaterial = confirmDeleteMaterial;
window.viewNotificationDetail = viewNotificationDetail;
window.markAllAsRead = markAllAsRead;
window.openAttendanceModal = openAttendanceModal;
window.confirmLogout = confirmLogout;
window.logout = logout;

