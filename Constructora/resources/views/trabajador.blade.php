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
        background-color: #e6f3ff;
        color: #0d273d;
        border-left: 5px solid #3e6985;
        padding: 10px 15px;
        border-radius: 6px;
        font-weight: 500;
    }

    ul {
        list-style: none;
        padding-left: 0;
    }

    ul li {
        background: #f4f6f8;
        padding: 8px 12px;
        border-radius: 5px;
        margin: 5px 0;
        border-left: 4px solid #3e6985;
    }
</style>

<!-- ======== DASHBOARD TRABAJADOR ======== -->
<div class="sidebar">
    <h2>Trabajador</h2>
    <a href="#" class="active">Panel</a>
    <a href="#">Mis Tareas</a>
    <a href="#">Registrar Avance</a>
    <a href="#">Notificar Problemas</a>
</div>

<div class="main-content">
    <h1>Bienvenido, Trabajador 👷‍♂️</h1>

    <div class="card">
        <h3>Mis Tareas Asignadas</h3>
        <ul>
            <li>Revisar nivel de cimentación en zona norte.</li>
            <li>Preparar mezcla para muro de contención.</li>
            <li>Verificar alineación de columnas principales.</li>
        </ul>
    </div>

    <div class="card">
        <h3>Registrar Avance de Obra</h3>
        <form enctype="multipart/form-data">
            <label>Descripción del avance:</label>
            <input type="text" class="form-control" placeholder="Ej. Avance del muro sur al 75%"><br>
            <label>Subir evidencia (foto o nota):</label>
            <input type="file" class="form-control"><br>
            <button class="btn">Enviar</button>
        </form>
    </div>

    <div class="card">
        <h3>Notificar Problemas</h3>
        <form>
            <label>Descripción del problema:</label>
            <textarea class="form-control" rows="3" placeholder="Ej. Falta de material o herramienta dañada"></textarea><br>
            <button class="btn">Notificar</button>
        </form>
    </div>

    <div class="card">
        <h3>Últimas Notificaciones</h3>
        <div class="alert">📢 Tarea completada: “Revisión del muro norte”.</div>
        <div class="alert">📢 Nueva tarea asignada: “Colocar refuerzo en losas”.</div>
    </div>
</div>

@endsection
