<?php

namespace App\DataCollector;

class CouchdbLogItem {
    public const METHOD_CREATE = 'create';
    public const METHOD_DELETE = 'delete';
    public const METHOD_DOCUMENT_CREATE = 'documentCreate';
    public const METHOD_DOCUMENT_DELETE = 'documentDelete';
    public const METHOD_DOCUMENT_READ = 'documentRead';
    public const METHOD_DOCUMENT_UPDATE = 'documentUpdate';
    public const METHOD_READ = 'read';
    public const METHOD_UPDATE = 'update';

    private string $detail;
    private bool $errors;

    public function __construct(private string $document, private string $requestType, private string $method) {
        $this->detail = '';
        $this->errors = false;
    }

    public function getDetail(): string {
        return $this->detail;
    }

    public function getDocument(): string {
        return $this->document;
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function getRequestType(): string {
        return $this->requestType;
    }

    public function isErrors(): bool {
        return $this->errors;
    }

    public function setDetail(string $detail): void {
        $this->detail = $detail;
    }

    public function setDocument(string $document): void {
        $this->document = $document;
    }

    public function setErrors(bool $errors): void {
        $this->errors = $errors;
    }

    public function setMethod(string $method): void {
        $this->method = $method;
    }

    public function setRequestType(string $requestType): void {
        $this->requestType = $requestType;
    }
}
