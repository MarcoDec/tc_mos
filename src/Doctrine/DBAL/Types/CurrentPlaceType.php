<?php

namespace App\Doctrine\DBAL\Types;

abstract class CurrentPlaceType extends Type {
    public const TYPE_AGREED = 'agreed';
    public const TYPE_BLOCKED = 'blocked';
    public const TYPE_DISABLED = 'disabled';
    public const TYPE_DRAFT = 'draft';
    public const TYPE_ENABLED = 'enabled';
    public const TYPE_TO_VALIDATE = 'to_validate';
    public const TYPE_UNDER_EXEMPTION = 'under_exemption';
    public const TYPE_WARNING = 'warning';
}
