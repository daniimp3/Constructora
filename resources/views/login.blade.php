<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Constructora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            overflow: hidden;
            background: linear-gradient(135deg, #0d273d 0%, #3e6985 100%);
        }

        .login-wrapper {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        /* ========== PANEL IZQUIERDO ========== */
        .left-panel {
            flex: 1;
            background: linear-gradient(135deg, #0d273d 0%, #3e6985 50%, #8aa7bc 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }

        /* Formas decorativas */
        .left-panel::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(138, 167, 188, 0.1);
            border-radius: 50%;
            top: -100px;
            left: -100px;
            animation: float 6s ease-in-out infinite;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(205, 215, 223, 0.1);
            border-radius: 50%;
            bottom: -50px;
            right: -50px;
            animation: float 8s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-20px) scale(1.1); }
        }

        .logo-section {
            position: relative;
            z-index: 1;
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-placeholder {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s;
            animation: pulse 3s ease-in-out infinite;
        }

        .logo-placeholder:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 20px rgba(138, 167, 188, 0.5); }
            50% { box-shadow: 0 0 40px rgba(138, 167, 188, 0.8); }
        }

        .logo-placeholder img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
        }

        .logo-text {
            color: #cdd7df;
            font-size: 12px;
            font-weight: 300;
            margin-top: 10px;
        }

        .welcome-content {
            position: relative;
            z-index: 1;
            color: white;
            text-align: center;
            max-width: 500px;
        }

        .welcome-title {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 20px;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .welcome-divider {
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #8aa7bc, #cdd7df);
            margin: 20px auto;
            border-radius: 2px;
        }

        .welcome-text {
            font-size: 16px;
            line-height: 1.8;
            color: #cdd7df;
            margin-bottom: 30px;
        }

        .learn-more-btn {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 12px 35px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .learn-more-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            color: white;
        }

        /* ========== PANEL DERECHO ========== */
        .right-panel {
            flex: 1;
            background: #cdd7df;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
        }

        .login-box {
            background: rgba(166, 190, 209, 0.6);
            backdrop-filter: blur(20px);
            padding: 50px 45px;
            border-radius: 30px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 60px rgba(13, 39, 61, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .signin-title {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #0d273d;
            margin-bottom: 40px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #0d273d;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid rgba(62, 105, 133, 0.3);
            border-radius: 15px;
            font-size: 15px;
            background: rgba(255, 255, 255, 0.8);
            color: #0d273d;
            transition: all 0.3s;
        }

        .form-input:focus {
            outline: none;
            border-color: #3e6985;
            background: white;
            box-shadow: 0 0 0 4px rgba(62, 105, 133, 0.1);
        }

        .form-input::placeholder {
            color: #8aa7bc;
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #3e6985;
            font-size: 18px;
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #3e6985, #0d273d);
            border: none;
            border-radius: 15px;
            color: white;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .submit-btn:hover:not(:disabled) {
            background: linear-gradient(135deg, #0d273d, #3e6985);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(13, 39, 61, 0.3);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .social-icon {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0d273d;
            font-size: 20px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .social-icon:hover {
            background: rgba(255, 255, 255, 0.5);
            transform: translateY(-3px);
        }

        .alert {
            border-radius: 12px;
            padding: 12px 20px;
            margin-bottom: 20px;
            display: none;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 968px) {
            .login-wrapper {
                flex-direction: column;
            }

            .left-panel {
                min-height: 40vh;
                padding: 40px 30px;
            }

            .welcome-title {
                font-size: 32px;
            }

            .right-panel {
                min-height: 60vh;
            }

            .logo-placeholder {
                width: 80px;
                height: 80px;
            }
        }

        @media (max-width: 576px) {
            .login-box {
                padding: 40px 30px;
            }

            .signin-title {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- ========== PANEL IZQUIERDO ========== -->
        <div class="left-panel">
            <div class="logo-section">
                <div class="logo-placeholder">
                    <!-- AQUÍ VA TU LOGO -->
                    <!-- Opción 1: Si tienes imagen -->
                    <!-- <img src="{{ asset('img/logo.png') }}" alt="Logo"> -->
                    
                    <!-- Opción 2: Icono temporal -->
                    <i class="bi bi-building" style="font-size: 50px; color: white;"></i>
                </div>
                <div class="logo-text">EL LOGO AQI</div>
            </div>

            <div class="welcome-content">
                <h1 class="welcome-title">¡Bienvenido!</h1>
                <div class="welcome-divider"></div>
                <p class="welcome-text">
                    Sistema integral de gestión para constructoras. 
                    Controla tus proyectos, presupuestos y equipo de trabajo 
                    de manera eficiente y profesional.
                </p>
                
            </div>
        </div>

        <!-- ========== PANEL DERECHO ========== -->
        <div class="right-panel">
            <div class="login-box">
                <h2 class="signin-title">Iniciar Sesión</h2>

                <!-- Alerta de error -->
                <div class="alert alert-danger" id="errorAlert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <span id="errorMessage"></span>
                </div>

                <!-- Formulario -->
                <form id="loginForm">
                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label">Correo Electrónico</label>
                        <input 
                            type="email" 
                            class="form-input" 
                            id="email" 
                            placeholder="usuario@ejemplo.com" 
                            required
                            autocomplete="email"
                        >
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label">Contraseña</label>
                        <div class="password-wrapper">
                            <input 
                                type="password" 
                                class="form-input" 
                                id="password" 
                                placeholder="••••••••" 
                                required
                                autocomplete="current-password"
                            >
                            <i class="bi bi-eye toggle-password" id="togglePassword"></i>
                        </div>
                    </div>

                    <!-- Botón Submit -->
                    <button type="submit" class="submit-btn" id="loginBtn">
                        <span id="btnText">Iniciar Sesión</span>
                        <span id="btnSpinner" class="d-none">
                            <span class="spinner-border spinner-border-sm me-2"></span>
                            Iniciando...
                        </span>
                    </button>
                </form>

                
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });

        // Login form
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        const errorAlert = document.getElementById('errorAlert');
        const errorMessage = document.getElementById('errorMessage');

        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            loginBtn.disabled = true;
            btnText.classList.add('d-none');
            btnSpinner.classList.remove('d-none');
            errorAlert.style.display = 'none';

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    showError(data.message || 'Credenciales incorrectas');
                    resetButton();
                }
            } catch (error) {
                showError('Error de conexión. Intenta nuevamente.');
                resetButton();
            }
        });

        function showError(message) {
            errorMessage.textContent = message;
            errorAlert.style.display = 'block';
        }

        function resetButton() {
            loginBtn.disabled = false;
            btnText.classList.remove('d-none');
            btnSpinner.classList.add('d-none');
        }

        document.getElementById('email').focus();
    </script>
</body>
</html>