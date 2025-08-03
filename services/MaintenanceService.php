<?php
class MaintenanceService {
    private MantenimientoModel $model;

    public function __construct(MantenimientoModel $model) {
        $this->model = $model;
    }

    public function getCalendarData(int $year, ?int $clubId): array {
        return $this->model->obtenerProgramacion([
            'year' => $year,
            'club_id' => $clubId
        ]);
    }

    public function saveMaintenance(array $data, int $userId): Maintenance {
        // Validación adicional y lógica de negocio
        return $this->model->guardarProgramacion($data, $userId);
    }
}