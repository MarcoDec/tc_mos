<?php

namespace App\Attribute\Couchdb\ORM;

use App\Attribute\Couchdb\Abstract\ManyToOne as AbstractManyToOne;
use Attribute;
/**
 * Propriété d'un CouchdbDocument lié à un objet ORM.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ManyToOne extends AbstractManyToOne {
}
