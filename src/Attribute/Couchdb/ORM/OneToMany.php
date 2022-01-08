<?php

namespace App\Attribute\Couchdb\ORM;

use App\Attribute\Couchdb\Abstract\OneToMany as AbstractOneToMany;
use Attribute;

/**
 * Propriété d'un CouchdbDocument lié à un objet ORM.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class OneToMany extends AbstractOneToMany {
}
