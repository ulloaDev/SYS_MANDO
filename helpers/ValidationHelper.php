<?php
// helpers/ValidationHelper.php

/**
 * Clase helper para validación de datos de entrada
 */
class ValidationHelper 
{
    /**
     * Valida un año
     */
    public static function validateYear($year): int 
    {
        $year = filter_var($year, FILTER_VALIDATE_INT);
        
        if ($year === false) {
            throw new InvalidArgumentException("El año debe ser un número entero");
        }
        
        $currentYear = (int)date('Y');
        $minYear = 2000;
        
        if ($year < $minYear || $year > $currentYear + 10) {
            throw new InvalidArgumentException("El año debe estar entre {$minYear} y " . ($currentYear + 10));
        }
        
        return $year;
    }

    /**
     * Valida un ID
     */
    public static function validateId($id): int 
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        
        if ($id === false || $id <= 0) {
            throw new InvalidArgumentException("El ID debe ser un número entero positivo");
        }
        
        return $id;
    }

    /**
     * Valida una cadena de texto
     */
    public static function validateString($string, int $minLength = 1, int $maxLength = 255): string 
    {
        if (!is_string($string)) {
            throw new InvalidArgumentException("El valor debe ser una cadena de texto");
        }
        
        $string = trim($string);
        $length = strlen($string);
        
        if ($length < $minLength) {
            throw new InvalidArgumentException("La cadena debe tener al menos {$minLength} caracteres");
        }
        
        if ($length > $maxLength) {
            throw new InvalidArgumentException("La cadena no puede exceder {$maxLength} caracteres");
        }
        
        return $string;
    }

    /**
     * Valida un email
     */
    public static function validateEmail($email): string 
    {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        
        if ($email === false) {
            throw new InvalidArgumentException("El email no tiene un formato válido");
        }
        
        return $email;
    }

    /**
     * Valida una fecha
     */
    public static function validateDate($date, string $format = 'Y-m-d'): string 
    {
        $dateObj = DateTime::createFromFormat($format, $date);
        
        if (!$dateObj || $dateObj->format($format) !== $date) {
            throw new InvalidArgumentException("La fecha no tiene el formato correcto ({$format})");
        }
        
        return $date;
    }

    /**
     * Valida un número decimal
     */
    public static function validateFloat($value, ?float $min = null, ?float $max = null): float 
    {
        $value = filter_var($value, FILTER_VALIDATE_FLOAT);
        
        if ($value === false) {
            throw new InvalidArgumentException("El valor debe ser un número decimal");
        }
        
        if ($min !== null && $value < $min) {
            throw new InvalidArgumentException("El valor debe ser mayor o igual a {$min}");
        }
        
        if ($max !== null && $value > $max) {
            throw new InvalidArgumentException("El valor debe ser menor o igual a {$max}");
        }
        
        return $value;
    }

    /**
     * Valida que un valor esté en una lista permitida
     */
    public static function validateInArray($value, array $allowedValues): string 
    {
        if (!in_array($value, $allowedValues, true)) {
            $allowed = implode(', ', $allowedValues);
            throw new InvalidArgumentException("El valor debe ser uno de: {$allowed}");
        }
        
        return $value;
    }

    /**
     * Valida datos de mantenimiento
     */
    public static function validateMaintenanceData(array $data): array 
    {
        $required = ['tipo', 'descripcion', 'fecha', 'costo'];
        
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new InvalidArgumentException("El campo '{$field}' es requerido");
            }
        }
        
        $validated = [];
        $validated['tipo'] = self::validateString($data['tipo'], 1, 50);
        $validated['descripcion'] = self::validateString($data['descripcion'], 1, 500);
        $validated['fecha'] = self::validateDate($data['fecha']);
        $validated['costo'] = self::validateFloat($data['costo'], 0);
        
        // Campos opcionales
        if (isset($data['club_id'])) {
            $validated['club_id'] = self::validateId($data['club_id']);
        }
        
        if (isset($data['proveedor'])) {
            $validated['proveedor'] = self::validateString($data['proveedor'], 1, 100);
        }
        
        return $validated;
    }

    /**
     * Valida parámetros de filtro para reportes
     */
    public static function validateReportFilters(array $filters): array 
    {
        $validated = [];
        
        if (isset($filters['year'])) {
            $validated['year'] = self::validateYear($filters['year']);
        }
        
        if (isset($filters['club_id'])) {
            $validated['club_id'] = self::validateId($filters['club_id']);
        }
        
        if (isset($filters['tipo'])) {
            $allowedTypes = ['preventivo', 'correctivo', 'mejora', 'emergencia'];
            $validated['tipo'] = self::validateInArray($filters['tipo'], $allowedTypes);
        }
        
        if (isset($filters['fecha_inicio'])) {
            $validated['fecha_inicio'] = self::validateDate($filters['fecha_inicio']);
        }
        
        if (isset($filters['fecha_fin'])) {
            $validated['fecha_fin'] = self::validateDate($filters['fecha_fin']);
        }
        
        // Validar que fecha_fin sea posterior a fecha_inicio
        if (isset($validated['fecha_inicio']) && isset($validated['fecha_fin'])) {
            if ($validated['fecha_fin'] < $validated['fecha_inicio']) {
                throw new InvalidArgumentException("La fecha de fin debe ser posterior a la fecha de inicio");
            }
        }
        
        return $validated;
    }

    /**
     * Sanitiza datos para prevenir XSS
     */
    public static function sanitizeOutput($data) 
    {
        if (is_string($data)) {
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
        
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeOutput'], $data);
        }
        
        return $data;
    }

    /**
     * Valida un archivo subido
     */
    public static function validateUploadedFile(array $file, array $allowedTypes = [], int $maxSize = 5242880): array 
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new InvalidArgumentException("Parámetros de archivo inválidos");
        }
        
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new InvalidArgumentException("No se subió ningún archivo");
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new InvalidArgumentException("El archivo excede el tamaño máximo permitido");
            default:
                throw new InvalidArgumentException("Error desconocido al subir el archivo");
        }
        
        if ($file['size'] > $maxSize) {
            throw new InvalidArgumentException("El archivo es demasiado grande. Máximo: " . round($maxSize / 1024 / 1024, 2) . "MB");
        }
        
        if (!empty($allowedTypes)) {
            $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($fileType, $allowedTypes)) {
                $allowed = implode(', ', $allowedTypes);
                throw new InvalidArgumentException("Tipo de archivo no permitido. Permitidos: {$allowed}");
            }
        }
        
        return $file;
    }
}
?>