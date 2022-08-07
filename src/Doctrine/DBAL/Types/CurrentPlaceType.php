<?php

namespace App\Doctrine\DBAL\Types;

abstract class CurrentPlaceType extends EnumType {
    final public const TYPE_AGREED = 'agreed';
    final public const TYPE_BILL = 'bill';
    final public const TYPE_BLOCKED = 'blocked';
    final public const TYPE_CLOSED = 'closed';
    final public const TYPE_DISABLED = 'disabled';
    final public const TYPE_DRAFT = 'draft';
    final public const TYPE_ENABLED = 'enabled';
    final public const TYPE_LITIGATION = 'litigation';
    final public const TYPE_PAID = 'paid';
    final public const TYPE_PARTIALLY_DELIVERED = 'partially_delivered';
    final public const TYPE_PARTIALLY_PAID = 'partially_paid';
    final public const TYPE_REJECTED = 'rejected';
    final public const TYPE_TO_VALIDATE = 'to_validate';
    final public const TYPE_UNDER_EXEMPTION = 'under_exemption';
    final public const TYPE_WARNING = 'warning';
}
