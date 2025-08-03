<?php
/**
 * Controlador de Mantenimientos - Versión Final
 * Cubre todas las acciones posibles del usuario:
 * - CRUD de mantenimientos
 * - Gestión de calendario
 * - Registro de ejecución
 * - Cambio de estados
 * - Reportes y estadísticas
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/MantenimientoModel.php';
require_once __DIR__ . '/../models/TecnicoModel.php';
require_once __DIR__ . '/../models/EquipoModel.php';

class MantenimientoController {
    private $db;
    private $mantenimientoModel;
    private $tecnicoModel;
    private $equipoModel;
      private $model;

    public function __construct($db) {
        $this->db = $db;
        $this->mantenimientoModel = new MantenimientoModel($this->db);
        $this->tecnicoModel = new TecnicoModel($this->db);
        $this->equipoModel = new EquipoModel($this->db);
    }
 public function mostrarCalendario(array $params): array {
        return $this->model->obtenerEventosCalendario($params);
    }

    public function mostrarLista(array $params): array {
        return $this->model->obtenerMantenimientos($params);
    }

    public function guardarMantenimiento(array $data): array {
        $data['usuario_id'] = $_SESSION['usuario_id'] ?? null;
        return $this->model->guardarProgramacion($data);
    }
    /**
     * Acciones principales del usuario
     */
    
    // 1. Gestión de programación
    public function programarMantenimiento(array $data) {
        try {
            $this->validarDatosMantenimiento($data);
            
            $result = $this->mantenimientoModel->crearPlanificacion($data);
            
            if($result['success']) {
                $this->equipoModel->actualizarEstado($data['IdEq'], 'En Mantenimiento Programado');
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Mantenimiento programado exitosamente',
                    'data' => $result['id']
                ]);
            }
            
            throw new Exception($result['message']);
            
        } catch (Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 2. Actualización de mantenimiento
    public function actualizarMantenimiento($id, array $data) {
        try {
            $this->validarDatosMantenimiento($data);
            
            $result = $this->mantenimientoModel->actualizarMantenimiento($id, $data);
            
            if(!$result['success']) {
                throw new Exception($result['message']);
            }
            
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Mantenimiento actualizado correctamente'
            ]);
            
        } catch (Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 3. Cancelación de mantenimiento
    public function cancelarMantenimiento($id, $motivo) {
        try {
            $result = $this->mantenimientoModel->cancelarMantenimiento($id, $motivo, $_SESSION['user_id'] ?? 'sistema');
            
            if($result['success']) {
                $mantenimiento = $this->mantenimientoModel->obtenerMantenimientoPorId($id);
                $this->equipoModel->actualizarEstado($mantenimiento['IdEq'], 'Disponible');
                
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Mantenimiento cancelado correctamente'
                ]);
            }
            
            throw new Exception($result['message']);
            
        } catch (Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 4. Registro de ejecución
    public function registrarEjecucion(array $data) {
        try {
            $required = ['IdPlan', 'ResultadoGeneral', 'TecnicoEjecutor'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    throw new Exception("El campo $field es requerido");
                }
            }

            $result = $this->mantenimientoModel->recordExecution($data);
            
            if($result['success']) {
                // Actualizar estado del equipo
                $mantenimiento = $this->mantenimientoModel->obtenerMantenimientoPorId($data['IdPlan']);
                $nuevoEstado = ($data['ResultadoGeneral'] == 'Satisfactorio') ? 'Disponible' : 'Requiere Reparación';
                $this->equipoModel->actualizarEstado($mantenimiento['IdEq'], $nuevoEstado);
                
                // Actualizar último mantenimiento
                $this->equipoModel->actualizarUltimoMantenimiento(
                    $mantenimiento['IdEq'],
                    date('Y-m-d H:i:s')
                );
                
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Ejecución registrada correctamente'
                ]);
            }
            
            throw new Exception($result['message']);
            
        } catch (Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 5. Cambio de estado
    public function cambiarEstado($id, $estado) {
        try {
            $estadosPermitidos = ['Pendiente', 'En Proceso', 'Completado', 'Cancelado'];
            if (!in_array($estado, $estadosPermitidos)) {
                throw new Exception("Estado no válido");
            }

            $result = $this->mantenimientoModel->updateStatus($id, $estado);
            
            if(!$result['success']) {
                throw new Exception($result['message']);
            }
            
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Estado actualizado correctamente'
            ]);
            
        } catch (Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Consultas y reportes
     */
    
    // 1. Obtener mantenimientos por rango de fechas
    public function obtenerMantenimientos($fechaInicio, $fechaFin, $filtros = []) {
        try {
            $data = $this->mantenimientoModel->getMantenimientosProgramados([
                'fecha_desde' => $fechaInicio,
                'fecha_hasta' => $fechaFin,
                ...$filtros
            ]);
            
            return $this->jsonResponse([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 2. Obtener estadísticas
    public function obtenerEstadisticas($fechaInicio, $fechaFin) {
        try {
            $data = $this->mantenimientoModel->getEstadisticasMantenimiento($fechaInicio, $fechaFin);
            
            return $this->jsonResponse([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 3. Obtener mantenimientos por equipo
    public function obtenerPorEquipo($idEquipo) {
        try {
            $data = $this->mantenimientoModel->getByEquipment($idEquipo);
            
            return $this->jsonResponse([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Funciones auxiliares
     */
    
    private function validarDatosMantenimiento(array $data) {
        $required = ['IdEq', 'IdTipoManto', 'FechaProgramada', 'TecnicoAsignado'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("El campo $field es requerido");
            }
        }
        
        // Validar fecha futura
        if (strtotime($data['FechaProgramada']) < strtotime('today')) {throw new Exception("La fecha programada debe ser futura");}
        if (!$this->equipoModel->verificarDisponibilidad($data['IdEq'],$data['FechaProgramada'],$data['HoraInicio'] ?? '00:00',$data['HoraFin'] ?? '00:00',            $data['IdPlan'] ?? null
        )) {
            throw new Exception("El equipo no está disponible en la fecha seleccionada");
        }
    }
    private function jsonResponse($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}