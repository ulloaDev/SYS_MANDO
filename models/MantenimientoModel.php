<?php
class MantenimientoModel {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function obtenerProgramacion(array $filtros = []) {
        try {
            $query = "SELECT * FROM programaciones WHERE 1=1";
            $params = [];

            if (!empty($filtros['fecha_inicio'])) {
                $query .= " AND fecha >= :fecha_inicio";
                $params[':fecha_inicio'] = $filtros['fecha_inicio']->format('Y-m-d');
            }

            if (!empty($filtros['fecha_fin'])) {
                $query .= " AND fecha <= :fecha_fin";
                $params[':fecha_fin'] = $filtros['fecha_fin']->format('Y-m-d');
            }

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            throw new Exception("Error al obtener programaciones: " . $e->getMessage());
        }
    }

    public function guardarProgramacion(array $datos) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO programaciones 
                (equipo_id, fecha, tecnico, creado_en) 
                VALUES (:equipo_id, :fecha, :tecnico, NOW())
            ");

            $stmt->execute([
                ':equipo_id' => $datos['equipo_id'],
                ':fecha' => $datos['scheduled_date'],
                ':tecnico' => $datos['tecnico']
            ]);

            $id = $this->db->lastInsertId();
            $this->db->commit();

            return ['id' => $id];

        } catch (PDOException $e) {
            $this->db->rollBack();
            throw new Exception("Error al guardar: " . $e->getMessage());
        }
    }
}