<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Constructora</title>
 
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #cdd7df;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      display: flex;
      width: 900px;
      height: 550px;
      background-color: white;
      border-radius: 20px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
      overflow: hidden;
    }

    .image-side {
      flex: 1;
      background: linear-gradient(180deg, #8aa7bc, #3e6985);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      color: #cdd7df;
    }

    .image-side h1 {
      font-size: 2em;
      margin-bottom: 10px;
    }

    .image-side img {
      width: 80%;
      border-radius: 10px;
    }

    .form-side {
      flex: 1;
      background-color: #f8f9fa;
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    h2 {
      color: #0d273d;
      margin-bottom: 10px;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    input, select {
      margin: 10px 0;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #a6bed1;
      font-size: 1em;
    }

    button {
      background-color: #3e6985;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 8px;
      margin-top: 15px;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background-color: #0d273d;
    }

    .switch {
      text-align: center;
      margin-top: 10px;
      color: #3e6985;
      cursor: pointer;
    }

    .hidden {
      display: none;
    }
  </style>

</head>
<body>

  <div class="container">
    <div class="image-side">
      <h1>Bienvenido</h1>
      <p>Control de proyectos - Constructora  </p>
      <img src="{{ asset('img/logo.png') }}" alt="Imagen constructora">

    </div>

    <div class="form-side">
      <!-- LOGIN -->
      <div id="login-form">
        <h2>Iniciar Sesión</h2>
        <form method="POST" action="/login">
  @csrf  <!-- 👈 ESTE TOKEN EVITA EL ERROR 419 -->

  <input type="text" name="username" placeholder="Usuario" autocomplete="username" required>
  <input type="password" name="password" placeholder="Contraseña" autocomplete="current-password" required>

  <button type="submit">Entrar</button>
</form>


        
