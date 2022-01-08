<?php

namespace App\Repository\Management;

use App\Entity\Management\Notification;
use App\Repository\Couchdb\AbstractRepository;
use App\Service\CouchDBManager;
use JetBrains\PhpStorm\Pure;

class NotificationRepository extends AbstractRepository {
    #[Pure]
    public function __construct(CouchDBManager $manager) {
        parent::__construct($manager, Notification::class);
    }
}
