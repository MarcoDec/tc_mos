<?php

namespace App\Doctrine\DBAL\Types\Embeddable;

use App\Doctrine\DBAL\Types\SetType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Illuminate\Support\Collection;

abstract class StateType extends SetType {
    final public const TYPE_STATE_AGREED = 'agreed';
    final public const TYPE_STATE_ASKED = 'asked';
    final public const TYPE_STATE_BILLED = 'billed';
    final public const TYPE_STATE_BLOCKED = 'blocked';
    final public const TYPE_STATE_CART = 'cart';
    final public const TYPE_STATE_CLOSED = 'closed';
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
    final public const TYPE_STATE_REJECTED = 'rejected';
    final public const TYPE_STATE_TO_VALIDATE = 'to_validate';
    final public const TYPE_STATE_WARNING = 'warning';

    /**
     * @param array<string, 1> $value
     */
    final public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string {
        return parent::convertToDatabaseValue(collect($value)->keys()->values()->all(), $platform);
    }

    /**
     * @param string $value
     *
     * @return array<string, 1>
     */
    final public function convertToPHPValue($value, AbstractPlatform $platform): array {
        /** @var string[] $converted */
        $converted = parent::convertToPHPValue($value, $platform);
        /** @var Collection<string, 1> $states */
        $states = collect($converted)->mapWithKeys(static fn (string $state): array => [$state => 1]);
        return $states->all();
    }
}
