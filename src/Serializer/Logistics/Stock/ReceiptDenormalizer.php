<?php

namespace App\Serializer\Logistics\Stock;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Embeddable\Purchase\Order\Item\State;
use App\Entity\Logistics\Stock\ComponentStock;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Order\Item;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Workflow\Registry;

final class ReceiptDenormalizer implements DenormalizerAwareInterface, DenormalizerInterface {
    use DenormalizerAwareTrait;

    public function __construct(private readonly IriConverterInterface $iriConverter, private readonly Registry $registry) {
    }

    /**
     * @param array{location: string, orderItem: string, quantity: array{code: string, value: float}, warehouse: string} $data
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []) {
        /** @var ComponentStock $stock */
        $stock = $this->denormalizer->denormalize(
            data: [
                'location' => $data['location'],
                'quantity' => $data['quantity'],
                'warehouse' => $data['warehouse']
            ],
            type: $type,
            format: $format,
            context: $context
        );
        /** @var Item<Component> $item */
        $item = $this->iriConverter->getItemFromIri($data['orderItem'], $context);
        $stock->setOrderItem($item);
        $workflow = $this->registry->get($item, 'purchase_order_item');
        if ($workflow->can($item, State::TR_DELIVER)) {
            $workflow->apply($item, State::TR_DELIVER);
        } elseif ($workflow->can($item, State::TR_PARTIALLY_DELIVER)) {
            $workflow->apply($item, State::TR_PARTIALLY_DELIVER);
        }
        return $stock;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool {
        return is_array($data) && isset($data['orderItem']) && $type === ComponentStock::class;
    }
}
