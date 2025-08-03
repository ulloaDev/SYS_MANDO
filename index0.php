<?php
// C:\xampp\htdocs\sistema_manto\index.php

// 1. Incluir el archivo de configuración de la base de datos (si es necesario por separado)
//    Asegúrate de que Database.php esté disponible
require_once __DIR__ . '/config/database.php';

// 2. Incluir el modelo
//    Es CRÍTICO incluir el modelo ANTES de instanciarlo o de incluir el controlador que lo usa.
require_once __DIR__ . '/models/MantenimientoModel.php';

// 3. Incluir el controlador
require_once __DIR__ . '/controllers/MantenimientoController.php';

// 4. (Opcional) Incluir el header si tu diseño lo requiere aquí, aunque idealmente
//    el header y footer son manejados por las vistas.
// require_once __DIR__ . '/includes/header.php'; // Si header.php maneja HTML de la página

// --- Instanciación de las clases ---

// Primero, crea una instancia de la clase Database
$database = new Database();
// Luego, obtén la conexión (o asegúrate de que el modelo la obtenga internamente)
// $dbConnection = $database->getConnection(); // Si el modelo necesita la conexión directamente

// Ahora, crea una instancia del modelo, pasándole la conexión si el modelo la necesita en su constructor
// Si tu MantenimientoModel ya maneja su propia conexión internamente como en tu código:
$mantenimientoModel = new MantenimientoModel();

// Finalmente, crea una instancia del controlador, pasándole la instancia del modelo
// ¡Esta es la línea clave que corrige el error!
$mantenimientoController = new MantenimientoController($mantenimientoModel); // <-- ¡Aquí se pasa el modelo!

// --- Lógica de enrutamiento (ejemplo básico) ---

// Determinar la acción solicitada
$action = $_GET['action'] ?? 'mostrarCalendario'; // Acción por defecto

switch ($action) {
    case 'mostrarCalendario':
        $mantenimientoController->mostrarCalendario();
        break;
    case 'guardarProgramacion':
        $mantenimientoController->guardarProgramacion();
        break;
    case 'cambiarEstado':
        $mantenimientoController->cambiarEstado();
        break;
    case 'ejecutarAccionMultiple':
        $mantenimientoController->ejecutarAccionMultiple();
        break;
    default:
        // Manejar acción no encontrada o mostrar una página de error
        // Puedes redirigir o mostrar un mensaje
        echo "Acción no válida.";
        break;
}

// (Opcional) Incluir el footer si tu diseño lo requiere aquí
// require_once __DIR__ . '/includes/footer.php';