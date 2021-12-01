<?php

namespace App\DataCollector;

use App\Entity\Management\Notification;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class CouchdbCollector extends DataCollector
{


   /**
    * @inheritDoc
    */
   public function collect(Request $request, Response $response, \Throwable $exception = null):void
   {
      $this->data['actions']= [
        [
           "document"=> Notification::class,
           "requestType" => "create",
           "detail" => "{{ item.toString }}",
           "errors" => false
        ],
         [
            "document"=> Notification::class,
            "requestType" => "update",
            "detail" => "{{ item.toString }}",
            "errors" => true
         ],
         [
            "document"=> Notification::class,
            "requestType" => "delete",
            "detail" => "{{ item.toString }}",
            "errors" => false
         ],
      ];
   }

   public function getActions():array {
      return $this->data['actions'];
   }

   public function getCreateIndicator():array {
      return $this->getIndicator("create");
   }
   public function getReadIndicator():array {
      return $this->getIndicator("read");
   }
   public function getUpdateIndicator():array {
      return $this->getIndicator("update");
   }
   public function getDeleteIndicator():array {
      return $this->getIndicator("delete");
   }
   public function getIndicator($requestType):array {
      $nbOk = collect($this->data['actions'])->filter(function($item) use ($requestType){
         return !$item["errors"] && $item["requestType"]==$requestType;
      })->count();
      $nbKo = collect($this->data['actions'])->filter(function($item) use ($requestType){
         return $item["errors"] && $item["requestType"]==$requestType;
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