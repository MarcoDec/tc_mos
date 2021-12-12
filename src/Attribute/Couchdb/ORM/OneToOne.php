<?php

namespace App\Attribute\Couchdb\ORM;

use App\Attribute\Couchdb\Abstract\OneToOne as AbstractOneToOne;
use Attribute;
/**
 * Propriété d'un CouchdbDocument lié à un objet ORM.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class OneToOne extends AbstractOneToOne {
}
