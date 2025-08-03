<?php
function manejarRuta(string $controlador, string $accion): void {
    try {
        // Autoload de clases
        require_once __DIR__.'/../config/database.php';
        $db = Database::getInstance();
        
        // Cargar controlador
        $archivoControlador = __DIR__."/../controllers/{$controlador}Controller.php";
        if (!file_exists($archivoControlador)) {
            throw new Exception("Controlador no encontrado", 404);
        }
        
        require_once $archivoControlador;
        $nombreClase = "{$controlador}Controller";
        
        if (!class_exists($nombreClase)) {
            throw new Exception("Clase {$nombreClase} no existe", 500);
        }
        
        // Ejecutar acción
        $instancia = new $nombreClase($db);
        $instancia->$accion();
        
    } catch (Throwable $e) {
        http_response_code($e->getCode() ?: 500);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage(),
            'code' => $e->getCode()
        ]);
        exit;
    }
}