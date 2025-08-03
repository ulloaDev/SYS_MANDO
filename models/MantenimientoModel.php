<?php
class MantenimientoModel {
    private $db;
  
    public function __construct(PDO $db) {
        $this->db = $db;
    }

/**
     * Obtiene programación de mantenimientos con filtros
     */
    public function obtenerProgramacion(array $filtros = []) {
        $query = "SELECT 
                    ms.id, 
                    ms.scheduled_date AS fecha,
                    ms.status AS estado,
                    ms.priority AS prioridad,
                    e.NombrePC AS equipo,
                    e.IdEq AS equipo_id,
                    t.name AS tecnico,
                    t.id AS tecnico_id,
                    c.name AS club,
                    c.id AS club_id,
                    te.NombreEq AS tipo_equipo
FROM maintenance_schedule ms
                  LEFT JOIN equipos e ON ms.equipo_id = e.IdEq
                  LEFT JOIN technicians t ON ms.technician_id = t.id
                  LEFT JOIN clubs c ON ms.club_id = c.id
                  LEFT JOIN tipoeq te ON ms.equipment_type_id = te.NombreEq
                  WHERE 1=1";
        
        $params = [];
        
        // Filtros dinámicos
        if (!empty($filtros['fecha_inicio'])) {
            $query .= " AND ms.scheduled_date >= :fecha_inicio";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $query .= " AND ms.scheduled_date <= :fecha_fin";
            $params[':fecha_fin'] = $filtros['fecha_fin'];
        }

        if (!empty($filtros['estado'])) {
            $query .= " AND ms.status = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        if (!empty($filtros['tecnico_id'])) {
            $query .= " AND ms.technician_id = :tecnico_id";
            $params[':tecnico_id'] = $filtros['tecnico_id'];
        }

        if (!empty($filtros['equipo_id'])) {
            $query .= " AND ms.equipo_id = :equipo_id";
            $params[':equipo_id'] = $filtros['equipo_id'];
        }

        // Ordenamiento por defecto
        $query .= " ORDER BY ms.scheduled_date DESC, ms.priority DESC";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en MantenimientoModel: " . $e->getMessage());
            throw new Exception("Error al obtener programación", 500);
        }
    }

/**
     * Guarda o actualiza una programación
     */
    public function guardarProgramacion(array $data): array {
        $this->db->beginTransaction();

        try {
            if (empty($data['id'])) {
                // Insertar nueva programación
                $query = "INSERT INTO maintenance_schedule (
                    technician_id, equipo_id, equipment_type_id, 
                    club_id, scheduled_date, priority,
                    status, notes, estimated_duration, 
                    frequency_id
                ) VALUES (
                    :technician_id, :equipo_id, :equipment_type_id,
                    :club_id, :scheduled_date, :priority,
                    :status, :notes, :estimated_duration,
                    :frequency_id
                )";
            } else {
                // Actualizar programación existente
                $query = "UPDATE maintenance_schedule SET
                    technician_id = :technician_id,
                    equipo_id = :equipo_id,
                    equipment_type_id = :equipment_type_id,
                    club_id = :club_id,
                    scheduled_date = :scheduled_date,
                    priority = :priority,
                    status = :status,
                    notes = :notes,
                    estimated_duration = :estimated_duration,
                    frequency_id = :frequency_id,
                    updated_at = NOW()
                WHERE id = :id";
            }

                $stmt = $this->db->prepare($query);
            
            // Parámetros comunes
            $params = [
                ':technician_id' => $data['technician_id'] ?? null,
                ':equipo_id' => $data['equipo_id'],
                ':equipment_type_id' => $data['equipment_type_id'] ?? null,
                ':club_id' => $data['club_id'] ?? null,
                ':scheduled_date' => $data['scheduled_date'],
                ':priority' => $data['priority'] ?? 'Media',
                ':status' => $data['status'] ?? 'Programado',
                ':notes' => $data['notes'] ?? null,
                ':estimated_duration' => $data['estimated_duration'] ?? 120,
                ':frequency_id' => $data['frequency_id'] ?? null
            ];

            if (!empty($data['id'])) {
                $params[':id'] = $data['id'];
            }

            $stmt->execute($params);
            
            $id = empty($data['id']) ? $this->db->lastInsertId() : $data['id'];
            
            $this->db->commit();

            return [
'success' => true,
'id' => $id
];

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error al guardar programación: " . $e->getMessage());
            throw new Exception("Error en la base de datos al guardar", 500);
        }
    }

    /**
     * Cambia el estado de un mantenimiento
     */
    public function cambiarEstado(int $id, string $estado): array {
        $this->db->beginTransaction();
        
        try {
            $query = "UPDATE maintenance_schedule 
                     SET status = :estado, 
                         updated_at = NOW()
                     WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id, ':estado' => $estado]);
            
            $this->db->commit();
            
            return [
                'success' => true,
                'rows_affected' => $stmt->rowCount()
            ];
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error al cambiar estado: " . $e->getMessage());
            throw new Exception("Error al actualizar estado", 500);
        }
    }

    /**
     * Obtiene tipos de equipo disponibles
     */
    public function obtenerTiposEquipo(): array {
        $query = "SELECT IdTpEq, NombreEq FROM tipoeq";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene técnicos disponibles
     */
    public function obtenerTecnicos(): array {
        $query = "SELECT id, name FROM technicians";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
}