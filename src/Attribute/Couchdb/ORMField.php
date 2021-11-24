<?php

namespace App\Attribute\Couchdb;

use Attribute;
/**
 * Propriété d'un CouchdbDocument lié à un objet ORM
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ORMField
{

}