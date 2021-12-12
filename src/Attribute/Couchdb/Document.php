<?php

namespace App\Attribute\Couchdb;

use Attribute;

/**
 * Classes gérées dans Couchdb.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Document {
}
