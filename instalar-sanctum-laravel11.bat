@echo off
echo ========================================
echo INSTALACION DE SANCTUM - LARAVEL 11
echo Sistema de Construccion - Dashboard Supervisor
echo ========================================
echo.

:: Verificar que estamos en la carpeta del proyecto
if not exist "artisan" (
    echo [ERROR] No se encontro el archivo artisan
    echo Por favor ejecuta este script desde la raiz del proyecto Laravel
    pause
    exit /b 1
)

echo [1/7] Instalando Laravel Sanctum...
echo.
call composer require laravel/sanctum
if errorlevel 1 (
    echo [ERROR] No se pudo instalar Sanctum
    pause
    exit /b 1
)
echo [OK] Sanctum instalado
echo.

echo [2/7] Publicando configuracion de Sanctum...
echo.
call php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
echo [OK] Configuracion publicada
echo.

echo [3/7] Ejecutando migraciones...
echo.
call php artisan migrate
if errorlevel 1 (
    echo [ADVERTENCIA] Error en migraciones - verifica tu base de datos
)
echo [OK] Migraciones ejecutadas
echo.

echo [4/7] Creando config/cors.php...
echo.
if not exist "config\cors.php" (
    (
        echo ^<?php
        echo.
        echo return [
        echo     'paths' =^> ['api/*', 'sanctum/csrf-cookie'],
        echo     'allowed_methods' =^> ['*'],
        echo     'allowed_origins' =^> ['*'],
        echo     'allowed_origins_patterns' =^> [],
        echo     'allowed_headers' =^> ['*'],
        echo     'exposed_headers' =^> [],
        echo     'max_age' =^> 0,
        echo     'supports_credentials' =^> true,
        echo ];
    ) > config\cors.php
    echo [OK] Archivo config/cors.php creado
) else (
    echo [INFO] config/cors.php ya existe
)
echo.

echo [5/7] Limpiando caches...
echo.
call php artisan config:clear
call php artisan cache:clear
call php artisan route:clear
call php artisan view:clear
echo [OK] Caches limpiadas
echo.

echo [6/7] Verificando instalacion...
echo.
call composer show laravel/sanctum > nul 2>&1
if errorlevel 1 (
    echo [ERROR] Sanctum no se instalo correctamente
    pause
    exit /b 1
)
echo [OK] Sanctum verificado
echo.

echo [7/7] Optimizando configuracion...
echo.
call php artisan config:cache
call php artisan route:cache
echo [OK] Optimizacion completada
echo.

echo ========================================
echo INSTALACION COMPLETADA!
echo ========================================
echo.
echo SIGUIENTES PASOS IMPORTANTES:
echo.
echo 1. Abre el archivo .env y AGREGA estas lineas:
echo.
echo    SESSION_DRIVER=cookie
echo    SESSION_DOMAIN=localhost
echo    SANCTUM_STATEFUL_DOMAINS=localhost:8000,127.0.0.1:8000,localhost
echo.
echo 2. Abre app/Models/User.php y verifica que tenga:
echo.
echo    use Laravel\Sanctum\HasApiTokens;
echo.
echo    class User extends Authenticatable
echo    {
echo        use HasApiTokens, HasFactory, Notifiable;
echo        ...
echo    }
echo.
echo 3. Abre bootstrap/app.php y asegurate que tenga:
echo.
echo    $middleware-^>api(prepend: [
echo        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
echo    ]);
echo.
echo 4. Ejecuta: php artisan route:list
echo    Para verificar las rutas API
echo.
echo 5. Reinicia el servidor: php artisan serve
echo.
echo 6. Prueba: http://localhost:8000/api/test
echo.
echo ========================================
pause
