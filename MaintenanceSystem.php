<?php
// MaintenanceSystem.php - Clase principal para manejo del sistema

require_once 'config.php';

class MaintenanceSystem
{
    private $conn;
    private $db;

    public function __construct()
    {
        $this->db   = new Database();
        $this->conn = $this->db->getConnection();
    }

    // Obtener todos los técnicos activos
    public function getTechnicians()
    {
        $query = "SELECT * FROM technicians WHERE active = 1 ORDER BY name";
        $stmt  = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener todos los clubes activos
    public function getClubs()
    {
        $query = "SELECT * FROM clubs WHERE active = 1 ORDER BY code";
        $stmt  = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener tipos de equipos
    public function getEquipmentTypes()
    {
        $query = "SELECT * FROM equipment_types WHERE active = 1 ORDER BY name";
        $stmt  = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener programación completa
    public function getMaintenanceSchedule($year, $club_id = null)
    {
        $query = "SELECT ms.*, t.name as technician_name, t.code as technician_code,
                         et.name as equipment_name, c.code as club_code, c.name as club_name
                  FROM maintenance_schedule ms
                  LEFT JOIN technicians t ON ms.technician_id = t.id
                  LEFT JOIN equipment_types et ON ms.equipment_type_id = et.id
                  LEFT JOIN clubs c ON ms.club_id = c.id
                  WHERE ms.year = ?";

        $params = [$year];

        if ($club_id) {
            $query .= " AND ms.club_id = ?";
            $params[] = $club_id;
        }

        $query .= " ORDER BY t.name, ms.month, ms.week";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        $results = $stmt->fetchAll();

        // Organizar datos por técnico, mes y semana
        $schedule = [];
        foreach ($results as $row) {
            $techId = $row['technician_id'];
            $month  = $row['month'];
            $week   = $row['week'];

            if (! isset($schedule[$techId])) {
                $schedule[$techId] = [
                    'technician' => [
                        'id'   => $row['technician_id'],
                        'name' => $row['technician_name'],
                        'code' => $row['technician_code'],
                    ],
                    'schedule'   => [],
                ];
            }

            if (! isset($schedule[$techId]['schedule'][$month])) {
                $schedule[$techId]['schedule'][$month] = [];
            }

            $schedule[$techId]['schedule'][$month][$week] = [
                'id'             => $row['id'],
                'scheduled'      => $row['scheduled'],
                'equipment'      => $row['equipment_name'],
                'equipment_id'   => $row['equipment_type_id'],
                'priority'       => $row['priority'],
                'status'         => $row['status'],
                'notes'          => $row['notes'],
                'scheduled_date' => $row['scheduled_date'],
                'completed_date' => $row['completed_date'],
            ];
        }

        return $schedule;
    }

    // Actualizar estado de mantenimiento
    public function toggleMaintenanceSlot($techId, $clubId, $year, $month, $week)
    {
        // Primero verificar si existe el registro
        $query = "SELECT * FROM maintenance_schedule
                  WHERE technician_id = ? AND club_id = ? AND year = ? AND month = ? AND week = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$techId, $clubId, $year, $month, $week]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Alternar estado scheduled
            $newScheduled = ! $existing['scheduled'];
            $query        = "UPDATE maintenance_schedule
                      SET scheduled = ?, updated_at = NOW()
                      WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$newScheduled, $existing['id']]);
        } else {
            // Crear nuevo registro
            $query = "INSERT INTO maintenance_schedule
                      (technician_id, club_id, year, month, week, scheduled)
                      VALUES (?, ?, ?, ?, ?, 1)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$techId, $clubId, $year, $month, $week]);
        }
    }

    // Actualizar mantenimiento completo
    public function updateMaintenance($id, $data)
    {
        $allowedFields = ['equipment_type_id', 'priority', 'status', 'notes', 'scheduled_date', 'completed_date'];
        $setClause     = [];
        $params        = [];

        foreach ($data as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $setClause[] = "$field = ?";
                $params[]    = $value;
            }
        }

        if (empty($setClause)) {
            return false;
        }

        $setClause[] = "updated_at = NOW()";
        $params[]    = $id;

        $query = "UPDATE maintenance_schedule SET " . implode(', ', $setClause) . " WHERE id = ?";
        $stmt  = $this->conn->prepare($query);

        return $stmt->execute($params);
    }

    // Obtener estadísticas
    public function getStatistics($year, $club_id = null)
    {
        $whereClause = "WHERE year = ?";
        $params      = [$year];

        if ($club_id) {
            $whereClause .= " AND club_id = ?";
            $params[] = $club_id;
        }

        $queries = [
            'total_programados' => "SELECT COUNT(*) as count FROM maintenance_schedule $whereClause AND scheduled = 1",
            'completados'       => "SELECT COUNT(*) as count FROM maintenance_schedule $whereClause AND status = 'Completado'",
            'en_proceso'        => "SELECT COUNT(*) as count FROM maintenance_schedule $whereClause AND status = 'En Proceso'",
            'alta_prioridad'    => "SELECT COUNT(*) as count FROM maintenance_schedule $whereClause AND priority = 'Alta'",
        ];

        $stats = [];
        foreach ($queries as $key => $query) {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            $result      = $stmt->fetch();
            $stats[$key] = $result['count'];
        }

        return $stats;
    }

    // Obtener meses trimestrales
    public function getQuarterlyMonths()
    {
        return [
            ['name' => 'Marzo', 'month' => 2, 'weeks' => ['S1', 'S2', 'S3', 'S4', 'S5']],
            ['name' => 'Junio', 'month' => 5, 'weeks' => ['S1', 'S2', 'S3', 'S4', 'S5']],
            ['name' => 'Septiembre', 'month' => 8, 'weeks' => ['S1', 'S2', 'S3', 'S4', 'S5']],
            ['name' => 'Diciembre', 'month' => 11, 'weeks' => ['S1', 'S2', 'S3', 'S4', 'S5']],
        ];
    }

    // Crear mantenimiento
    public function createMaintenance($data)
    {
        $query = "INSERT INTO maintenance_schedule
                  (technician_id, club_id, year, month, week, equipment_type_id, priority, status, scheduled, notes)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['technician_id'],
            $data['club_id'],
            $data['year'],
            $data['month'],
            $data['week'],
            $data['equipment_type_id'] ?? null,
            $data['priority'] ?? 'Media',
            $data['status'] ?? 'Programado',
            $data['scheduled'] ?? 0,
            $data['notes'] ?? null,
        ]);
    }

    // Eliminar mantenimiento
    public function deleteMaintenance($id)
    {
        $query = "DELETE FROM maintenance_schedule WHERE id = ?";
        $stmt  = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // Generar reporte
    public function generateReport($year, $club_id, $format = 'array')
    {
        $schedule = $this->getMaintenanceSchedule($year, $club_id);
        $stats    = $this->getStatistics($year, $club_id);
        $club     = $this->getClubById($club_id);

        $report = [
            'club'         => $club,
            'year'         => $year,
            'schedule'     => $schedule,
            'statistics'   => $stats,
            'generated_at' => date('Y-m-d H:i:s'),
        ];

        return $report;
    }

    private function getClubById($club_id)
    {
        $query = "SELECT * FROM clubs WHERE id = ?";
        $stmt  = $this->conn->prepare($query);
        $stmt->execute([$club_id]);
        return $stmt->fetch();
    }
}
