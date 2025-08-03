<?php
function route($controller, $action, $data = []) {
    $controllerFile = __DIR__ . "/../controllers/{$controller}Controller.php";
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $className = "{$controller}Controller";
        
        if (class_exists($className)) {
            $instance = new $className();
            if (method_exists($instance, $action)) {
                return $instance->$action($data);
            }
        }
    }
    throw new Exception("Ruta no encontrada", 404);
}