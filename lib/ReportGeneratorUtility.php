<?php
// lib/ReportGeneratorUtility.php

class ReportGeneratorUtility {
    private $mantenimientoModel;

/**
     * El constructor ahora recibe la instancia de MantenimientoModel como una dependencia.
     *
     * @param MantenimientoModel $mantenimientoModel
     */
    public function __construct(MantenimientoModel $mantenimientoModel)     {
        $this->mantenimientoModel = $mantenimientoModel;
    }


    /**
     * Obtiene todos los datos necesarios para el reporte de mantenimiento.
     * Esta lógica fue movida directamente aquí desde el anterior MaintenanceSystem.
     *
     * @param int $year El año del reporte.
     * @param int $clubId El ID del club.
     * @return array Un array que contiene el club, el horario y las estadísticas.
     * @throws Exception Si el club no se encuentra.
     */
    private function getReportData($year, $clubId) {
        // 1. Obtener la información del club
        $club = $this->mantenimientoModel->obtenerClubPorId($clubId);
        if (!$club) {
            throw new Exception("No se encontró el club con ID: " . $clubId);
        }

        // 2. Obtener la programación de mantenimiento
            $programacion = $this->mantenimientoModel->obtenerProgramacionTrimestral($year, $clubId);
        
        // 3. Obtener las estadísticas
        $estadisticas = $this->mantenimientoModel->obtenerEstadisticas($year, $clubId);

        // 4. Organizar la programación por técnico, mes y semana
        $scheduleByTechnician = [];
        // Obtener todos los técnicos relevantes para el club y año, incluyendo equipos asociados
        $relevantTechniciansData = $this->mantenimientoModel->obtenerTecnicosConUsuario($clubId, $year);

        // Inicializar la estructura para cada técnico relevante
        foreach ($relevantTechniciansData as $techData) {
            $techId = $techData['id'];
            // Usar la combinación de técnico y equipo para una fila única en el reporte
            $uniqueTechEquipoKey = $techId . '-' . ($techData['equipo_id'] ?? 'null');

            if (!isset($scheduleByTechnician[$uniqueTechEquipoKey])) {
                $scheduleByTechnician[$uniqueTechEquipoKey] = [
                    'technician' => [
                        'id' => $techId,
                        'name' => $techData['technician_name'],
                        'code' => $techData['usuario'] ?? 'N/A' // Usar 'usuario' o 'N/A'
                    ],
                    'equipo' => [
                        'id' => $techData['equipo_id'],
                        'nombre' => $techData['equipo_nombre'] ?? 'N/A',
                        'tipo_nombre' => $techData['equipment_type_name'] ?? 'N/A'
                    ],
                    'schedule' => []
                ];
            }
        }

        // Llenar el horario con los datos de programación
        foreach ($programacion as $item) {
            $techId = $item['technician_id'];
            $equipoId = $item['equipo_id'];
            $month = (int)$item['scheduled_month'];
            $week = (int)$item['scheduled_week'];
            $uniqueTechEquipoKey = $techId . '-' . $equipoId;

            // Asegurarse de que la entrada del técnico/equipo exista
            if (!isset($scheduleByTechnician[$uniqueTechEquipoKey])) {
                // Esto podría ocurrir si la programación existe para un técnico/equipo
                // que no fue capturado por obtenerTecnicosConUsuario (ej. si no tiene usuario AD)
                // En este caso, lo inicializamos con la información disponible.
                $scheduleByTechnician[$uniqueTechEquipoKey] = [
                    'technician' => [
                        'id' => $techId,
                        'name' => $item['technician_name'] ?? 'Técnico Desconocido',
                        'code' => 'N/A'
                    ],
                    'equipo' => [
                        'id' => $equipoId,
                        'nombre' => $item['equipo_nombre'] ?? 'Equipo Desconocido',
                        'tipo_nombre' => $item['equipment_type'] ?? 'N/A'
                    ],
                    'schedule' => []
                ];
            }

            if (!isset($scheduleByTechnician[$uniqueTechEquipoKey]['schedule'][$month])) {
                $scheduleByTechnician[$uniqueTechEquipoKey]['schedule'][$month] = [];
            }
            // Almacenar el elemento completo, incluyendo estado, prioridad, etc.
            $scheduleByTechnician[$uniqueTechEquipoKey]['schedule'][$month][$week] = $item;
            $scheduleByTechnician[$uniqueTechEquipoKey]['schedule'][$month][$week]['scheduled'] = true; // Marcar como programado
            }

            // Ordenar técnicos por nombre para una salida de reporte consistente
        usort($scheduleByTechnician, function($a, $b) {
            $cmpTech = strcmp($a['technician']['name'], $b['technician']['name']);
            if ($cmpTech !== 0) {
                return $cmpTech;
            }
            // Si los técnicos son los mismos, ordenar por nombre de equipo
            return strcmp($a['equipo']['nombre'], $b['equipo']['nombre']);
        });

            return [
                'club' => $club,
            'schedule' => $scheduleByTechnician,
            'statistics' => $estadisticas
        ];
    }

    /**
     * Genera un reporte HTML para ser usado como contenido PDF.
     * @param int $year El año del reporte.
     * @param int $clubId El ID del club.
     * @return string El contenido HTML del reporte.
     */
    public function generatePDFReport($year, $clubId) {
        $report = $this->getReportData($year, $clubId);
        $club = $report['club'];
        $schedule = $report['schedule'];
        $statistics = $report['statistics'];

        ob_start();
        ?>
<!DOCTYPE html>
<html>

<head>
 <meta charset="UTF-8">
 <title>Reporte de Mantenimiento - <?php echo htmlspecialchars($club['name']); ?> <?php echo $year; ?></title>
 <style>
 body {
  font-family: Arial, sans-serif;
  font-size: 12px;
  margin: 0;
  padding: 0;
 }

 .header {
  background-color: #1e40af;
  color: white;
  padding: 20px;
  text-align: center;
 }

 .info {
  margin: 20px;
  line-height: 1.6;
 }

 .statistics {
  display: flex;
  justify-content: space-around;
  margin: 20px;
  flex-wrap: wrap;
 }

 .stat-box {
  text-align: center;
  border: 1px solid #ccc;
  padding: 15px;
  margin: 5px;
  border-radius: 5px;
  min-width: 120px;
 }

 .stat-box h3 {
  margin: 0;
  font-size: 24px;
  color: #1e40af;
 }

 table {
  width: 100%;
  border-collapse: collapse;
  margin: 20px 0;
  font-size: 10px;
 }

 th,
 td {
  border: 1px solid #ccc;
  padding: 8px;
  text-align: center;
 }

 th {
  background-color: #f0f0f0;
  font-weight: bold;
 }

 .programado {
  background-color: #f97316;
  color: white;
  padding: 2px 4px;
  border-radius: 3px;
  font-size: 9px;
  display: inline-block;
 }

 .en-proceso {
  background-color: #3b82f6;
  color: white;
  padding: 2px 4px;
  border-radius: 3px;
  font-size: 9px;
  display: inline-block;
 }

 .completado {
  background-color: #10b981;
  color: white;
  padding: 2px 4px;
  border-radius: 3px;
  font-size: 9px;
  display: inline-block;
 }

 .reprogramado {
  background-color: #ffc107;
  /* Amarillo */
  color: black;
  padding: 2px 4px;
  border-radius: 3px;
  font-size: 9px;
  display: inline-block;
 }

 .cancelado {
  background-color: #dc3545;
  /* Rojo */
  color: white;
  padding: 2px 4px;
  border-radius: 3px;
  font-size: 9px;
  display: inline-block;
 }

 .conclusions {
  margin: 40px 20px 20px;
  padding: 20px;
  background-color: #f8f9fa;
  border-left: 4px solid #1e40af;
 }

 .footer {
  text-align: center;
  margin-top: 40px;
  padding: 20px;
  border-top: 1px solid #ccc;
  font-size: 10px;
  color: #666;
 }
 </style>
</head>

<body>
 <div class="header">
  <h1>REPORTE DE PROGRAMACIÓN DE MANTENIMIENTO</h1>
  <h2><?php echo htmlspecialchars($club['name']); ?> - <?php echo $year; ?></h2>
  <p>Generado el: <?php echo date('d/m/Y H:i:s'); ?></p>
 </div>
 <div class="info">
  <p><strong>Objetivo:</strong> Mantener la eficiencia y durabilidad de los equipos del Club con un mantenimiento
   preventivo de manera trimestral.</p>
  <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($club['location'] ?? 'N/A'); ?></p>
  <p><strong>Coordinado por:</strong> IT</p>
 </div>
 <div class="statistics">
  <div class="stat-box">
   <h3><?php echo $statistics['total_programados']; ?></h3>
   <p>Total Programados</p>
  </div>
  <div class="stat-box">
   <h3><?php echo $statistics['completados']; ?></h3>
   <p>Completados</p>
  </div>
  <div class="stat-box">
   <h3><?php echo $statistics['en_proceso']; ?></h3>
   <p>En Proceso</p>
  </div>
  <div class="stat-box">
   <h3><?php echo $statistics['alta_prioridad']; ?></h3>
   <p>Alta Prioridad</p>
  </div>
 </div>
 <table>
  <thead>
   <tr>
    <th>Colaborador / Equipo</th>
    <th>Marzo</th>
    <th>Junio</th>
    <th>Septiembre</th>
    <th>Diciembre</th>
   </tr>
  </thead>
  <tbody> <?php foreach ($schedule as $techEquipoSchedule): ?> <tr>
    <td style="text-align: left;">
     <strong><?php echo htmlspecialchars($techEquipoSchedule['technician']['name']); ?></strong><br>
     <small>Usuario: <?php echo htmlspecialchars($techEquipoSchedule['technician']['code']); ?></small><br>
     <small>Equipo: <?php echo htmlspecialchars($techEquipoSchedule['equipo']['nombre']); ?>
      (<?php echo htmlspecialchars($techEquipoSchedule['equipo']['tipo_nombre']); ?>)</small>
    </td> <?php
                $monthNumbers = [3, 6, 9, 12]; // Meses de los trimestres
                foreach ($monthNumbers as $month):
                    $monthData = $techEquipoSchedule['schedule'][$month] ?? [];
                    $scheduledCount = 0;
                    $statusSummary = []; // Para resumir estados como 'Completado: 2', 'Programado: 1'

                    foreach ($monthData as $week => $data) {
                        if (isset($data['status'])) {
                            $scheduledCount++;
                            $status = $data['status'];
                            // Agrupar por estado y contar
                            $statusSummary[$status] = ($statusSummary[$status] ?? 0) + 1;
                        }
                    }
                ?> <td> <?php if ($scheduledCount > 0): ?> <div style="margin-bottom: 5px;">
      <strong><?php echo $scheduledCount; ?></strong> programados
     </div> <?php foreach ($statusSummary as $status => $count): ?> <div
      class="<?php echo strtolower(str_replace([' ', '_'], '-', $status)); ?>"> <?php echo $count; ?>
      <?php echo htmlspecialchars($status); ?> </div> <?php endforeach; ?> <?php else: ?> Ninguno <?php endif; ?> </td>
    <?php endforeach; ?>
   </tr> <?php endforeach; ?> </tbody>
 </table>
 <div class="conclusions">
  <h3>CONCLUSIONES Y RECOMENDACIONES</h3>
  <p>El cronograma de rutinas está programado para mantener la eficiencia operativa. Los mantenimientos requieren un
   nivel de dedicación especial en los equipos críticos. La duración total estimada considera todos los equipos
   implementados en el Club.</p>
  <p>Gracias al apoyo de gerentes, supervisores y colaboradores, se ha logrado mantener el correcto estado de las
   terminales y equipos de uso común.</p>
 </div>
 <div class="footer">
  <p>Documento generado automáticamente por el Sistema de Mantenimiento</p>
  <p>Para consultas contactar al departamento de IT</p>
 </div>
</body>

</html> <?php
        $html = ob_get_clean();
        return $html;
    }

    /**
     * Genera datos para un reporte Excel/CSV.
     * @param int $year El año del reporte.
     * @param int $clubId El ID del club.
     * @return array Un array de arrays con los datos para Excel/CSV.
     */
    public function generateExcelReport($year, $clubId) {
        $report = $this->getReportData($year, $clubId);

        // Crear array de datos para Excel
        $data = [];
        $data[] = ['REPORTE DE MANTENIMIENTO - ' . $report['club']['name'] . ' - ' . $year];
        $data[] = []; // Línea vacía

        // Información del club
        $data[] = ['INFORMACIÓN DEL CLUB'];
        $data[] = ['Nombre', $report['club']['name']];
        $data[] = ['Ubicación', $report['club']['location'] ?? 'N/A'];
        $data[] = ['Año', $year];
        $data[] = ['Fecha de generación', date('d/m/Y H:i:s')];
        $data[] = []; // Línea vacía

        // Estadísticas
        $data[] = ['ESTADÍSTICAS'];
        $data[] = ['Métrica', 'Valor'];
        $data[] = ['Total Programados', $report['statistics']['total_programados']];
        $data[] = ['Completados', $report['statistics']['completados']];
        $data[] = ['En Proceso', $report['statistics']['en_proceso']];
        $data[] = ['Alta Prioridad', $report['statistics']['alta_prioridad']];
        $data[] = []; // Línea vacía

        // Encabezados de la tabla principal
        $headers = ['Colaborador', 'Usuario AD', 'Equipo', 'Tipo de Equipo'];
        $quarters = ['Marzo', 'Junio', 'Septiembre', 'Diciembre'];
        $weeks = ['S1', 'S2', 'S3', 'S4', 'S5']; // Usando S para Semana

        foreach ($quarters as $quarter) {
            foreach ($weeks as $week) {
                $headers[] = $quarter . ' ' . $week;
            }
        }
        $data[] = $headers;

        // Datos de los técnicos y equipos
        $monthNumbers = [3, 6, 9, 12]; // Marzo, Junio, Septiembre, Diciembre

        foreach ($report['schedule'] as $techEquipoSchedule) {
            $row = [
                $techEquipoSchedule['technician']['name'],
                $techEquipoSchedule['technician']['code'],
                $techEquipoSchedule['equipo']['nombre'],
                $techEquipoSchedule['equipo']['tipo_nombre'],
            ];

            foreach ($monthNumbers as $month) {
                foreach ($weeks as $weekIdx => $weekLabel) {
                    $weekNum = $weekIdx + 1; // Las semanas en la DB son 1-5
                    $slot = $techEquipoSchedule['schedule'][$month][$weekNum] ?? null;
                    if ($slot && isset($slot['status'])) {
                        $status = $slot['status'] ?? 'Sin estado';
                        $row[] = $status;
                    } else {
                        $row[] = '';
                    }
                }
            }
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Descarga un reporte.
     * @param string $type El tipo de reporte ('pdf', 'excel', 'csv').
     * @param int $year El año del reporte.
     * @param int $clubId El ID del club.
     */
    public function downloadReport($type, $year, $clubId) {
        try {
            switch (strtolower($type)) {
                case 'pdf':
                    $html = $this->generatePDFReport($year, $clubId);

                    // Headers para PDF (como HTML para renderizado en navegador, o usar una librería PDF)
                    header('Content-Type: text/html; charset=UTF-8');
                    header('Content-Disposition: attachment; filename="reporte_mantenimiento_' . $year . '_club_' . $clubId . '.html"');
                    header('Cache-Control: no-cache, must-revalidate');
                    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

                    echo $html;
                    break;

                case 'excel':
                case 'csv':
                    $data = $this->generateExcelReport($year, $clubId);

                    // Headers para CSV
                    header('Content-Type: text/csv; charset=UTF-8');
                    header('Content-Disposition: attachment; filename="reporte_mantenimiento_' . $year . '_club_' . $clubId . '.csv"');
                    header('Cache-Control: no-cache, must-revalidate');
                    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

                    // BOM para UTF-8
                    echo "\xEF\xBB\xBF";

                    $output = fopen('php://output', 'w');
                    foreach ($data as $row) {
                        fputcsv($output, $row, ';'); // Usar punto y coma para mejor compatibilidad con Excel
                    }
                    fclose($output);
                    break;

                default:
                    throw new InvalidArgumentException("Tipo de reporte no válido: $type");
            }
        } catch (Exception $e) {
            error_log("Error generando reporte: " . $e->getMessage());

            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'error' => true,
                'message' => 'Error generando el reporte: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Obtiene los tipos de reportes disponibles.
     * @return array Un array de reportes disponibles.
     */
    public function getAvailableReports() {
        return [
'pdf' => 'Reporte PDF/HTML',
'excel' => 'Archivo Excel/CSV',
            'csv' => 'Archivo CSV',
        ];
    }

    /**
     * Valida los parámetros del reporte.
     * @param int $year El año.
     * @param int $clubId El ID del club.
     * @return array Un array de errores, si los hay.
     */
    public function validateReportParameters($year, $clubId) {
        $errors = [];

        if (! is_numeric($year) || $year < 2020 || $year > date('Y') + 1) {
            $errors[] = 'Año no válido';
        }

        if (! is_numeric($clubId) || $clubId <= 0) {
            $errors[] = 'ID de club no válido';
        }

        return $errors;
    }

    /**
     * Genera una vista previa de los datos del reporte (JSON).
     * @param int $year El año de la vista previa.
     * @param int $clubId El ID del club de la vista previa.
     * @return array Un array con los datos de la vista previa.
     */
    public function generateReportPreview($year, $clubId) {
        $report = $this->getReportData($year, $clubId);

        return [
            'club_name' => $report['club']['name'],
            'club_location' => $report['club']['location'] ?? 'N/A',
            'year' => $year,
            'statistics' => $report['statistics'],
            'technician_equipment_pairs_count' => count($report['schedule']), // Cambiado para reflejar la agrupación
            'generation_date' => date('d/m/Y H:i:s'),
        ];
    }
}