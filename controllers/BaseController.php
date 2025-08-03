<?php
abstract class BaseController {
    protected PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    protected function jsonResponse(
        array $data,
        int $httpCode = 200,
        array $headers = []
    ): void {
        http_response_code($httpCode);
        header('Content-Type: application/json; charset=utf-8');
        
        foreach ($headers as $header) {
            header($header);
        }
        
        echo json_encode([
            'success' => $httpCode < 400,
            'data' => $data,
            'meta' => [
                'timestamp' => date('c'),
                'endpoint' => $_SERVER['REQUEST_URI']
            ]
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    protected function handleError(
        Throwable $e,
        string $context = '',
        array $additionalData = []
    ): void {
        $errorId = uniqid('ERR-');
        error_log(sprintf(
            "[%s] Error en %s: %s\nTrace: %s",
            $errorId,
            $context,
            $e->getMessage(),
            $e->getTraceAsString()
        ));
        
        $this->jsonResponse([
            'error' => $e->getMessage(),
            'error_id' => $errorId,
            'code' => $e->getCode(),
            'context' => $context,
            ...$additionalData
        ], $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500);
    }

    protected function validateRequestMethod(array $allowedMethods): void {
        if (!in_array($_SERVER['REQUEST_METHOD'], $allowedMethods)) {
            throw new Exception(
                "Método no permitido. Esperado: " . implode(', ', $allowedMethods), 
                405
            );
        }
    }

    protected function getRequestData(): array {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method === 'GET') return $_GET;
        
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        return $method === 'POST' ? array_merge($_POST, $input) : $input;
    }

    protected function renderView(
        string $viewPath, 
        array $data = [], 
        int $httpCode = 200
    ): void {
        http_response_code($httpCode);
        extract($data);
        
        $fullPath = realpath(__DIR__ . "/../views/$viewPath.php");
        
        if (!file_exists($fullPath)) {
            throw new Exception("Vista no encontrada: $viewPath", 404);
        }
        
        include $fullPath;
        exit;
    }
}