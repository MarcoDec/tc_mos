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
    final public const TR_CREATE = 'create';
    final public const TR_DELAY = 'delay';
    final public const TR_DELIVER = 'deliver';
    final public const TR_DISABLE = 'disable';
    final public const TR_FORECAST = 'forecast';
    final public const TR_MONTH = 'month';
    final public const TR_PARTIALLY_DELIVER = 'partially_deliver';
    final public const TR_PARTIALLY_PAY = 'partially_pay';
    final public const TR_PAY = 'pay';
    final public const TR_REJECT = 'reject';
    final public const TR_SUBMIT_VALIDATION = 'submit_validation';
    final public const TR_SUPERVISE = 'supervise';
    final public const TR_UNLOCK = 'unlock';
    final public const TR_VALIDATE = 'validate';

    /** @var array<string, 1> */
    protected array $state = [];

    /**
     * @return array<string, 1>
     */
    final public function getState(): array {
        return $this->state;
    }

    /**
     * @param array<string, 1> $state
     */
    final public function setState(array $state): self {
        $this->state = $state;
        return $this;
    }
}
