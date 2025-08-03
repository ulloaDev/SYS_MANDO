<?php
class ServiceException extends Exception {
    private array $details;

    public function __construct(
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null,
        array $details = []
    ) {
        parent::__construct($message, $code, $previous);
        $this->details = $details;
    }

    public function getDetails(): array {
        return $this->details;
    }

    public function toArray(): array {
        return [
            'error' => $this->getMessage(),
            'code' => $this->getCode(),
            'details' => $this->getDetails()
        ];
    }
}