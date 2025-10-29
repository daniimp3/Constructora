@extends('layouts.app')
@section('content')

<!-- Fuente -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<style>
    /* ======== ESTILOS GENERALES ======== */
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background-color: #f4f6f8;
    }

    /* ======== SIDEBAR ======== */
    .sidebar {
        width: 220px;
        height: 100vh;
        background-color: #0d273d;
        position: fixed;
        top: 0;
        left: 0;
        padding: 20px;
        color: white;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .sidebar h2 {
        text-align: center;
        width: 100%;
        margin-bottom: 25px;
        font-size: 22px;
        font-weight: 600;
    }

    .sidebar a {
        display: block;
        width: 100%;
        color: #8aa7bc;
        text-decoration: none;
        margin: 8px 0;
        padding: 10px 15px;
        border-radius: 8px;
        transition: background-color 0.3s, color 0.3s;
    }

    .sidebar a:hover,
    .sidebar a.active {
        background-color: #3e6985;
        color: white;
    }

    /* ======== CONTENIDO PRINCIPAL ======== */
    .main-content {
        margin-left: 250px;
        padding: 30px;
    }

    .main-content h1 {
        color: #0d273d;
        margin-bottom: 20px;
    }

    /* ======== TARJETAS ======== */
    .card {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
    }

    .card h3 {
        margin-top: 0;
        color: #3e6985;
        font-weight: 600;
    }

    label {
        font-weight: 500;
        color: #0d273d;
    }

    .form-control {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        margin-top: 4px;
    }

    /* ======== BOTONES ======== */
    .btn {
        background-color: #3e6985;
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn:hover {
        background-color: #2c5167;
    }

    /* ======== ALERTAS ======== */
    .alert {
        background-color: #ffeaea;
        color: #a94442;
        border-left: 5px solid #a94442;
        padding: 10px 15px;
        border-radius: 6px;
        font-weight: 500;
    }
</style>

<!-- ======== DASHBOARD SUPERVISOR ======== -->
<div class="sidebar">
    <h2>Supervisor</h2>
    <a href="#" class="active">Panel</a>
    <a href="#">Asignar Tareas</a>
    <a href="#">Subir Avances</a>
    <a href="#">Registrar Materiales</a>
    <a href="#">Asistencia</a>
    <a href="#">Alertas</a>
</div>

<div class="main-content">
    <h1>Bienvenido, Supervisor 👷‍♂️</h1>

    <div class="card">
        <h3>Asignar Tareas a Trabajadores</h3>
        <form>
            <label>Trabajador:</label>
            <input type="text" class="form-control" placeholder="Ej. Juan Pérez"><br>
            <label>Tarea:</label>
            <input type="text" class="form-control" placeholder="Ej. Revisar estructura de muro"><br>
            <button class="btn">Asignar</button>
        </form>
    </div>

    <div class="card">
        <h3>Subir Avances con Evidencia</h3>
        <form enctype="multipart/form-data">
            <label>Descripción del avance:</label>
            <input type="text" class="form-control" placeholder="Ej. Avance del 50% en cimentación"><br>
            <label>Subir foto o nota:</label>
            <input type="file" class="form-control"><br>
            <button class="btn">Subir Evidencia</button>
        </form>
    </div>

    <div class="card">
        <h3>Registrar Materiales Utilizados</h3>
        <form>
            <label>Material:</label>
            <input type="text" class="form-control" placeholder="Ej. Cemento gris"><br>
            <label>Cantidad utilizada:</label>
            <input type="number" class="form-control"><br>
            <button class="btn">Registrar</button>
        </form>
    </div>

    <div class="card">
        <h3>Control de Asistencia</h3>
        <form>
            <label>Nombre del trabajador:</label>
            <input type="text" class="form-control" placeholder="Ej. Carlos López"><br>
            <label>Estado:</label>
            <select class="form-control">
                <option>Presente</option>
                <option>Ausente</option>
                <option>Permiso</option>
            </select><br>
            <button class="btn">Guardar Asistencia</button>
        </form>
    </div>

    <div class="card">
        <h3>Alertas de Presupuesto Excedido</h3>
        <div class="alert">⚠ Proyecto Torre B ha superado el presupuesto en un 8%. Revisar gastos.</div>
    </div>
</div>

@endsection
