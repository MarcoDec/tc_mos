<?php

namespace App\DataCollector;

use App\Service\CouchDBManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class CouchdbCollector extends DataCollector
{

   public function __construct(private CouchDBManager $manager) {
   }

   /**
    * @inheritDoc
    */
   public function collect(Request $request, Response $response, \Throwable $exception = null):void
   {
      $this->data['actions'] = collect($this->manager->actions)->sortKeys()->toArray();
   }

   public function getActions():array {
      return $this->data['actions'];
   }

   public function getCreateIndicator():array {
      return $this->getIndicator(CouchdbLogItem::METHOD_CREATE);
   }
   public function getReadIndicator():array {
      return $this->getIndicator(CouchdbLogItem::METHOD_READ);
   }
   public function getUpdateIndicator():array {
      return $this->getIndicator(CouchdbLogItem::METHOD_UPDATE);
   }
   public function getDeleteIndicator():array {
      return $this->getIndicator(CouchdbLogItem::METHOD_DELETE);
   }
   public function getDocumentCreateIndicator():array {
      return $this->getIndicator(CouchdbLogItem::METHOD_DOCUMENT_CREATE);
   }
   public function getDocumentReadIndicator():array {
      return $this->getIndicator(CouchdbLogItem::METHOD_DOCUMENT_READ);
   }
   public function getDocumentUpdateIndicator():array {
      return $this->getIndicator(CouchdbLogItem::METHOD_DOCUMENT_UPDATE);
   }
   public function getDocumentDeleteIndicator():array {
      return $this->getIndicator(CouchdbLogItem::METHOD_DOCUMENT_DELETE);
   }
   public function getIndicator($requestType):array {
      $nbOk = collect($this->data['actions'])->filter(function(CouchdbLogItem $item) use ($requestType){
         return !$item->isErrors() && $item->getRequestType()==$requestType;
      })->count();
      $nbKo = collect($this->data['actions'])->filter(function($item) use ($requestType){
         return $item->isErrors() && $item->getRequestType()==$requestType;
      })->count();
      return ["ok"=>$nbOk, "ko"=>$nbKo];
   }
   /**
    * @inheritDoc
    */
   public function getName():string
   {
      return self::class;
   }

   public function reset():void
   {
      $this->data = [];
   }
}