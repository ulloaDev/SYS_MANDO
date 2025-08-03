<?php
// controllers/ApiController.php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../services/MaintenanceService.php';
require_once __DIR__ . '/../services/TechnicianService.php';
require_once __DIR__ . '/../models/MantenimientoModel.php';

class ApiController extends BaseController {
    private $db;
    private $usuarioId;
    private MaintenanceService $maintenanceService;
    private TechnicianService $technicianService;
    private MantenimientoModel $mantenimientoModel;

    public function __construct(
        $db,
        $usuarioId,
        MaintenanceService $maintenanceService,
        TechnicianService $technicianService
    ) {
        parent::__construct(); // Si BaseController tiene constructor
        $this->db = $db;
        $this->usuarioId = $usuarioId;
        $this->maintenanceService = $maintenanceService;
        $this->technicianService = $technicianService;
        $this->mantenimientoModel = new MantenimientoModel($db);
    }

    /**
     * Obtiene eventos para el calendario
     */
    public function getEvents(): void {
        try {
            $year = (int)($_GET['year'] ?? date('Y'));
            $clubId = isset($_GET['club']) ? (int)$_GET['club'] : null;
            
            $events = $this->maintenanceService->getCalendarEvents($year, $clubId);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $events
            ]);
            
        } catch (Exception $e) {
            $this->handleException($e, 'ApiController::getEvents');
        }
    }

    /**
     * Obtiene la programación de mantenimientos
     */
    public function obtenerProgramacion(array $params = []): void {
        try {
            if (empty($params['club_id'])) {
                throw new InvalidInputException("El parámetro club_id es requerido", 400);
            }
            
            $result = $this->mantenimientoModel->getProgramacion($params);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $result
            ]);
            
        } catch (InvalidInputException $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], $e->getCode());
        } catch (\PDOException $e) {
            $this->handleException($e, 'ApiController::obtenerProgramacion');
        }
    }

    /**
     * Guarda una nueva programación de mantenimiento
     */
    public function guardarProgramacion(array $data): void {
        try {
            $data['usuario_id'] = $this->usuarioId; // Añade el ID del usuario que realiza la acción
            $result = $this->mantenimientoModel->guardar($data);
            
            $this->jsonResponse($result);
            
        } catch (Exception $e) {
            $this->handleException($e, 'ApiController::guardarProgramacion');
        }
    }

    /**
     * Actualiza el estado de un mantenimiento
     */
    public function updateMaintenanceStatus(): void {
        try {
            $this->validateRequestMethod('POST');
            
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $id = (int)($input['id'] ?? 0);
            $status = $input['status'] ?? '';
            
            $success = $this->maintenanceService->updateStatus($id, $status);
            
            $this->jsonResponse([
                'success' => $success,
                'message' => $success ? 'Estado actualizado' : 'No se pudo actualizar'
            ]);
            
        } catch (Exception $e) {
            $this->handleException($e, 'ApiController::updateMaintenanceStatus');
        }
    }

    /**
     * Obtiene estadísticas de mantenimiento
     */
    public function getMaintenanceStats(): void {
        try {
            $year = (int)($_GET['year'] ?? date('Y'));
            $clubId = isset($_GET['club']) ? (int)$_GET['club'] : null;
            
            $stats = $this->maintenanceService->getMaintenanceStats($year, $clubId);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (Exception $e) {
            $this->handleException($e, 'ApiController::getMaintenanceStats');
        }
    }

    /**
     * Maneja excepciones y devuelve respuesta JSON
     */
    protected function handleException(Exception $e, string $context): void {
        error_log("Error en $context: " . $e->getMessage());
        
        $code = $e->getCode() ?: 500;
        $message = $code === 500 ? 'Error interno del servidor' : $e->getMessage();
        
        $this->jsonResponse([
            'success' => false,
            'error' => $message,
            'code' => $code
        ], $code);
    }
}