<?php

namespace App\Attribute\Couchdb\ODM;

use App\Attribute\Couchdb\Abstract\OneToMany as AbstractOneToMany;
use Attribute;

/**
 * Propriété d'un CouchdbDocument lié à un objet CouchDB.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class OneToMany extends AbstractOneToMany {
}
