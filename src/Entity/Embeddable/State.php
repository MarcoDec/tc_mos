<?php

namespace App\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
abstract class State {
    final public const TR_ACCEPT = 'accept';
    final public const TR_BILL = 'bill';
    final public const TR_BLOCK = 'block';
    final public const TR_BUY = 'buy';
    final public const TR_CLOSE = 'close';
    final public const TR_LOCK = 'lock';
    final public const TR_CREATE = 'create';
    final public const TR_DELAY = 'delay';
    final public const TR_DELIVER = 'deliver';
    final public const TR_DISABLE = 'disable';
    final public const TR_FORECAST = 'forecast';
    final public const TR_MONTH = 'month';
    final public const TR_PARTIALLY_DELIVER = 'partially_deliver';
    final public const TR_PARTIALLY_PAY = 'partially_pay';
    final public const TR_PARTIALLY_RECEIVE = 'partially_receive';
    final public const TR_PAY = 'pay';
    final public const TR_RECEIVE = 'receive';
    final public const TR_REJECT = 'reject';
    final public const TR_SUBMIT_VALIDATION = 'submit_validation';
    final public const TR_SUPERVISE = 'supervise';
    final public const TR_SUBROGATE = 'subrogate';
    final public const TR_UNDER_MAINTENANCE = 'under_maintenance';
    final public const TR_UNLOCK = 'unlock';
    final public const TR_VALIDATE = 'validate';

    protected string $state;

    final public function getState(): string {
        return $this->state;
    }

    final public function setState(string $state): self {
        $this->state = $state;
        return $this;
    }
}