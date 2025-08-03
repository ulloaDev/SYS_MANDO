<?php
// FILE: C:\xampp\htdocs\sistema_manto\exceptions\ApiExceptions.php

class ApiException extends Exception {
    protected $details;

    public function __construct($message = "", $code = 0, Exception $previous = null, $details = null) {
        parent::__construct($message, $code, $previous);
        $this->details = $details;
    }

    public function getDetails() {
        return $this->details;
    }
}

class DatabaseException extends ApiException {}
class NotFoundException extends ApiException {}
class ValidationException extends ApiException {}