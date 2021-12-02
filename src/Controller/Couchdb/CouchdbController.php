<?php

namespace App\Controller\Couchdb;

use App\Entity\Management\Notification;
use App\Service\CouchDBManager;
use Exception;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/Couchdb")
 */
class CouchdbController extends AbstractController
{
   public function __construct(private CouchDBManager $DBManager, private EventDispatcherInterface $dispatcher)
   {
   }

   /**
    * @Route(name="couchdb.create", path="/create")
    * @throws ReflectionException
    * @throws Exception
    */
   public function actionCreate() : Response{
      $newNotification = new Notification();
      $newNotification
         ->setCategory("Category")
         ->setRead(false)
         ->setSubject("Test")
      ;
      $this->DBManager->itemCreate($newNotification);

      $notificationDocs = $this->DBManager->documentRead(Notification::class);
      $firstNotificationDB = $notificationDocs->getItem(2);
      /** @var Notification $firstNotification */
      $firstNotification = $firstNotificationDB->getEntity();
      $firstNotification->setRead(true);
      $this->DBManager->itemUpdate($firstNotification);
      return new Response($this->renderView('couchdb/create.html.twig'));
   }

   /**
    * @Route(name="couchdb.delete", path="/delete/{id}")
    * @throws ReflectionException
    * @throws Exception
    */
   public function actionDelete($id) : Response{

      $notificationDocs = $this->DBManager->documentRead(Notification::class);
      $item = $notificationDocs->getItem($id);
//      $deleted = false;
//      if ($item!=null) {
        $this->DBManager->itemDelete($item?->getEntity());
//        $deleted = true;
//      }
      return new Response($this->renderView('couchdb/delete.html.twig',[
         'id' => $id,
         'deleted' => true
      ]));
   }
}