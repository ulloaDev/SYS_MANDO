<?php
    // reports.php - Generador de reportes para el sistema

    require_once 'MaintenanceSystem.php';

    class ReportGenerator
    {
        private $maintenance;

        public function __construct()
        {
            $this->maintenance = new MaintenanceSystem();
        }

        public function generatePDFReport($year, $clubId)
        {
            // Requiere librería TCPDF o similar
            // Este es un ejemplo básico de estructura HTML para PDF

            $report     = $this->maintenance->generateReport($year, $clubId);
            $club       = $report['club'];
            $schedule   = $report['schedule'];
            $statistics = $report['statistics'];

            ob_start();
        ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reporte de Mantenimiento -                                      <?php echo htmlspecialchars($club['name']); ?><?php echo $year; ?></title>
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
    }

    .proceso {
        background-color: #3b82f6;
        color: white;
        padding: 2px 4px;
        border-radius: 3px;
        font-size: 9px;
    }

    .completado {
        background-color: #10b981;
        color: white;
        padding: 2px 4px;
        border-radius: 3px;
        font-size: 9px;
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
        <h2><?php echo htmlspecialchars($club['name']); ?> -<?php echo $year; ?></h2>
        <p>Generado el:                        <?php echo date('d/m/Y H:i:s'); ?></p>
    </div>
    <div class="info">
        <p><strong>Objetivo:</strong> Mantener la eficiencia y durabilidad de los equipos del Club con un mantenimiento
            preventivo de manera trimestral.</p>
        <p><strong>Ubicación:</strong>                                        <?php echo htmlspecialchars($club['location']); ?></p>
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
                <th>Colaborador</th>
                <th>Marzo</th>
                <th>Junio</th>
                <th>Septiembre</th>
                <th>Diciembre</th>
            </tr>
        </thead>
        <tbody>                <?php foreach ($schedule as $techSchedule): ?> <tr>
                <td style="text-align: left;">
                    <strong><?php echo htmlspecialchars($techSchedule['technician']['name']); ?></strong><br>
                    <small><?php echo htmlspecialchars($techSchedule['technician']['code']); ?></small>
                </td>                      <?php
                          $quarters = [2, 5, 8, 11]; // Marzo, Junio, Septiembre, Diciembre
                                  foreach ($quarters as $month):
                                      $monthData      = $techSchedule['schedule'][$month] ?? [];
                                      $scheduledCount = 0;
                                      $statusSummary  = [];

                                      foreach ($monthData as $week => $data) {
                                          if (isset($data['scheduled']) && $data['scheduled']) {
                                              $scheduledCount++;
                                              $status                 = $data['status'] ?? 'Sin estado';
                                              $statusSummary[$status] = ($statusSummary[$status] ?? 0) + 1;
                                          }
                                  }
                                  ?> <td>
	                    <div style="margin-bottom: 5px;"><strong><?php echo $scheduledCount; ?></strong> programados</div>
	                    <?php foreach ($statusSummary as $status => $count): ?> <div
	                        class="<?php echo strtolower(str_replace([' ', '_'], '-', $status)); ?>"><?php echo $count; ?>
<?php echo htmlspecialchars($status); ?> </div><?php endforeach; ?>
                </td><?php endforeach; ?>
            </tr><?php endforeach; ?> </tbody>
    </table>
    <div class="conclusions">
        <h3>CONCLUSIONES Y RECOMENDACIONES</h3>
        <p>El cronograma de rutinas está programado para mantener la eficiencia operativa. Los mantenimientos requieren
            un nivel de dedicación especial en los equipos críticos. La duración total estimada considera todos los
            equipos implementados en el Club.</p>
        <p>Gracias al apoyo de gerentes, supervisores y colaboradores, se ha logrado mantener el correcto estado de las
            terminales y equipos de uso común.</p>
    </div>
    <div class="footer">
        <p>Documento generado automáticamente por el Sistema de Mantenimiento</p>
        <p>Para consultas contactar al departamento de IT</p>
    </div>
</body>

</html>        <?php
            $html = ob_get_clean();
                    return $html;
                }

                public function generateExcelReport($year, $clubId)
                {
                    // Para generar Excel, se puede usar PHPSpreadsheet
                    $report = $this->maintenance->generateReport($year, $clubId);

                    // Crear array de datos para Excel
                    $data   = [];
                    $data[] = ['REPORTE DE MANTENIMIENTO - ' . $report['club']['name'] . ' - ' . $year];
                    $data[] = []; // Línea vacía

                    // Información del club
                    $data[] = ['INFORMACIÓN DEL CLUB'];
                    $data[] = ['Nombre', $report['club']['name']];
                    $data[] = ['Ubicación', $report['club']['location']];
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
                    $headers  = ['Colaborador', 'Código'];
                    $quarters = ['Marzo', 'Junio', 'Septiembre', 'Diciembre'];
                    $weeks    = ['S1', 'S2', 'S3', 'S4', 'S5'];

                    foreach ($quarters as $quarter) {
                        foreach ($weeks as $week) {
                            $headers[] = $quarter . ' ' . $week;
                        }
                    }
                    $data[] = $headers;

                    // Datos de los técnicos
                    foreach ($report['schedule'] as $techSchedule) {
                        $row = [
                            $techSchedule['technician']['name'],
                            $techSchedule['technician']['code'],
                        ];

                        $monthNumbers = [2, 5, 8, 11]; // Marzo, Junio, Septiembre, Diciembre

                        foreach ($monthNumbers as $month) {
                            foreach ($weeks as $week) {
                                $slot = $techSchedule['schedule'][$month][$week] ?? null;
                                if ($slot && isset($slot['scheduled']) && $slot['scheduled']) {
                                    $status    = $slot['status'] ?? 'Sin estado';
                                    $equipment = $slot['equipment'] ?? 'N/A';
                                    $row[]     = $status . ' - ' . $equipment;
                                } else {
                                    $row[] = '';
                                }
                            }
                        }

                        $data[] = $row;
                    }

                    return $data;
                }

                public function downloadReport($type, $year, $clubId)
                {
                    try {
                        switch (strtolower($type)) {
                            case 'pdf':
                                $html = $this->generatePDFReport($year, $clubId);

                                // Headers para PDF
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
                            'error'   => true,
                            'message' => 'Error generando el reporte: ' . $e->getMessage(),
                        ]);
                    }
                }

                public function getAvailableReports()
                {
                    return [
                        'pdf'   => 'Reporte PDF/HTML',
                        'excel' => 'Archivo Excel/CSV',
                        'csv'   => 'Archivo CSV',
                    ];
                }

                public function validateReportParameters($year, $clubId)
                {
                    $errors = [];

                    if (! is_numeric($year) || $year < 2020 || $year > date('Y') + 1) {
                        $errors[] = 'Año no válido';
                    }

                    if (! is_numeric($clubId) || $clubId <= 0) {
                        $errors[] = 'ID de club no válido';
                    }

                    return $errors;
                }

                public function generateReportPreview($year, $clubId)
                {
                    $report = $this->maintenance->generateReport($year, $clubId);

                    return [
                        'club_name'        => $report['club']['name'],
                        'club_location'    => $report['club']['location'],
                        'year'             => $year,
                        'statistics'       => $report['statistics'],
                        'technician_count' => count($report['schedule']),
                        'generation_date'  => date('d/m/Y H:i:s'),
                    ];
                }
            }

            // Ejemplo de uso
            /*
$reportGenerator = new ReportGenerator();

// Generar y descargar reporte PDF
$reportGenerator->downloadReport('pdf', 2024, 1);

// Obtener vista previa
$preview = $reportGenerator->generateReportPreview(2024, 1);
echo json_encode($preview);

// Validar parámetros
$errors = $reportGenerator->validateReportParameters(2024, 1);
if (!empty($errors)) {
    echo "Errores: " . implode(', ', $errors);
}
*/
        ?>