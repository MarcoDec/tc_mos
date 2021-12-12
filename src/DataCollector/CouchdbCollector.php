<?php

namespace App\DataCollector;

use App\Service\CouchDBManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Throwable;

// DataCollector dans debug bar pour CouchDB
class CouchdbCollector extends DataCollector {
    public function __construct(private CouchDBManager $manager) {
    }

    /**
     * @inheritDoc
     */
    public function collect(Request $request, Response $response, ?Throwable $exception = null): void {
        $this->data['actions'] = collect($this->manager->actions)->sortKeys()->toArray();
    }

   /**
    * @return array<string,CouchdbLogItem>
    */
    public function getActions(): array {
        return $this->data['actions'];
    }

   /**
    * @return array<string,int>
    */
    public function getCreateIndicator(): array {
        return $this->getIndicator(CouchdbLogItem::METHOD_CREATE);
    }

   /**
    * @return array<string,int>
    */
    public function getDeleteIndicator(): array {
        return $this->getIndicator(CouchdbLogItem::METHOD_DELETE);
    }

   /**
    * @return array<string,int>
    */
    public function getDocumentCreateIndicator(): array {
        return $this->getIndicator(CouchdbLogItem::METHOD_DOCUMENT_CREATE);
    }

   /**
    * @return array<string,int>
    */
    public function getDocumentDeleteIndicator(): array {
        return $this->getIndicator(CouchdbLogItem::METHOD_DOCUMENT_DELETE);
    }

   /**
    * @return array<string,int>
    */
    public function getDocumentReadIndicator(): array {
        return $this->getIndicator(CouchdbLogItem::METHOD_DOCUMENT_READ);
    }

   /**
    * @return array<string,int>
    */
    public function getDocumentUpdateIndicator(): array {
        return $this->getIndicator(CouchdbLogItem::METHOD_DOCUMENT_UPDATE);
    }

   /**
    * @param string $requestType
    * @return array<string,int>
    */
    public function getIndicator(string $requestType): array {
        $nbOk = collect($this->data['actions'])->filter(static fn (CouchdbLogItem $item) => !$item->isErrors() && $item->getRequestType() == $requestType)->count();
        $nbKo = collect($this->data['actions'])->filter(static fn ($item) => $item->isErrors() && $item->getRequestType() == $requestType)->count();
        return ['ok' => $nbOk, 'ko' => $nbKo];
    }

    /**
     * @inheritDoc
     */
    public function getName(): string {
        return self::class;
    }

   /**
    * @return array<string,int>
    */
    public function getReadIndicator(): array {
        return $this->getIndicator(CouchdbLogItem::METHOD_READ);
    }

   /**
    * @return array<string,int>
    */
    public function getUpdateIndicator(): array {
        return $this->getIndicator(CouchdbLogItem::METHOD_UPDATE);
    }

    public function reset(): void {
        $this->data = [];
    }
}
