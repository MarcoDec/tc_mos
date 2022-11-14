<?php

declare(strict_types=1);

namespace App\Encoder;

use App\Collection;
use JsonException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class MultipartDecoder implements DecoderInterface {
    public function __construct(private readonly RequestStack $stack) {
    }

    /** @return array<string, mixed>|null */
    public function decode(string $data, string $format, array $context = []): ?array {
        if (empty($request = $this->stack->getCurrentRequest())) {
            return null;
        }
        /** @var array<string, string> $elements */
        $elements = $request->request->all();
        return (new Collection($elements))
            ->map(static function (string $element): mixed {
                try {
                    return json_decode($element, true, 512, JSON_THROW_ON_ERROR);
                } catch (JsonException) {
                    return $element;
                }
            })
            ->merge($request->files->all())
            ->toArray();
    }

    public function supportsDecoding(string $format): bool {
        return $format === 'multipart';
    }
}
