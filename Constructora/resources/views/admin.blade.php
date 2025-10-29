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

    /* ======== ALERTAS Y FILTROS ======== */
    .alert {
        background-color: #e6f3ff;
        color: #0d273d;
        border-left: 5px solid #3e6985;
        padding: 10px 15px;
        border-radius: 6px;
        font-weight: 500;
    }

    select {
        width: 100%;
        padding: 8px 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
    }

    .export-buttons {
        display: flex;
        gap: 10px;
    }
</style>

<!-- ======== DASHBOARD ADMINISTRADOR ======== -->
<div class="sidebar">
    <h2>Administrador</h2>
    <a href="#" class="active">Panel</a>
    <a href="#">Registrar Presupuesto</a>
    <a href="#">Reportes</a>
    <a href="#">Avance</a>
    <a href="#">Filtrar Proyectos</a>
    <a href="#">Comparar Proyectos</a>
    <a href="#">Exportar Datos</a>
</div>

<div class="main-content">
    <h1>Bienvenido, Administrador 🏗️</h1>

    <div class="card">
        <h3>Registrar Presupuesto por Proyecto</h3>
        <form>
            <label>Nombre del Proyecto:</label>
            <input type="text" class="form-control" placeholder="Ej. Torre A"><br>
            <label>Presupuesto Asignado ($):</label>
            <input type="number" class="form-control" placeholder="Ej. 500000"><br>
            <button class="btn">Registrar</button>
        </form>
    </div>

    <div class="card">
        <h3>Reportes de Gastos vs Presupuesto</h3>
        <p>Genera reportes para controlar desviaciones de presupuesto.</p>
        <button class="btn">Generar Reporte</button>
    </div>

    <div class="card">
        <h3>Visualizar Porcentaje de Avance</h3>
        <p>Proyecto “Torre Norte” — Avance: <strong>72%</strong></p>
        <div style="background-color: #8aa7bc; border-radius: 10px; height: 20px; width: 100%;">
            <div style="width:72%; height:100%; background-color:#3e6985; border-radius:10px;"></div>
        </div>
    </div>

    <div class="card">
        <h3>Filtrar Proyectos por Estado</h3>
        <select>
            <option>Activos</option>
            <option>Terminados</option>
            <option>En pausa</option>
        </select>
        <br><br>
        <button class="btn">Aplicar Filtro</button>
    </div>

    <div class="card">
        <h3>Comparar Proyectos</h3>
        <form>
            <label>Proyecto 1:</label>
            <input type="text" class="form-control" placeholder="Ej. Torre A"><br>
            <label>Proyecto 2:</label>
            <input type="text" class="form-control" placeholder="Ej. Torre B"><br>
            <button class="btn">Comparar</button>
        </form>
    </div>

    <div class="card">
        <h3>Exportar Reportes</h3>
        <p>Descarga los reportes en formato PDF o Excel para presentarlos a inversionistas.</p>
        <div class="export-buttons">
            <button class="btn">📄 Exportar PDF</button>
            <button class="btn">📊 Exportar Excel</button>
        </div>
    </div>
</div>

@endsection
