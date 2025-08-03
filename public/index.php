<?php
// 1. Cargar configuración
require_once __DIR__.'/../config/database.php';

// 2. Configurar respuesta
header("Content-Type: application/json");

try {
    // 3. Conectar a BD
    $db = Database::getInstance();
    
    // 4. Determinar controlador/acción
    $ruta = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    $controlador = $ruta[1] ?? 'mantenimiento';
    $accion = $ruta[2] ?? 'listar';
    
    // 5. Cargar controlador
    $archivoControlador = __DIR__."/../controllers/".ucfirst($controlador)."Controller.php";
    if (!file_exists($archivoControlador)) {
        throw new Exception("Controlador no encontrado", 404);
    }
    
    require_once $archivoControlador;
    $nombreClase = ucfirst($controlador)."Controller";
    $controladorInstancia = new $nombreClase($db);
    
    // 6. Ejecutar acción
    if (!method_exists($controladorInstancia, $accion)) {
        throw new Exception("Acción no válida", 400);
    }
    
    $controladorInstancia->$accion();
    
} catch (Exception $e) {
    // Manejo centralizado de errores
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}