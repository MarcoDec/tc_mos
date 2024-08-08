<?php

namespace App\Doctrine\DBAL\Types\Embeddable;

use App\Doctrine\DBAL\Types\EnumType;

abstract class StateType extends EnumType {
    final public const TYPE_STATE_AGREED = 'agreed';
    final public const TYPE_STATE_ACKNOWLEDGMENT_OF_RECEIPT ='acknowledgment_of_receipt';
    final public const TYPE_STATE_ASKED = 'asked';
    final public const TYPE_STATE_BILLED = 'billed';
    final public const TYPE_STATE_BLOCKED = 'blocked';
    final public const TYPE_STATE_CART = 'cart';
    final public const TYPE_STATE_CLOSED = 'closed';
    final public const TYPE_STATE_LOCKED = 'locked';
    final public const TYPE_STATE_DELAYED = 'delayed';
    final public const TYPE_STATE_DELIVERED = 'delivered';
    final public const TYPE_STATE_DISABLED = 'disabled';
    final public const TYPE_STATE_DRAFT = 'draft';
    final public const TYPE_STATE_ENABLED = 'enabled';
    final public const TYPE_STATE_FORECAST = 'forecast';
    final public const TYPE_STATE_INITIAL = 'initial';
    final public const TYPE_STATE_MONTHLY = 'monthly';
    final public const TYPE_STATE_PAID = 'paid';
    final public const TYPE_STATE_PARTIALLY_DELIVERED = 'partially_delivered';
    final public const TYPE_STATE_PARTIALLY_PAID = 'partially_paid';
    final public const TYPE_STATE_PARTIALLY_RECEIVED = 'partially_received';
    final public const TYPE_STATE_TO_VALIDATE = 'to_validate';
    final public const TYPE_STATE_READY_TO_SEND = 'ready_to_send';
    final public const TYPE_STATE_READY_TO_WORK = 'ready_to_work';
    final public const TYPE_STATE_REJECTED = 'rejected';
    final public const TYPE_STATE_RECEIVED = 'received';
    final public const TYPE_STATE_SENT = 'sent';
    final public const TYPE_STATE_WARNING = 'warning';
    final public const TYPE_STATE_UNDER_WAIVER = 'under_waiver';
    final public const TYPE_STATE_UNDER_MAINTENANCE = 'under_maintenance';

}
