<?php
// models/EquipoModel.php

require_once __DIR__ . '/../config/database.php';

class EquipoModel
{
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Obtiene todos los equipos activos con información de tipo
     * @return array Lista de equipos con detalles de tipo
     * @throws Exception Si ocurre un error en la consulta
     */
    public function getAllEquipment(): array
    {
        $query = "SELECT 
                    e.IdEq, 
                    e.Serie, 
                    e.NombrePC, 
                    e.Tipo as tipo_id,
                    t.NombreEq as tipo_nombre,
                    t.Descripcion as tipo_descripcion,
                    e.Usuario,
                    e.Club,
                    e.Estado
                  FROM equipos e
                  LEFT JOIN tipoeq t ON e.Tipo = t.IdTipoEq
                  WHERE e.Estado = 'A'
                  ORDER BY e.NombrePC";
        
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . $this->db->error);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Error al obtener equipos: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $equipment = [];
        while ($row = $result->fetch_assoc()) {
            $equipment[] = $row;
        }
        $result->free();
        
        return $equipment;
    }

    /**
     * Obtiene un equipo específico por ID con detalles de tipo
     * @param int $id ID del equipo
     * @return array|null Datos del equipo o null si no existe
     * @throws Exception Si ocurre un error en la consulta
     */
    public function getEquipmentById(int $id): ?array
    {
        $query = "SELECT 
                    e.IdEq, 
                    e.Serie, 
                    e.NombrePC, 
                    e.Tipo as tipo_id,
                    t.NombreEq as tipo_nombre,
                    t.Descripcion as tipo_descripcion,
                    e.Usuario,
                    e.Club,
                    e.Estado
                  FROM equipos e
                  LEFT JOIN tipoeq t ON e.Tipo = t.IdTipoEq
                  WHERE e.IdEq = ?";
        
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . $this->db->error);
        }
        
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception("Error al obtener equipo: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }

    /**
     * Obtiene todos los tipos de equipo disponibles
     * @return array Lista de tipos de equipo
     * @throws Exception Si ocurre un error en la consulta
     */
    public function getAllEquipmentTypes(): array
    {
        $query = "SELECT 
                    IdTipoEq as id, 
                    NombreEq as nombre, 
                    Descripcion as descripcion
                  FROM tipoeq
                  ORDER BY NombreEq";
        
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . $this->db->error);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Error al obtener tipos: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $types = [];
        while ($row = $result->fetch_assoc()) {
            $types[] = $row;
        }
        $result->free();
        
        return $types;
    }

    /**
     * Obtiene equipos por club con información de tipo
     * @param int $clubId ID del club
     * @return array Lista de equipos del club
     * @throws Exception Si ocurre un error en la consulta
     */
    public function getEquipmentByClub(int $clubId): array
    {
        $query = "SELECT 
                    e.IdEq, 
                    e.NombrePC, 
                    e.Tipo as tipo_id,
                    t.NombreEq as tipo_nombre,
                    e.Estado
                  FROM equipos e
                  LEFT JOIN tipoeq t ON e.Tipo = t.IdTipoEq
                  WHERE e.Club = ? AND e.Estado = 'A'
                  ORDER BY e.NombrePC";
        
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . $this->db->error);
        }
        
        $stmt->bind_param("i", $clubId);
        if (!$stmt->execute()) {
            throw new Exception("Error al obtener equipos: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $equipment = [];
        while ($row = $result->fetch_assoc()) {
            $equipment[] = $row;
        }
        $result->free();
        
        return $equipment;
    }

    /**
     * Actualiza el tipo de un equipo
     * @param int $equipoId ID del equipo
     * @param int $tipoId ID del nuevo tipo
     * @return bool True si la actualización fue exitosa
     * @throws Exception Si ocurre un error en la actualización
     */
    public function actualizarTipoEquipo(int $equipoId, int $tipoId): bool
    {
        $query = "UPDATE equipos SET Tipo = ? WHERE IdEq = ?";
        
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . $this->db->error);
        }
        
        $stmt->bind_param("ii", $tipoId, $equipoId);
        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar tipo: " . $stmt->error);
        }
        
        return $stmt->affected_rows > 0;
    }
}