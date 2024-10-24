<?php

namespace App\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Jawira\CaseConverter\Convert;
use Symfony\Component\HttpFoundation\Request;

final class ContextBuilderInterface implements SerializerContextBuilderInterface {
    public function __construct(private readonly SerializerContextBuilderInterface $decorated) {
    }

    /**
     * @param mixed[] $extractedAttributes
     *
     * @return mixed[]
     */
    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        if (
            !$normalization
            && $request->isMethod(Request::METHOD_PATCH)
            && $request->attributes->has('process')
            && isset($context['item_operation_name'])
            && $context['item_operation_name'] === 'patch'
            && isset($context['resource_class'])
        ) {
            /** @var string $process */
            $process = $request->attributes->get('process');
            $exploded = explode('\\', (string) $context['resource_class']);
            $context['groups'] = [sprintf(
                "write:%s:{$process}",
                (new Convert(end($exploded)))->toKebab()
            )];
        }
        return $context;
    }
}
