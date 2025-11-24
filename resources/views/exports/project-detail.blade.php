<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Proyecto - {{ $project->name }}</title>

    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
            color: #333;
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 10px;
        }

        .header img {
            height: 55px;
            margin-right: 15px;
        }

        h1 {
            margin: 0;
            font-size: 26px;
            color: #2c3e50;
        }

        h3 {
            margin: 0;
            font-size: 14px;
            color: #777;
        }

        .grid {
            display: flex;
            width: 100%;
            gap: 15px;
            margin-bottom: 15px;
        }

        .card {
            flex: 1;
            padding: 12px 15px;
            background: #fafafa;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .card-title {
            font-size: 13px;
            color: #555;
            margin-bottom: 5px;
        }

        .card-value {
            font-size: 20px;
            font-weight: bold;
        }

        .green { color: #28a745; }
        .red { color: #dc3545; }
        .blue { color: #007bff; }

        .section {
            margin-top: 25px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
        }

        .section h2 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 7px;
            text-align: left;
            font-size: 12px;
        }

        th {
            background: #f2f2f2;
        }

    </style>
</head>

<body>

    <!-- ENCABEZADO -->
    <div class="header">
        <img src="{{ public_path('img/logoR.png') }}">
        <div>
            <h1>Recursamos S.A.</h1>
            <h3>Reporte del Proyecto</h3>
        </div>
    </div>

    <h2>{{ $project->name }}</h2>
    <small>Cliente: <strong>{{ $project->client }}</strong></small>

    <!-- TARJETAS PRINCIPALES -->
    <div class="grid">
        <div class="card">
            <div class="card-title">Avance del Proyecto</div>
            <div class="card-value blue">{{ number_format($project->progress ?? 0, 1) }}%</div>
        </div>

        <div class="card">
            <div class="card-title">Presupuesto Asignado</div>
            <div class="card-value blue">${{ number_format($project->budget, 2) }}</div>
        </div>

        <div class="card">
            <div class="card-title">Gasto Total</div>
            <div class="card-value red">${{ number_format($project->expenses->sum('amount') ?? 0, 2) }}</div>
        </div>

        <div class="card">
            <div class="card-title">Disponible</div>
            <div class="card-value green">
                ${{ number_format($project->budget - ($project->expenses->sum('amount') ?? 0), 2) }}
            </div>
        </div>
    </div>


    

    <!-- INFO DEL PROYECTO -->
    <div class="section">
        <h2>Información del Proyecto</h2>

        <table>
            <tr><th>Fecha de Inicio</th><td>{{ $project->start_date }}</td></tr>
            <tr><th>Supervisor</th><td>{{ $project->supervisor->name ?? 'Sin asignar' }}</td></tr>
            <tr><th>Estado</th><td>{{ $project->estado_esp }}</td></tr>
            <tr><th>Descripción</th><td>{{ $project->description ?? 'N/A' }}</td></tr>
        </table>
    </div>

    <!-- FINANZAS -->
    <div class="section">
        <h2>Análisis Financiero</h2>

        <table>
            <tr>
                <th>% Presupuesto Utilizado</th>
                <td>{{ number_format(($project->expenses->sum('amount') / max($project->budget,1)) * 100, 1) }}%</td>
            </tr>

            <tr>
                <th>Desviación</th>
                <td class="{{ ($project->budget - $project->expenses->sum('amount')) >= 0 ? 'green' : 'red' }}">
                    ${{ number_format($project->budget - $project->expenses->sum('amount'), 2) }}
                </td>
            </tr>
        </table>
    </div>

    <!-- GASTOS -->
    <div class="section">
        <h2>Gastos</h2>

        @if($project->expenses->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Monto</th>
                    <th>Fecha</th>
                </tr>
            </thead>

            <tbody>
                @foreach($project->expenses as $e)
                <tr>
                    <td>{{ $e->description }}</td>
                    <td>${{ number_format($e->amount, 2) }}</td>
                    <td>{{ $e->date }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p>No hay gastos registrados.</p>
        @endif
    </div>

    <!-- PERSONAL -->
    <div class="section">
        <h2>Personal</h2>
        @if($project->workers->count() > 0)
            <ul>
                @foreach($project->workers as $w)
                    <li>{{ $w->name }} ({{ $w->role }})</li>
                @endforeach
            </ul>
        @else
            <p>Sin personal asignado.</p>
        @endif
    </div>

</body>



</html>
