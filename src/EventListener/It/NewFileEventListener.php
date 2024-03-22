<?php

namespace App\EventListener\It;

use App\Entity\Event\NewFileDetectedEvent;

class NewFileEventListener
{
    public function onAppEntityEventNewFileDetectedEvent(NewFileDetectedEvent $event): void
    {
        $fileName = $event->getFileName();
        //        echo "New file detected: $fileName\n";
        // Continuer le code
    }
}