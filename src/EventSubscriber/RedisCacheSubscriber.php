<?php
namespace App\EventSubscriber;

use Predis\Client;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;

class RedisCacheSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            'redis.key_expired' => 'onKeyExpired',
        ];
    }

   
}

?>