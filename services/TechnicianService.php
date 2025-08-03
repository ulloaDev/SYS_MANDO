<?php
// services/TechnicianService.php

require_once __DIR__ . '/../repositories/TechnicianRepository.php';
require_once __DIR__ . '/../repositories/ClubRepository.php';

class TechnicianService {
    private TechnicianRepository $technicianRepo;
    private ClubRepository $clubRepo;

    public function __construct(
        TechnicianRepository $technicianRepo,
        ClubRepository $clubRepo
    ) {
        $this->technicianRepo = $technicianRepo;
        $this->clubRepo = $clubRepo;
    }

    public function getTechniciansByClub(?int $clubId): array {
        if ($clubId === 670) {
            return []; // Caso especial para club 670
        }
        
        $technicians = $this->technicianRepo->findByClub($clubId);
        
        return array_map(function($tech) {
            return [
                'id' => $tech['id'],
                'name' => $tech['name'],
                'workload' => $this->calculateWorkload($tech['id']),
                'specializations' => $this->getTechnicianSpecializations($tech['id'])
            ];
        }, $technicians);
    }

    public function assignTechnicianToClub(int $userId, int $clubId): bool {
        if ($clubId === 670) {
            throw new InvalidArgumentException("No se pueden asignar técnicos al club 670");
        }
        
        if (!$this->clubRepo->userBelongsToClub($userId, $clubId)) {
            throw new RuntimeException("El usuario no pertenece a este club");
        }
        
        return $this->technicianRepo->assignToClub($userId, $clubId);
    }

    private function calculateWorkload(int $technicianId): int {
        return $this->technicianRepo->countActiveMaintenance($technicianId);
    }

    private function getTechnicianSpecializations(int $technicianId): array {
        return $this->technicianRepo->getTechnicianSpecializations($technicianId);
    }
}