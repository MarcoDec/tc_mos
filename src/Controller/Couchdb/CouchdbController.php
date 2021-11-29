<?php

namespace App\Controller\Couchdb;

use App\Entity\Management\Notification;
use App\Service\CouchDBManager;
use Doctrine\CouchDB\HTTP\HTTPException;
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
    * @throws HTTPException
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
}