<?php
// models/TecnicoModel.php

require_once __DIR__ . '/../config/database.php';

class TecnicoModel {
    private PDO $db;

    public function __construct(?PDO $db = null) {
        $this->db = $db ?? Database::getConnection();
    }

    /**
     * Obtiene todos los técnicos con información completa
     * @param int|null $clubId Filtro por club
     * @return array Lista de técnicos con sus especialidades
     */
    public function obtenerTodosLosTecnicos(?int $clubId = null): array {
        $query = "SELECT 
                    t.id,
                    t.name,
                    t.contact_info,
                    t.club_id,
                    t.user_id,
                    t.created_at,
                    c.name as club_name,
                    c.location as club_location,
                    u.user_name,
                    u.user_email,
                    CONCAT(u.firstname, ' ', u.lastname) as full_name,
                    GROUP_CONCAT(DISTINCT et.Nombre SEPARATOR ', ') as especialidades
                  FROM technicians t
                  LEFT JOIN clubs c ON t.club_id = c.id
                  LEFT JOIN users u ON t.user_id = u.user_id
                  LEFT JOIN usuarios_especialidades ue ON u.user_id = ue.user_id
                  LEFT JOIN especialidades_tecnicos et ON ue.IdEspecialidad = et.IdEspecialidad
                  WHERE ue.Estado = 'A' OR ue.Estado IS NULL";
        
        $params = [];
        
        if ($clubId !== null) {
            $query .= " AND t.club_id = ?";
            $params[] = $clubId;
        }
        
        $query .= " GROUP BY t.id ORDER BY t.name";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un técnico por su ID con información completa
     * @param int $id ID del técnico
     * @return array|null Datos del técnico o null si no existe
     */
    public function obtenerTecnicoPorId(int $id): ?array {
        // Datos básicos del técnico
        $query = "SELECT 
                    t.id,
                    t.name,
                    t.contact_info,
                    t.club_id,
                    t.user_id,
                    t.created_at,
                    c.name as club_name,
                    c.location as club_location,
                    u.user_name,
                    u.user_email,
                    CONCAT(u.firstname, ' ', u.lastname) as full_name
                  FROM technicians t
                  LEFT JOIN clubs c ON t.club_id = c.id
                  LEFT JOIN users u ON t.user_id = u.user_id
                  WHERE t.id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        $tecnico = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tecnico) {
            return null;
        }

        // Obtener especialidades
        $tecnico['especialidades'] = $this->obtenerEspecialidadesTecnico($tecnico['user_id']);
        
        // Obtener equipos asignados
        $tecnico['equipos_asignados'] = $this->obtenerEquiposAsignados($id);

        return $tecnico;
    }

    /**
     * Obtiene técnicos especializados en un tipo de equipo específico
     * @param int $tipoEquipoId ID del tipo de equipo (relacionado con tipoeq)
     * @return array Lista de técnicos especializados
     */
    public function obtenerTecnicosPorTipoEquipo(int $tipoEquipoId): array {
        $query = "SELECT 
                    t.id,
                    t.name,
                    te.NombreEq as tipo_equipo,
                    et.Nombre as especialidad
                  FROM technicians t
                  JOIN users u ON t.user_id = u.user_id
                  JOIN usuarios_especialidades ue ON u.user_id = ue.user_id
                  JOIN especialidades_tecnicos et ON ue.IdEspecialidad = et.IdEspecialidad
                  JOIN tipoeq te ON et.RelacionTipoEq = te.IdTipoEq
                  WHERE te.IdTipoEq = ? AND ue.Estado = 'A'
                  GROUP BY t.id";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$tipoEquipoId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene equipos asignados a un técnico
     * @param int $tecnicoId ID del técnico
     * @return array Lista de equipos asignados
     */
    public function obtenerEquiposAsignados(int $tecnicoId): array {
        $query = "SELECT 
                    e.IdEq as id,
                    e.NombrePC as nombre,
                    te.NombreEq as tipo,
                    te.IdTipoEq as tipo_id
                  FROM equipos e
                  JOIN technicians t ON e.Club = t.club_id
                  JOIN tipoeq te ON e.Tipo = te.IdTipoEq
                  WHERE t.id = ? AND e.Estado = 'A'";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$tecnicoId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Asigna una especialidad relacionada a tipos de equipo a un técnico
     * @param int $userId ID del usuario técnico
     * @param int $especialidadId ID de la especialidad
     * @return bool True si se asignó correctamente
     */
    public function asignarEspecialidad(int $userId, int $especialidadId): bool {
        $query = "INSERT INTO usuarios_especialidades 
                  (user_id, IdEspecialidad, Estado, FechaAsignacion) 
                  VALUES (?, ?, 'A', NOW())
                  ON DUPLICATE KEY UPDATE Estado = 'A'";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([$userId, $especialidadId]);
    }

    /**
     * Obtiene la carga de trabajo actual de un técnico
     * @param int $tecnicoId ID del técnico
     * @return array Estadísticas de carga de trabajo
     */
    public function obtenerCargaTrabajo(int $tecnicoId): array {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'Completado' THEN 1 ELSE 0 END) as completados,
                    SUM(CASE WHEN status = 'Pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN priority = 'Alta' THEN 1 ELSE 0 END) as alta_prioridad
                  FROM maintenance_schedule
                  WHERE technician_id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$tecnicoId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mantener los métodos existentes (crearTecnico, actualizarTecnico, eliminarTecnico, etc.)
    // con sus implementaciones originales pero añadiendo manejo de errores
    
    /**
     * Actualizar técnico existente con manejo de errores mejorado
     * @param int $id ID del técnico
     * @param array $datos Datos actualizados
     * @return bool True si se actualizó correctamente
     * @throws PDOException Si ocurre un error en la actualización
     */
    public function actualizarTecnico(int $id, array $datos): bool {
        $query = "UPDATE technicians 
                  SET name = ?, contact_info = ?, club_id = ?
                  WHERE id = ?";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $datos['name'],
                $datos['contact_info'],
                $datos['club_id'],
                $id
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new PDOException("Error al actualizar técnico: " . $e->getMessage());
        }
    }

    /**
     * Obtiene técnicos disponibles para asignar a un tipo de equipo específico
     * @param int $tipoEquipoId ID del tipo de equipo
     * @param int $clubId ID del club (opcional)
     * @return array Lista de técnicos disponibles
     */
    public function obtenerTecnicosDisponibles(int $tipoEquipoId, ?int $clubId = null): array {
        $query = "SELECT 
                    t.id,
                    t.name,
                    c.name as club_name
                  FROM technicians t
                  JOIN users u ON t.user_id = u.user_id
                  JOIN usuarios_especialidades ue ON u.user_id = ue.user_id
                  JOIN especialidades_tecnicos et ON ue.IdEspecialidad = et.IdEspecialidad
                  JOIN tipoeq te ON et.RelacionTipoEq = te.IdTipoEq
                  LEFT JOIN clubs c ON t.club_id = c.id
                  WHERE te.IdTipoEq = ? AND ue.Estado = 'A'";
        
        $params = [$tipoEquipoId];
        
        if ($clubId !== null) {
            $query .= " AND t.club_id = ?";
            $params[] = $clubId;
        }
        
        $query .= " GROUP BY t.id ORDER BY t.name";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}