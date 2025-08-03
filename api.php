<?php
// FILE: C:\xampp\htdocs\sistema_manto\api.php

// 1. Iniciar sesión PHP (esto mantiene la autenticación tradicional)
session_start();

// 2. Configuración básica
header("Content-Type: application/json; charset=UTF-8");

// 3. Incluir archivos necesarios (como los tenías)
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/ApiController.php';

try {
    // 4. Conexión a la base de datos (igual que antes)
    $database = new Database();
    $db = $database->getConnection();
    
    // 5. Verificar si el usuario está logueado (sistema tradicional)
    if (!isset($_SESSION['usuario_id'])) {
        throw new Exception("Debes iniciar sesión primero", 401);
    }
    
    // 6. Instanciar el controlador (igual que antes)
    $apiController = new ApiController($db);
    
    // 7. Determinar la acción solicitada
    $action = $_GET['action'] ?? '';
    
    // 8. Sistema de rutas tradicional con switch
    switch ($action) {
        case 'obtener_programacion':
            $response = $apiController->obtenerProgramacion();
            break;
            
        case 'guardar_programacion':
            $inputData = json_decode(file_get_contents('php://input'), true);
            $response = $apiController->guardarProgramacion($inputData);
            break;
            
        case 'obtener_tecnicos':
            $response = $apiController->obtenerTecnicos();
            break;
            
        default:
            throw new Exception("Acción no reconocida: $action", 404);
    }
    
    // 9. Devolver la respuesta
    echo json_encode([
        'success' => true,
        'data' => $response
    ]);
    
} catch (Exception $e) {
    // 10. Manejo de errores
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}