<?php
class MantenimientoController {
    private $model;

    public function __construct(PDO $db) {
        require_once __DIR__.'/../models/MantenimientoModel.php';
        $this->model = new MantenimientoModel($db);
    }

    public function listar() {
        try {
            header('Content-Type: application/json');
            
            // Validar fechas
            $filtros = [
                'fecha_inicio' => isset($_GET['fecha_inicio']) ? 
                    \DateTime::createFromFormat('Y-m-d', $_GET['fecha_inicio']) : null,
                'fecha_fin' => isset($_GET['fecha_fin']) ? 
                    \DateTime::createFromFormat('Y-m-d', $_GET['fecha_fin']) : null
            ];

            if (($filtros['fecha_inicio'] && !$filtros['fecha_inicio']) || 
                ($filtros['fecha_fin'] && !$filtros['fecha_fin'])) {
                throw new Exception("Formato de fecha inválido. Use YYYY-MM-DD");
            }

            $resultados = $this->model->obtenerProgramacion($filtros);
            
            echo json_encode([
                'success' => true,
                'data' => $resultados,
                'total' => count($resultados)
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function guardar() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido", 405);
            }

            $datos = json_decode(file_get_contents('php://input'), true);
            
            // Validación estricta
            $camposRequeridos = ['equipo_id', 'scheduled_date'];
            foreach ($camposRequeridos as $campo) {
                if (empty($datos[$campo])) {
                    throw new Exception("El campo $campo es requerido", 400);
                }
            }

            // Sanitización
            $datosLimpios = [
                'equipo_id' => (int)$datos['equipo_id'],
                'scheduled_date' => filter_var($datos['scheduled_date'], FILTER_SANITIZE_STRING),
                'tecnico' => isset($datos['tecnico']) ? filter_var($datos['tecnico'], FILTER_SANITIZE_STRING) : null
            ];

            $resultado = $this->model->guardarProgramacion($datosLimpios);
            
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'data' => $resultado
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error en la base de datos',
                'details' => $e->getMessage()
            ]);
        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}