<?php

class MantenimientoController {
    private $model;

    public function __construct(PDO $db) {
        require_once __DIR__.'/../models/MantenimientoModel.php';
        $this->model = new MantenimientoModel($db);
    }

    /**
     * Lista programaciones de mantenimiento en formato JSON
     */
    public function listar(): void {
        try {
            $filtros = [
                'fecha_inicio' => $_GET['fecha_inicio'] ?? null,
                'fecha_fin' => $_GET['fecha_fin'] ?? null
            ];
            
            $resultados = $this->model->obtenerProgramacion($filtros);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $resultados,
                'total' => count($resultados)
            ]);

        } catch (PDOException $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Error en la base de datos',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function calendario(): void {
        try {
            $this->validateRequestMethod(['GET']);
            $params = $this->getRequestData();
            
            $vista = 'mantenimiento/calendario';
            $datos = [
                'eventos' => $this->model->obtenerProgramacion($params),
                'tecnicos' => $this->model->obtenerTecnicos()
            ];
            
            $this->renderView($vista, $datos);
            
        } catch (Throwable $e) {
            $this->handleError($e, 'MantenimientoController::calendario');
        }
    }

    public function guardar(): void {
        try {
            $this->validateRequestMethod(['POST']);
            $datos = $this->getRequestData();
            
            if (empty($datos['equipo_id']) || empty($datos['scheduled_date'])) {
                throw new Exception("Datos requeridos faltantes", 400);
            }
            
            $resultado = $this->model->guardarProgramacion($datos);
            $this->jsonResponse($resultado, 201);
            
        } catch (Throwable $e) {
            $this->Exception($e, 'MantenimientoController::guardar', [
                'input_data' => $datos ?? null
            ]);
        }
    }
private function validateRequestMethod(array $allowedMethods): void {
    if (!in_array($_SERVER['REQUEST_METHOD'], $allowedMethods)) {
        throw new Exception("Método no permitido", 405);
    }
}

private function getRequestData(): array {
    return $_SERVER['REQUEST_METHOD'] === 'GET' ? $_GET : json_decode(file_get_contents('php://input'), true);
}

private function jsonResponse(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
    }