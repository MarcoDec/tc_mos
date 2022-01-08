<?php

namespace App\Attribute\Couchdb\ORM;

use Attribute;

/**
 * Propriété d'une classe CouchDB en lien avec un objet ORM.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class ORMDocument {
}
