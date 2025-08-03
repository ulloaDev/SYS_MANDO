<?php
/**
 * Archivo: controllers/MantenimientoController.php
 * Descripción: Controlador que gestiona las solicitudes HTTP relacionadas con los mantenimientos.
 * Interactúa con MantenimientoModel, TecnicoModel, EquipoModel y prepara los datos para las vistas.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/MantenimientoModel.php';
require_once __DIR__ . '/../models/TecnicoModel.php';
require_once __DIR__ . '/../models/EquipoModel.php';

class MantenimientoController
{
    private $mantenimientoModel;
    private $tecnicoModel;
    private $equipoModel;
    private $db; // Añadir la propiedad db

    public function __construct()
    {
        $this->db = getDB(); // Obtener la instancia de la base de datos
        $this->mantenimientoModel = new MantenimientoModel($this->db);
        $this->tecnicoModel = new TecnicoModel($this->db);
        $this->equipoModel = new EquipoModel($this->db);
    }

    /**
     * Muestra la vista del calendario de mantenimientos.
     * @param array $getData Datos GET de la solicitud.
     */
    public function mostrarCalendario($getData)
    {
        try {
            $currentMonth = isset($getData['month']) ? intval($getData['month']) : date('n');
            $currentYear = isset($getData['year']) ? intval($getData['year']) : date('Y');

            // Asegurarse de que el mes y el año sean válidos
            if ($currentMonth < 1 || $currentMonth > 12) {
                $currentMonth = date('n');
            }
            if ($currentYear < 1900 || $currentYear > 2100) {
                $currentYear = date('Y');
            }

            $firstDayOfMonth = new DateTime("{$currentYear}-{$currentMonth}-01");
            $lastDayOfMonth = new DateTime("{$currentYear}-{$currentMonth}-" . $firstDayOfMonth->format('t'));

            $filtros = [
                'fecha_desde' => $firstDayOfMonth->format('Y-m-d'),
                'fecha_hasta' => $lastDayOfMonth->format('Y-m-d'),
            ];

            $mantenimientosEnMes = [];
            $mantenimientosRaw = $this->mantenimientoModel->getMantenimientosProgramados($filtros);

            foreach ($mantenimientosRaw as $manto) {
                $fecha = $manto['FechaProgramada'];
                if (!isset($mantenimientosEnMes[$fecha])) {
                    $mantenimientosEnMes[$fecha] = [];
                }
                $mantenimientosEnMes[$fecha][] = $manto;
            }

            $tecnicoId = cleanInput($getData['tecnico'] ?? null);
            $tecnicos = $this->tecnicoModel->obtenerTecnicos(); // Usar el método correcto

            // Pasa todas las variables necesarias a la vista
            require_once __DIR__ . '/../views/mantenimiento/calendario.php';

        } catch (MaintenanceException $e) {
            logError("Error en MantenimientoController::mostrarCalendario: " . $e->getMessage(), __FILE__, __LINE__);
            global $error_global;
            $error_global .= "Error al mostrar calendario: " . $e->getMessage();
            require_once __DIR__ . '/../views/mantenimiento/calendario.php'; // Cargar la vista incluso con error
        } catch (Exception $e) {
            logError("Error inesperado en MantenimientoController::mostrarCalendario: " . $e->getMessage(), __FILE__, __LINE__);
            global $error_global;
            $error_global .= "Ocurrió un error inesperado al mostrar calendario: " . $e->getMessage();
            require_once __DIR__ . '/../views/mantenimiento/calendario.php'; // Cargar la vista incluso con error
        }
    }

    /**
     * Muestra la vista de lista de mantenimientos.
     * @param array $getData Datos GET de la solicitud.
     */
    public function mostrarLista($getData)
    {
        try {
            $filtros = [
                'fecha_desde' => cleanInput($getData['fecha_desde'] ?? ''),
                'fecha_hasta' => cleanInput($getData['fecha_hasta'] ?? ''),
                'estado'      => cleanInput($getData['estado'] ?? ''),
                'tecnico'     => cleanInput($getData['tecnico'] ?? ''),
                'club'        => cleanInput($getData['club'] ?? ''),
            ];

            $mantenimientos = $this->mantenimientoModel->getMantenimientosProgramados($filtros);
            $pendientes = $this->mantenimientoModel->getMantenimientosPendientes(7);

            $fechaInicioMes = date('Y-m-01');
            $fechaFinMes = date('Y-m-t');
            $estadisticas = $this->mantenimientoModel->getEstadisticasMantenimiento($fechaInicioMes, $fechaFinMes);
            $estadisticas = $estadisticas ?? [
                'total_mantenimientos' => 0, 'completados' => 0, 'pendientes' => 0, 'cancelados' => 0,
            ];

            // Pasa todas las variables necesarias a la vista
            require_once __DIR__ . '/../views/mantenimiento/lista.php';

        } catch (MaintenanceException $e) {
            logError("Error en MantenimientoController::mostrarLista: " . $e->getMessage(), __FILE__, __LINE__);
            global $error_global;
            $error_global .= "Error al cargar la lista de mantenimientos: " . $e->getMessage();
            $mantenimientos = []; // Asegurar que sea un array vacío para la vista
            $pendientes = [];
            $estadisticas = [
                'total_mantenimientos' => 0, 'completados' => 0, 'pendientes' => 0, 'cancelados' => 0,
            ];
            require_once __DIR__ . '/../views/mantenimiento/lista.php';
        } catch (Exception $e) {
            logError("Error inesperado en MantenimientoController::mostrarLista: " . $e->getMessage(), __FILE__, __LINE__);
            global $error_global;
            $error_global .= "Ocurrió un error inesperado al cargar la lista: " . $e->getMessage();
            $mantenimientos = [];
            $pendientes = [];
            $estadisticas = [
                'total_mantenimientos' => 0, 'completados' => 0, 'pendientes' => 0, 'cancelados' => 0,
            ];
            require_once __DIR__ . '/../views/mantenimiento/lista.php';
        }
    }

    /**
     * Muestra la vista del formulario de mantenimiento (para crear o editar).
     * @param array $getData Datos GET de la solicitud.
     */
    public function mostrarFormulario($getData)
    {
        $idPlan = cleanInput($getData['id'] ?? null);
        $mantenimiento = null;

        try {
            if ($idPlan) {
                $mantenimiento = $this->mantenimientoModel->obtenerMantenimientoPorId($idPlan);
                if (!$mantenimiento) {
                    throw new Exception("Mantenimiento con ID {$idPlan} no encontrado.");
                }
            }

            $equipos = $this->equipoModel->obtenerEquiposActivos();
            $tecnicos = $this->tecnicoModel->obtenerTecnicos();
            $tiposMantenimiento = $this->mantenimientoModel->obtenerTiposMantenimiento();
            $frecuencias = $this->mantenimientoModel->getFrecuencias(); // Usar el método correcto

            require_once __DIR__ . '/../views/mantenimiento/formulario.php';

        } catch (MaintenanceException $e) {
            logError("Error en MantenimientoController::mostrarFormulario: " . $e->getMessage(), __FILE__, __LINE__);
            global $error_global;
            $error_global .= "Error al cargar el formulario de mantenimiento: " . $e->getMessage();
            // Asegurar que las variables estén definidas incluso si hay error
            $equipos = []; $tecnicos = []; $tiposMantenimiento = []; $frecuencias = [];
            require_once __DIR__ . '/../views/mantenimiento/formulario.php';
        } catch (EquipoException $e) {
            logError("Error de equipo en MantenimientoController::mostrarFormulario: " . $e->getMessage(), __FILE__, __LINE__);
            global $error_global;
            $error_global .= "Error al cargar datos de equipos para el formulario: " . $e->getMessage();
            $equipos = []; $tecnicos = []; $tiposMantenimiento = []; $frecuencias = [];
            require_once __DIR__ . '/../views/mantenimiento/formulario.php';
        } catch (Exception $e) {
            logError("Error inesperado en MantenimientoController::mostrarFormulario: " . $e->getMessage(), __FILE__, __LINE__);
            global $error_global;
            $error_global .= "Ocurrió un error inesperado al cargar el formulario: " . $e->getMessage();
            $equipos = []; $tecnicos = []; $tiposMantenimiento = []; $frecuencias = [];
            require_once __DIR__ . '/../views/mantenimiento/formulario.php';
        }
    }

    /**
     * Guarda un nuevo mantenimiento o actualiza uno existente.
     * Se invoca mediante una solicitud POST.
     * @param array $postData Datos POST de la solicitud.
     * @return array Resultado de la operación (success, message).
     */
    public function guardarMantenimiento($postData)
    {
        $response = ['success' => false, 'message' => ''];
        try {
            $datos = [
                'IdEq'            => cleanInput($postData['IdEq'] ?? null),
                'Serie'           => cleanInput($postData['Serie'] ?? ''), // No se usa directamente en model, pero es buena práctica limpiarlo
                'IdTipoManto'     => cleanInput($postData['IdTipoManto'] ?? null),
                'IdFrecuencia'    => cleanInput($postData['IdFrecuencia'] ?? null),
                'FechaProgramada' => cleanInput($postData['FechaProgramada'] ?? ''),
                'HoraInicio'      => cleanInput($postData['HoraInicio'] ?? '00:00'),
                'HoraFin'         => cleanInput($postData['HoraFin'] ?? '00:00'),
                'TecnicoAsignado' => cleanInput($postData['TecnicoAsignado'] ?? null), // Esto debería ser un ID de técnico, no un nombre
                'Prioridad'       => cleanInput($postData['Prioridad'] ?? 'Media'),
                'Observaciones'   => cleanInput($postData['Observaciones'] ?? null),
                'CreadoPor'       => $_SESSION['user_id'] ?? 'sistema', // Asumiendo user_id en sesión
            ];

            // Calcular estimated_duration si HoraInicio y HoraFin están presentes
            $horaInicioTimestamp = strtotime($datos['FechaProgramada'] . ' ' . $datos['HoraInicio']);
            $horaFinTimestamp = strtotime($datos['FechaProgramada'] . ' ' . $datos['HoraFin']);
            $datos['EstimatedDuration'] = ($horaFinTimestamp && $horaInicioTimestamp) ? round(abs($horaFinTimestamp - $horaInicioTimestamp) / 60) : 120;


            if (empty($postData['IdPlan'])) { // Asumiendo que IdPlan es el campo oculto para el ID en edición
                // Crear nuevo mantenimiento
                $resultado = $this->mantenimientoModel->crearPlanificacion($datos);
                $response['success'] = $resultado['success'];
                $response['message'] = $resultado['message'];
            } else {
                // Actualizar mantenimiento existente
                $idPlan = cleanInput($postData['IdPlan']);
                $resultado = $this->mantenimientoModel->actualizarMantenimiento($idPlan, $datos);
                $response['success'] = $resultado['success'];
                $response['message'] = $resultado['message'];
            }
        } catch (MaintenanceException $e) {
            logError("Error en MantenimientoController::guardarMantenimiento: " . $e->getMessage(), __FILE__, __LINE__);
            $response['message'] = "Error al guardar mantenimiento: " . $e->getMessage();
        } catch (Exception $e) {
            logError("Error inesperado en MantenimientoController::guardarMantenimiento: " . $e->getMessage(), __FILE__, __LINE__);
            $response['message'] = "Ocurrió un error inesperado al guardar mantenimiento: " . $e->getMessage();
        }
        return $response; // Devolver la respuesta para el manejo global en index.php
    }

    /**
     * Cambia el estado de un mantenimiento programado.
     * Se invoca mediante una solicitud POST.
     * @param array $postData Datos POST de la solicitud.
     * @return array Resultado de la operación (success, message).
     */
    public function cambiarEstado($postData)
    {
        $response = ['success' => false, 'message' => ''];
        try {
            $idPlan = cleanInput($postData['IdPlan']);
            $estado = cleanInput($postData['Estado']);
            $usuarioId = $_SESSION['user_id'] ?? 'desconocido'; // Asegurarse de tener un ID de usuario

            $resultado = $this->mantenimientoModel->updateStatus($idPlan, $estado);

            if ($resultado['success']) {
                $response['success'] = true;
                $response['message'] = 'Estado actualizado exitosamente.';

                // Si el estado es 'Completado', redirigir al formulario de registro de ejecución
                if ($estado === 'Completado') {
                    // La redirección real se maneja en index.php después de recibir esta respuesta
                    // Aquí solo se prepara el mensaje y se sugiere la acción.
                    $response['redirect_to'] = 'mantenimiento/registro-ejecucion&id=' . $idPlan;
                }
            } else {
                $response['message'] = $resultado['message'];
            }

        } catch (MaintenanceException $e) {
            logError("Error en MantenimientoController::cambiarEstado: " . $e->getMessage(), __FILE__, __LINE__);
            $response['message'] = "Error al cambiar estado: " . $e->getMessage();
        } catch (Exception $e) {
            logError("Error inesperado en MantenimientoController::cambiarEstado: " . $e->getMessage(), __FILE__, __LINE__);
            $response['message'] = "Ocurrió un error inesperado al cambiar estado: " . $e->getMessage();
        }
        return $response;
    }

    /**
     * Cancela un mantenimiento programado.
     * Delega la lógica al modelo.
     * @param int $idPlan ID del plan de mantenimiento.
     * @param string $motivo Motivo de la cancelación.
     * @param string $canceladoPor Usuario que realiza la cancelación.
     * @return array Resultado de la operación (success, message).
     */
    public function cancelarMantenimiento($idPlan, $motivo, $canceladoPor)
    {
        $response = ['success' => false, 'message' => ''];
        try {
            $resultado = $this->mantenimientoModel->cancelarMantenimiento($idPlan, $motivo, $canceladoPor);
            $response['success'] = $resultado['success'];
            $response['message'] = $resultado['message'];
        } catch (MaintenanceException $e) {
            logError("Error en MantenimientoController::cancelarMantenimiento: " . $e->getMessage(), __FILE__, __LINE__);
            $response['message'] = "Error al cancelar mantenimiento: " . $e->getMessage();
        } catch (Exception $e) {
            logError("Error inesperado en MantenimientoController::cancelarMantenimiento: " . $e->getMessage(), __FILE__, __LINE__);
            $response['message'] = "Ocurrió un error inesperado al cancelar mantenimiento: " . $e->getMessage();
        }
        return $response;
    }

    /**
     * Muestra el formulario para registrar la ejecución de un mantenimiento.
     * Se invoca mediante una solicitud GET.
     * @param array $getData Datos GET de la solicitud.
     */
    public function mostrarRegistroEjecucion($getData)
    {
        $idPlan = cleanInput($getData['id'] ?? null);
        $mantenimiento = null;
        $actividades = [];

        try {
            if (!$idPlan) {
                throw new Exception("ID de plan de mantenimiento no proporcionado para registrar ejecución.");
            }
            $mantenimiento = $this->mantenimientoModel->obtenerMantenimientoPorId($idPlan);
            if (!$mantenimiento) {
                throw new Exception("Mantenimiento con ID {$idPlan} no encontrado para registrar ejecución.");
            }
            // Obtener actividades filtradas por el tipo de mantenimiento del equipo
            // Asumiendo que $mantenimiento['IdTipoManto'] contiene el ID del tipo de mantenimiento
            $actividades = $this->mantenimientoModel->obtenerActividadesPorTipo($mantenimiento['IdTipoManto'] ?? null);
            // Si necesitas pasar el objeto TecnicoModel o EquipoModel a la vista, hazlo aquí.

            require_once __DIR__ . '/../views/mantenimiento/registro-ejecucion.php';

        } catch (MaintenanceException $e) {
            logError("Error en MantenimientoController::mostrarRegistroEjecucion: " . $e->getMessage(), __FILE__, __LINE__);
            global $error_global;
            $error_global .= "Error al cargar formulario de registro de ejecución: " . $e->getMessage();
            require_once __DIR__ . '/../views/mantenimiento/registro-ejecucion.php';
        } catch (Exception $e) {
            logError("Error inesperado en MantenimientoController::mostrarRegistroEjecucion: " . $e->getMessage(), __FILE__, __LINE__);
            global $error_global;
            $error_global .= "Ocurrió un error inesperado al cargar el formulario de registro: " . $e->getMessage();
            require_once __DIR__ . '/../views/mantenimiento/registro-ejecucion.php';
        }
    }

    /**
     * Registra la ejecución de un mantenimiento en el historial.
     * Se invoca mediante una solicitud POST desde el formulario de registro.
     * @param array $postData Datos POST de la solicitud.
     * @return array Resultado de la operación (success, message).
     */
    public function registrarEjecucion($postData)
    {
        $response = ['success' => false, 'message' => ''];
        try {
            $datos = [
                'IdPlan'                 => cleanInput($postData['IdPlan']),
                'FechaInicio'            => cleanInput($postData['FechaInicio'] ?? date('Y-m-d H:i:s')),
                'FechaFin'               => cleanInput($postData['FechaFin'] ?? date('Y-m-d H:i:s')),
                'TecnicoEjecutor'        => $_SESSION['user_id'] ?? 'sistema', // Usar el ID de usuario autenticado
                'TiempoEjecutado'        => cleanInput($postData['TiempoEjecutado'] ?? null),
                'ResultadoGeneral'       => cleanInput($postData['ResultadoGeneral'] ?? null),
                'ObservacionesEjecucion' => cleanInput($postData['ObservacionesEjecucion'] ?? null),
                'ProximoMantenimiento'   => cleanInput($postData['ProximoMantenimiento'] ?? null),
                'RequiereRepuestos'      => cleanInput($postData['RequiereRepuestos'] ?? 0),
                'CostoManoObra'          => cleanInput($postData['CostoManoObra'] ?? 0.00),
                'CostoRepuestos'         => cleanInput($postData['CostoRepuestos'] ?? 0.00),
                // Asumiendo que 'Photos' se manejaría de forma diferente, por ahora no se incluye directamente en cleanInput
                // 'Photos' => $_POST['Photos'] ?? null,
            ];

            $resultado = $this->mantenimientoModel->recordExecution($datos);
            $response['success'] = $resultado['success'];
            $response['message'] = $resultado['message'];

        } catch (MaintenanceException $e) {
            logError("Error en MantenimientoController::registrarEjecucion: " . $e->getMessage(), __FILE__, __LINE__);
            $response['message'] = "Error al registrar ejecución: " . $e->getMessage();
        } catch (Exception $e) {
            logError("Error inesperado en MantenimientoController::registrarEjecucion: " . $e->getMessage(), __FILE__, __LINE__);
            $response['message'] = "Ocurrió un error inesperado al registrar ejecución: " . $e->getMessage();
        }
        return $response;
    }
}