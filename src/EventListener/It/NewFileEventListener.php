<?php

namespace App\EventListener\It;

use App\Entity\Event\NewFileDetectedEvent;
use App\Entity\It\Desadv;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class NewFileEventListener
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws Exception
     */
    public function onAppEntityEventNewFileDetectedEvent(NewFileDetectedEvent $event): void
    {
        $fileName = $event->getFileName();
        // On analyse le chemin du fichier de la forme '/{mode_edi}/{type_edi}/{filename}'
        $pathParts = explode('/', $fileName);
        $modeEdi = $pathParts[0];
        $typeEdi = $pathParts[1];
        $fileName = $pathParts[2];
        /**
         * En fonction du type EDI, on déclenche un événement spécifique
         */
        switch ($typeEdi) {
//            case 'orders':
//                $this->dispatchOrderEvent($modeEdi, $fileName, $event);
//                break;
//            case 'ordchg':
//                $this->dispatchOrderChgEvent($modeEdi, $fileName, $event);
//                break;
            case 'desadv':
                $this->dispatchDesadvEvent($modeEdi, $fileName, $event);
                break;
//            case 'delfor':
//                $this->dispatchDelforEvent($modeEdi, $fileName, $event);
//                break;
            default:
                break;
        }

    }
    private function dispatchOrderEvent(string $modeEdi, string $fileName, NewFileDetectedEvent $event): void
    {
        // Pour l'instant on ne fait rien car c'est nous qui envoyons les Orders
    }
    private function dispatchOrderChgEvent(string $modeEdi, string $fileName, NewFileDetectedEvent $event): void
    {
        // Pour l'instant on ne fait rien car c'est nous qui envoyons les OrderChg
    }

    /**
     * @throws Exception
     */
    private function dispatchDesadvEvent(string $modeEdi, string $fileName, NewFileDetectedEvent $event): void
    {
        $json = $event->getJsonContent();
        $sentDesadv = json_decode($json, true);
        $messageId = $sentDesadv['messageId'];
        $messageDate = $sentDesadv['messageDate'];
        $supplierOldId = $sentDesadv['supplierDetails']['ID'];

        $desAdv = new Desadv();
        $desAdv->setEdiMode($modeEdi)
            ->setMessageDate(new \DateTime($messageDate))
            ->setJson($json)
            ->setSupplierOldId($supplierOldId)
            ->setRef($messageId)
        ;
        // On persiste l'objet
        $this->entityManager->persist($desAdv);
        $this->entityManager->flush();
        // Une fois qu'on a persisté l'objet,
        // on regarde si la commande était issue d'un besoin GP ou d'un besoin AN
        // Pour cela on regarde dans le json au niveau de la clé 'despatchAdvice',
        // puis on prend le premier élément et on récupère la clé ['orderDetails']['ID']
        // Si l'ID commence par 'AN', alors c'est un besoin AN, sinon c'est un besoin GP
        $despatchDevices = $sentDesadv['despatchAdvice'];
        $firstDespatchDevice = $despatchDevices[0];
        $orderDetails = $firstDespatchDevice['orderDetails'];
        $orderId = $orderDetails['ID'];
        $needType = substr($orderId, 0, 2);
        if ($needType === 'AN') {
            // C'est un besoin AN
            // On envoie via l'API Antenne les informations desadv
            dump('Envoi desadv via API Antenne');
        } else {
            // C'est un besoin GP
            // On envoie via l'API GP les informations desadv
            dump('Envoi desadv via API GP');
        }

    }
    private function dispatchDelforEvent(string $modeEdi, string $fileName): void
    {
        // Pour l'instant on ne fait rien car c'est nous qui envoyons les Delfor
    }

}