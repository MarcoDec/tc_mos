<?php

namespace App\Attribute\Couchdb\ORM;

use App\Attribute\Couchdb\Abstract\ManyToMany as AbstractManyToMany;
use Attribute;

/**
 * Propriété liée à un objet ORM d'une entité implémentant l'attribut App\Attribute\couchdb\Document.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ManyToMany extends AbstractManyToMany {
}
