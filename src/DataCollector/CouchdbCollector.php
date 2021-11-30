<?php

namespace App\DataCollector;

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
      $this->data['title']= [
         'value'=> 'green',
         'status'=> 'STATUS',
         'size'=> 'SIZE'
      ];
      $this->data['description']= [
         'value'=> 'DESCRIPTION',
         'status'=> 'STATUS',
         'size'=> 'SIZE'
      ];
   }

   public function getTitle() {
      return $this->data['title'];
   }

   public function getDescription() {
      return $this->data['description'];
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