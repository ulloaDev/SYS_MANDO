<?php
// helpers/statusHelper.php

function getMaintenanceStatusStyles(array $slot): array {
    $status = $slot['status'] ?? 'Programado';
    $priority = $slot['priority'] ?? 'Media';
    $equipment = $slot['equipment_type_name'] ?? 'Equipo';

    // Definir estilos base
    $styles = [
        'class' => 'bg-gray-100 hover:bg-gray-200',
        'icon' => '',
        'priority_icon' => '',
        'tooltip' => 'Click para programar'
    ];

    if (!empty($slot)) {
        // Mapeo de estatus
        $statusMap = [
            'Completado' => [
                'class' => 'bg-green-500 text-white',
                'icon' => 'fas fa-check-circle'
            ],
            'En Proceso' => [
                'class' => 'bg-blue-500 text-white',
                'icon' => 'fas fa-clock'
            ],
            'Programado' => [
                'class' => 'bg-orange-500 text-white',
                'icon' => 'fas fa-tools'
            ],
            'Reprogramado' => [
                'class' => 'bg-orange-500 text-white',
                'icon' => 'fas fa-tools'
            ]
        ];

        $styles['class'] = $statusMap[$status]['class'] ?? $statusMap['Programado']['class'];
        $styles['icon'] = $statusMap[$status]['icon'] ?? $statusMap['Programado']['icon'];
        
        // Prioridad
        if (in_array($priority, ['Alta', 'Crítica'])) {
            $styles['priority_icon'] = 'fas fa-exclamation-circle text-red-300';
        }
        
        // Tooltip
        $styles['tooltip'] = htmlspecialchars("$equipment - Estado: $status - Prioridad: $priority");
    }

    return $styles;
}