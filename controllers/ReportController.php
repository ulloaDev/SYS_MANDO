<?php
// controllers/ReportController.php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../services/ReportService.php';

class ReportController extends BaseController {
    private ReportService $reportService;

    public function __construct(ReportService $reportService) {
        $this->reportService = $reportService;
    }

    public function showReportForm(): void {
        try {
            $year = (int)($_GET['year'] ?? date('Y'));
            $clubId = isset($_GET['club']) ? (int)$_GET['club'] : null;
            
            $data = [
                'clubs' => $this->reportService->getAvailableClubs(),
                'selectedYear' => $year,
                'selectedClub' => $clubId
            ];
            
            $this->renderView('reports/form', $data);
            
        } catch (Exception $e) {
            $this->handleException($e, 'ReportController::showReportForm');
        }
    }

    public function generateReport(): void {
        try {
            $this->validateRequestMethod('POST');
            
            $reportData = $this->reportService->generateReport(
                $_POST['report_type'],
                (int)$_POST['year'],
                isset($_POST['club']) ? (int)$_POST['club'] : null,
                $_POST['format']
            );
            
            $this->jsonResponse([
                'success' => true,
                'download_url' => $reportData['download_url']
            ]);
            
        } catch (Exception $e) {
            $this->handleException($e, 'ReportController::generateReport');
        }
    }
}