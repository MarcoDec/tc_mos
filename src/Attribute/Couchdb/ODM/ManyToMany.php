<?php

namespace App\Attribute\Couchdb\ODM;

use App\Attribute\Couchdb\Abstract\ManyToMany as AbstractManyToMany;
use Attribute;

/**
 * Classe ORM en relation avec un objet Couchdb.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ManyToMany extends AbstractManyToMany {
}
