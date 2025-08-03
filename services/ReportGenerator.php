<?php
// services/ReportGenerator.php

/**
 * Servicio para la generación de reportes en diferentes formatos (sin Composer)
 */
class ReportGenerator 
{
    private $tempDir;
    private $mantenimientoModel;

    public function __construct($mantenimientoModel = null) 
    {
        $this->tempDir = sys_get_temp_dir();
        $this->mantenimientoModel = $mantenimientoModel;
    }

    /**
     * Genera un reporte según los parámetros especificados
     */
    public function generate(array $params): ?string 
    {
        try {
            $type = $params['type'] ?? '';
            $format = $params['format'] ?? 'html';
            $data = $params['data'] ?? [];
            $year = $params['year'] ?? date('Y');
            $clubId = $params['club_id'] ?? null;

            // Registrar la generación del reporte
            if ($this->mantenimientoModel) {
                $this->mantenimientoModel->registrarReporteGenerado($type, $year, $clubId);
            }

            switch ($format) {
                case 'html':
                    return $this->generateHTML($type, $data, $params);
                case 'csv':
                    return $this->generateCSV($type, $data, $params);
                case 'json':
                    return $this->generateJSON($type, $data, $params);
                case 'xml':
                    return $this->generateXML($type, $data, $params);
                default:
                    throw new Exception("Formato no soportado: {$format}");
            }
        } catch (Exception $e) {
            error_log("Error al generar reporte: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Genera reporte en formato HTML
     */
    private function generateHTML(string $type, array $data, array $params): string 
    {
        $filename = $this->tempDir . '/reporte_' . $type . '_' . uniqid() . '.html';
        $html = $this->generateHTMLContent($type, $data, $params);
        file_put_contents($filename, $html);
        return $filename;
    }

    /**
     * Genera contenido HTML completo
     */
    private function generateHTMLContent(string $type, array $data, array $params): string 
    {
        $year = $params['year'] ?? date('Y');
        $clubId = $params['club_id'] ?? null;
        $title = $this->getReportTitle($type, $params);

        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . $title . '</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px;
            font-size: 14px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #3498db;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        h1 { 
            color: #2c3e50; 
            margin: 0;
            font-size: 28px;
        }
        h2 { 
            color: #34495e; 
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-top: 30px;
        }
        .summary { 
            background-color: #ecf0f1; 
            padding: 15px; 
            margin: 20px 0; 
            border-radius: 5px;
            border-left: 5px solid #3498db;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th { 
            background-color: #3498db; 
            color: white; 
            padding: 12px; 
            text-align: left;
            font-weight: bold;
        }
        td { 
            padding: 10px 12px; 
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #e8f4f8;
        }
        .total-row { 
            font-weight: bold; 
            background-color: #e8f5e8 !important;
            border-top: 2px solid #27ae60;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        .stat-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 10px;
            text-align: center;
            min-width: 150px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #3498db;
        }
        .stat-label {
            color: #7f8c8d;
            font-size: 12px;
            text-transform: uppercase;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-style: italic;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #7f8c8d;
            font-size: 12px;
        }
        @media print {
            body { margin: 0; }
            .header { page-break-after: avoid; }
            table { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>' . $title . '</h1>
    </div>
    
    <div class="summary">
        <strong>Año:</strong> ' . $year . '<br>
        <strong>Fecha de generación:</strong> ' . date('d/m/Y H:i:s') . '<br>';
        
        if ($clubId) {
            $html .= '<strong>Club:</strong> ' . $this->getClubName($clubId) . '<br>';
        }
        
        $html .= '</div>';

        // Agregar estadísticas si hay datos
        if (!empty($data)) {
            $html .= $this->generateStatsSection($type, $data);
        }

        // Contenido principal según el tipo
        switch ($type) {
            case 'maintenance':
                $html .= $this->generateMaintenanceHTML($data);
                break;
            case 'usage':
                $html .= $this->generateUsageHTML($data);
                break;
            case 'financial':
                $html .= $this->generateFinancialHTML($data);
                break;
            case 'equipment':
                $html .= $this->generateEquipmentHTML($data);
                break;
        }

        $html .= '
    <div class="footer">
        <p>Reporte generado por el Sistema de Gestión de Clubes</p>
        <p>© ' . date('Y') . ' - Todos los derechos reservados</p>
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * Genera sección de estadísticas
     */
    private function generateStatsSection(string $type, array $data): string 
    {
        if (empty($data)) return '';

        $html = '<div class="stats">';
        
        switch ($type) {
            case 'maintenance':
                $total = count($data);
                $costoTotal = array_sum(array_column($data, 'costo'));
                $promedio = $total > 0 ? $costoTotal / $total : 0;
                
                $html .= '<div class="stat-card">
                    <div class="stat-value">' . $total . '</div>
                    <div class="stat-label">Total Mantenimientos</div>
                </div>';
                $html .= '<div class="stat-card">
                    <div class="stat-value">$' . number_format($costoTotal, 2) . '</div>
                    <div class="stat-label">Costo Total</div>
                </div>';
                $html .= '<div class="stat-card">
                    <div class="stat-value">$' . number_format($promedio, 2) . '</div>
                    <div class="stat-label">Costo Promedio</div>
                </div>';
                break;
                
            case 'usage':
                $total = count($data);
                $horasTotal = array_sum(array_column($data, 'horas_uso'));
                $usuariosTotal = array_sum(array_column($data, 'numero_usuarios'));
                
                $html .= '<div class="stat-card">
                    <div class="stat-value">' . $total . '</div>
                    <div class="stat-label">Total Registros</div>
                </div>';
                $html .= '<div class="stat-card">
                    <div class="stat-value">' . $horasTotal . '</div>
                    <div class="stat-label">Horas Totales</div>
                </div>';
                $html .= '<div class="stat-card">
                    <div class="stat-value">' . $usuariosTotal . '</div>
                    <div class="stat-label">Total Usuarios</div>
                </div>';
                break;
        }
        
        $html .= '</div>';
        return $html;
    }

    /**
     * Genera HTML para reporte de mantenimiento
     */
    private function generateMaintenanceHTML(array $data): string 
    {
        if (empty($data)) {
            return '<div class="no-data">No hay datos de mantenimiento para mostrar.</div>';
        }

        $html = '<h2>Detalle de Mantenimientos</h2>';
        $html .= '<table>';
        $html .= '<thead><tr><th>Fecha</th><th>Club</th><th>Tipo</th><th>Descripción</th><th>Costo</th><th>Proveedor</th></tr></thead>';
        $html .= '<tbody>';

        $totalCosto = 0;
        foreach ($data as $item) {
            $html .= '<tr>';
            $html .= '<td>' . date('d/m/Y', strtotime($item['fecha'])) . '</td>';
            $html .= '<td>' . htmlspecialchars($item['club_nombre']) . '</td>';
            $html .= '<td>' . htmlspecialchars($item['tipo_nombre']) . '</td>';
            $html .= '<td>' . htmlspecialchars($item['descripcion']) . '</td>';
            $html .= '<td>$' . number_format($item['costo'], 2) . '</td>';
            $html .= '<td>' . htmlspecialchars($item['proveedor'] ?? '') . '</td>';
            $html .= '</tr>';
            $totalCosto += $item['costo'];
        }

        $html .= '<tr class="total-row">';
        $html .= '<td colspan="4"><strong>Total General</strong></td>';
        $html .= '<td><strong>$' . number_format($totalCosto, 2) . '</strong></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        $html .= '</tbody></table>';

        return $html;
    }

    /**
     * Genera HTML para reporte de uso
     */
    private function generateUsageHTML(array $data): string 
    {
        if (empty($data)) {
            return '<div class="no-data">No hay datos de uso para mostrar.</div>';
        }

        $html = '<h2>Uso de Instalaciones</h2>';
        $html .= '<table>';
        $html .= '<thead><tr><th>Fecha</th><th>Club</th><th>Instalación</th><th>Horas de Uso</th><th>Usuarios</th></tr></thead>';
        $html .= '<tbody>';

        $totalHoras = 0;
        $totalUsuarios = 0;
        foreach ($data as $item) {
            $html .= '<tr>';
            $html .= '<td>' . date('d/m/Y', strtotime($item['fecha'])) . '</td>';
            $html .= '<td>' . htmlspecialchars($item['club_nombre']) . '</td>';
            $html .= '<td>' . htmlspecialchars($item['instalacion_nombre']) . '</td>';
            $html .= '<td>' . $item['horas_uso'] . '</td>';
            $html .= '<td>' . $item['numero_usuarios'] . '</td>';
            $html .= '</tr>';
            $totalHoras += $item['horas_uso'];
            $totalUsuarios += $item['numero_usuarios'];
        }

        $html .= '<tr class="total-row">';
        $html .= '<td colspan="3"><strong>Totales</strong></td>';
        $html .= '<td><strong>' . $totalHoras . ' hrs</strong></td>';
        $html .= '<td><strong>' . $totalUsuarios . '</strong></td>';
        $html .= '</tr>';
        $html .= '</tbody></table>';

        return $html;
    }

    /**
     * Genera HTML para reporte financiero
     */
    private function generateFinancialHTML(array $data): string 
    {
        if (empty($data)) {
            return '<div class="no-data">No hay datos financieros para mostrar.</div>';
        }

        $html = '<h2>Resumen Financiero por Club</h2>';
        $html .= '<table>';
        $html .= '<thead><tr><th>Club</th><th>Total Mantenimiento</th><th>Preventivo</th><th>Correctivo</th><th>Mejoras</th><th>Trabajos</th></tr></thead>';
        $html .= '<tbody>';

        $granTotal = 0;
        foreach ($data as $item) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($item['club_nombre']) . '</td>';
            $html .= '<td>$' . number_format($item['total_mantenimiento'], 2) . '</td>';
            $html .= '<td>$' . number_format($item['costo_preventivo'], 2) . '</td>';
            $html .= '<td>$' . number_format($item['costo_correctivo'], 2) . '</td>';
            $html .= '<td>$' . number_format($item['costo_mejoras'], 2) . '</td>';
            $html .= '<td>' . $item['total_trabajos'] . '</td>';
            $html .= '</tr>';
            $granTotal += $item['total_mantenimiento'];
        }

        $html .= '<tr class="total-row">';
        $html .= '<td><strong>Gran Total</strong></td>';
        $html .= '<td><strong>$' . number_format($granTotal, 2) . '</strong></td>';
        $html .= '<td colspan="4"></td>';
        $html .= '</tr>';
        $html .= '</tbody></table>';

        return $html;
    }

    /**
     * Genera HTML para reporte de equipamiento
     */
    private function generateEquipmentHTML(array $data): string 
    {
        if (empty($data)) {
            return '<div class="no-data">No hay datos de equipamiento para mostrar.</div>';
        }

        $html = '<h2>Estado del Equipamiento</h2>';
        $html .= '<table>';
        $html .= '<thead><tr><th>Club</th><th>Equipo</th><th>Tipo</th><th>Estado</th><th>Mantenimientos</th><th>Costo Total</th><th>Último Mantenimiento</th></tr></thead>';
        $html .= '<tbody>';

        foreach ($data as $item) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($item['club_nombre']) . '</td>';
            $html .= '<td>' . htmlspecialchars($item['nombre']) . '</td>';
            $html .= '<td>' . htmlspecialchars($item['tipo_equipo']) . '</td>';
            $html .= '<td><span class="status-' . strtolower($item['estado']) . '">' . htmlspecialchars($item['estado']) . '</span></td>';
            $html .= '<td>' . ($item['mantenimientos_realizados'] ?? 0) . '</td>';
            $html .= '<td>$' . number_format($item['costo_total_mantenimiento'] ?? 0, 2) . '</td>';
            $html .= '<td>' . ($item['ultimo_mantenimiento'] ? date('d/m/Y', strtotime($item['ultimo_mantenimiento'])) : 'N/A') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        return $html;
    }

    /**
     * Genera reporte en formato CSV
     */
    private function generateCSV(string $type, array $data, array $params): string 
    {
        $filename = $this->tempDir . '/reporte_' . $type . '_' . uniqid() . '.csv';
        $file = fopen($filename, 'w');

        // Escribir BOM para UTF-8
        fwrite($file, "\xEF\xBB\xBF");

        // Escribir encabezados y datos según el tipo
        switch ($type) {
            case 'maintenance':
                $this->writeMaintenanceCSV($file, $data);
                break;
            case 'usage':
                $this->writeUsageCSV($file, $data);
                break;
            case 'financial':
                $this->writeFinancialCSV($file, $data);
                break;
            case 'equipment':
                $this->writeEquipmentCSV($file, $data);
                break;
        }

        fclose($file);
        return $filename;
    }

    /**
     * Genera reporte en formato JSON
     */
    private function generateJSON(string $type, array $data, array $params): string 
    {
        $filename = $this->tempDir . '/reporte_' . $type . '_' . uniqid() . '.json';
        
        $reportData = [
            'metadata' => [
                'type' => $type,
                'title' => $this->getReportTitle($type, $params),
                'year' => $params['year'] ?? date('Y'),
                'club_id' => $params['club_id'] ?? null,
                'generated_at' => date('Y-m-d H:i:s'),
                'total_records' => count($data)
            ],
            'data' => $data
        ];

        file_put_contents($filename, json_encode($reportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $filename;
    }

    /**
     * Genera reporte en formato XML
     */
    private function generateXML(string $type, array $data, array $params): string 
    {
        $filename = $this->tempDir . '/reporte_' . $type . '_' . uniqid() . '.xml';
        
        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;
        
        $root = $xml->createElement('reporte');
        $xml->appendChild($root);
        
        // Metadata
        $metadata = $xml->createElement('metadata');
        $metadata->appendChild($xml->createElement('tipo', $type));
        $metadata->appendChild($xml->createElement('titulo', $this->getReportTitle($type, $params)));
        $metadata->appendChild($xml->createElement('año', $params['year'] ?? date('Y')));
        $metadata->appendChild($xml->createElement('fecha_generacion', date('Y-m-d H:i:s')));
        $metadata->appendChild($xml->createElement('total_registros', count($data)));
        $root->appendChild($metadata);
        
        // Data
        $dataElement = $xml->createElement('datos');
        foreach ($data as $item) {
            $registro = $xml->createElement('registro');
            foreach ($item as $key => $value) {
                $registro->appendChild($xml->createElement($key, htmlspecialchars($value ?? '')));
            }
            $dataElement->appendChild($registro);
        }
        $root->appendChild($dataElement);
        
        $xml->save($filename);
        return $filename;
    }

    /**
     * Métodos para escribir CSV (reutilizados del código anterior)
     */
    private function writeMaintenanceCSV($file, array $data): void 
    {
        fputcsv($file, ['Fecha', 'Club', 'Tipo', 'Descripción', 'Costo', 'Proveedor']);
        foreach ($data as $item) {
            fputcsv($file, [
                date('d/m/Y', strtotime($item['fecha'])),
                $item['club_nombre'],
                $item['tipo_nombre'],
                $item['descripcion'],
                $item['costo'],
                $item['proveedor'] ?? ''
            ]);
        }
    }

    private function writeUsageCSV($file, array $data): void 
    {
        fputcsv($file, ['Fecha', 'Club', 'Instalación', 'Horas de Uso', 'Número de Usuarios']);
        foreach ($data as $item) {
            fputcsv($file, [
                date('d/m/Y', strtotime($item['fecha'])),
                $item['club_nombre'],
                $item['instalacion_nombre'],
                $item['horas_uso'],
                $item['numero_usuarios']
            ]);
        }
    }

    private function writeFinancialCSV($file, array $data): void 
    {
        fputcsv($file, ['Club', 'Total Mantenimiento', 'Costo Preventivo', 'Costo Correctivo', 'Costo Mejoras', 'Total Trabajos']);
        foreach ($data as $item) {
            fputcsv($file, [
                $item['club_nombre'],
                $item['total_mantenimiento'],
                $item['costo_preventivo'],
                $item['costo_correctivo'],
                $item['costo_mejoras'],
                $item['total_trabajos']
            ]);
        }
    }

    private function writeEquipmentCSV($file, array $data): void 
    {
        fputcsv($file, ['Club', 'Equipo', 'Tipo', 'Estado', 'Mantenimientos', 'Costo Total', 'Último Mantenimiento']);
        foreach ($data as $item) {
            fputcsv($file, [
                $item['club_nombre'],
                $item['nombre'],
                $item['tipo_equipo'],
                $item['estado'],
                $item['mantenimientos_realizados'] ?? 0,
                $item['costo_total_mantenimiento'] ?? 0,
                $item['ultimo_mantenimiento'] ? date('d/m/Y', strtotime($item['ultimo_mantenimiento'])) : 'N/A'
            ]);
        }
    }

    /**
     * Obtiene el título del reporte
     */
    private function getReportTitle(string $type, array $params): string 
    {
        $year = $params['year'] ?? date('Y');
        $titles = [
            'maintenance' => 'Reporte de Mantenimiento',
            'usage' => 'Reporte de Uso de Instalaciones',
            'financial' => 'Reporte Financiero',
            'equipment' => 'Reporte de Equipamiento'
        ];

        $title = $titles[$type] ?? 'Reporte';
        return $title . ' - Año ' . $year;
    }

    /**
     * Obtiene el nombre del club por ID
     */
    private function getClubName(int $clubId): string 
    {
        if (!$this->mantenimientoModel) {
            return 'Club ID: ' . $clubId;
        }

        try {
            $clubes = $this->mantenimientoModel->obtenerClubes();
            foreach ($clubes as $club) {
                if ($club['id'] == $clubId) {
                    return $club['nombre'];
                }
            }
        } catch (Exception $e) {
            error_log("Error al obtener nombre del club: " . $e->getMessage());
        }

        return 'Club ID: ' . $clubId;
    }
}
?>