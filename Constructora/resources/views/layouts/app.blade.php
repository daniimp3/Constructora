<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Control de Proyectos</title>

    <!-- Fuente y estilos -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <!-- Íconos opcionales (Lucide o FontAwesome) -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

    <!-- ====== CONTENEDOR PRINCIPAL ====== -->
    <div class="layout-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <h2>Constructora</h2>
            </div>
            <nav>
                <!-- Estas opciones cambian según el rol -->
                <!-- Las vistas hijas pueden sobrescribirlas -->
                @yield('sidebar')
            </nav>
            <div class="footer-sidebar">
                <a href="#">Cerrar sesión</a>
            </div>
        </aside>

        <!-- Contenido principal -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <script>
        // Cargar íconos Lucide (opcional)
        lucide.createIcons();
    </script>
</body>
</html>
