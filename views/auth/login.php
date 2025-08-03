<?php
// Verificar si el usuario ya está autenticado
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/UsuarioModel.php';
if (isset($_SESSION['usuario'])) {
    header('Location: /dashboard');
    exit;
}


// Cuando validas las credenciales correctamente:
$usuarioModel = new UsuarioModel((new Database())->getConnection());
$usuario = $usuarioModel->validarUsuario($_POST['usuario'], $_POST['password']);

if ($usuario) {
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nombre'] = $usuario['nombre'];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Credenciales incorrectas']);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Sistema de Mantenimiento - Login</title>
 <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100">
 <div class="min-h-screen flex items-center justify-center">
  <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
   <div class="text-center mb-6">
    <i class="fas fa-tools text-4xl text-blue-600 mb-2"></i>
    <h1 class="text-2xl font-bold text-gray-800">Sistema de Mantenimiento</h1>
    <p class="text-gray-600">Ingrese sus credenciales</p>
   </div>
   <form id="loginForm" class="space-y-4">
    <div>
     <label for="username" class="block text-sm font-medium text-gray-700">Usuario</label>
     <input type="text" id="username" name="username" required
      class="mt-1 p-2 w-full border rounded-md focus:ring-blue-500 focus:border-blue-500">
    </div>
    <div>
     <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
     <input type="password" id="password" name="password" required
      class="mt-1 p-2 w-full border rounded-md focus:ring-blue-500 focus:border-blue-500">
    </div>
    <div>
     <button type="submit"
      class="w-full bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <i class="fas fa-sign-in-alt mr-2"></i>Ingresar </button>
    </div>
   </form>
   <div id="loginMessage" class="mt-4 text-center text-red-600 hidden"></div>
  </div>
 </div>
 <script>
 document.getElementById('loginForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const messageDiv = document.getElementById('loginMessage');
  fetch('/api.php?endpoint=login', {
   method: 'POST',
   body: formData
  }).then(response => response.json()).then(data => {
   if (data.success) {
    window.location.href = '/dashboard';
   } else {
    messageDiv.textContent = data.message || 'Error en las credenciales';
    messageDiv.classList.remove('hidden');
   }
  }).catch(error => {
   messageDiv.textContent = 'Error al conectar con el servidor';
   messageDiv.classList.remove('hidden');
  });
 });
 </script>
</body>

</html>