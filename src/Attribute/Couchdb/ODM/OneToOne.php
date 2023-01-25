<?php

namespace App\Attribute\Couchdb\ODM;

use App\Attribute\Couchdb\Abstract\OneToOne as AbstractOneToOne;
use Attribute;

/**
 * Propriété d'un CouchdbDocument lié à un objet CouchDB.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class OneToOne extends AbstractOneToOne {
}
