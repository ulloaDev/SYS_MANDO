<?php
header('Content-Type: application/json');

try {
    // Incluir los controladores necesarios
    require_once __DIR__ . '/controllers/ApiController.php';
    require_once __DIR__ . '/controllers/ReportController.php'; // Incluir el ReportController

    // Instanciar los controladores
    $apiController = new ApiController();
    $reportController = new ReportController(); // Instanciar el ReportController

    // Obtener el endpoint de la URL
    $endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';
    
    // Enrutamiento de los endpoints
    switch ($endpoint) {
        case 'programacion':
            $apiController->obtenerProgramacion();
            break;
                    case 'tecnicos':
            $apiController->obtenerTecnicos();
            break;
                    case 'reportPreview': // Nuevo endpoint para la vista previa del reporte
                $reportController->reportPreview();
            break;
                                default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint no encontrado']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}