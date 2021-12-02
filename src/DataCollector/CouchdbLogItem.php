<?php
namespace App\DataCollector;

class CouchdbLogItem {
   private string $detail;
   private bool $errors;

   public const METHOD_CREATE='create';
   public const METHOD_DOCUMENT_CREATE='documentCreate';
   public const METHOD_UPDATE='update';
   public const METHOD_DOCUMENT_UPDATE='documentUpdate';
   public const METHOD_READ='read';
   public const METHOD_DOCUMENT_READ='documentRead';
   public const METHOD_DELETE='delete';
   public const METHOD_DOCUMENT_DELETE='documentDelete';

   public function __construct(private string $document, private string $requestType, private string $method){
      $this->detail="";
      $this->errors=false;
   }

   /**
    * @return string
    */
   public function getDetail(): string
   {
      return $this->detail;
   }

   /**
    * @param string $detail
    */
   public function setDetail(string $detail): void
   {
      $this->detail = $detail;
   }

   /**
    * @return bool
    */
   public function isErrors(): bool
   {
      return $this->errors;
   }

   /**
    * @param bool $errors
    */
   public function setErrors(bool $errors): void
   {
      $this->errors = $errors;
   }

   /**
    * @return string
    */
   public function getDocument(): string
   {
      return $this->document;
   }

   /**
    * @param string $document
    */
   public function setDocument(string $document): void
   {
      $this->document = $document;
   }

   /**
    * @return string
    */
   public function getRequestType(): string
   {
      return $this->requestType;
   }

   /**
    * @param string $requestType
    */
   public function setRequestType(string $requestType): void
   {
      $this->requestType = $requestType;
   }

   /**
    * @return string
    */
   public function getMethod(): string
   {
      return $this->method;
   }

   /**
    * @param string $method
    */
   public function setMethod(string $method): void
   {
      $this->method = $method;
   }


}
