<?php

namespace App\Attribute\Couchdb\ODM;

use App\Attribute\Couchdb\Abstract\ManyToOne as AbstractManyToOne;
use Attribute;
/**
 * Propriété d'un CouchdbDocument lié à un objet CouchDB.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ManyToOne extends AbstractManyToOne {
}
