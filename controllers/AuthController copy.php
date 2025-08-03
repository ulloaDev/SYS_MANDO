<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

class AuthController {
    private $db;
    private $usuarioModel;

    public function __construct($db) {
        $this->db = $db;
        $this->usuarioModel = new UsuarioModel($db);
    }

    /**
     * Muestra el formulario de login
     */
    public function mostrarLogin(): void {
        // Verificar si ya está logueado
        if (isset($_SESSION['usuario_id'])) {
            header('Location: /sistema_manto/mantenimiento/dashboard');
            exit;
        }

        // Mostrar vista de login
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Procesa el formulario de login
     */
    public function procesarLogin(array $data): array {
        try {
            // Validar datos básicos
            if (empty($data['usuario']) || empty($data['password'])) {
                throw new Exception('Usuario y contraseña son requeridos', 400);
            }

            // Autenticar usuario
            $usuario = $this->usuarioModel->autenticar($data['usuario'], $data['password']);

            if (!$usuario) {
                throw new Exception('Credenciales incorrectas', 401);
            }

            // Crear sesión
            session_start();
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_rol'] = $usuario['rol'];
            $_SESSION['usuario_club_id'] = $usuario['club_id'] ?? null;

            return [
                'success' => true,
                'message' => 'Login exitoso',
                'redirect' => '/sistema_manto/mantenimiento/dashboard'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ];
        }
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout(): void {
        session_start();
        session_unset();
        session_destroy();
        
        header('Location: /sistema_manto/auth/login');
        exit;
    }

    /**
     * Verifica si el usuario está autenticado
     */
    public static function verificarAutenticacion(): void {
        session_start();
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /sistema_manto/auth/login');
            exit;
        }
    }

    /**
     * Verifica permisos de administrador
     */
    public static function verificarAdmin(): void {
        session_start();
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
            header('Location: /sistema_manto/auth/login');
            exit;
        }
    }
}