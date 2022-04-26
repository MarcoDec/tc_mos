<?php

namespace App\EventListener;

use ApiPlatform\Core\EventListener\DeserializeListener as ApiDeserializeListener;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class DeserializeListener {
    public function __construct(
        private readonly ApiDeserializeListener $decorated,
        private readonly DenormalizerInterface $denormalizer,
        private readonly EntityManagerInterface $em,
        private readonly SerializerContextBuilderInterface $serializer
    ) {
    }

    public function __invoke(RequestEvent $event): void {
        $request = $event->getRequest();
        if ($request->isMethodCacheable() || $request->isMethod(Request::METHOD_DELETE)) {
            return;
        }

        if (!in_array($request->getContentType(), ['form', 'multipart'])) {
            $this->decorated->onKernelRequest($event);
        }

        $this->denormalizeMultipart($request);
    }

    private function denormalizeMultipart(Request $request): void {
        if (empty($attrs = RequestAttributesExtractor::extractAttributes($request))) {
            return;
        }
        /** @var array{resource_class: string} $context */
        $context = $this->serializer->createFromRequest($request, false, $attrs);
        if (!empty($populated = $request->attributes->get('data'))) {
            $context['object_to_populate'] = $populated;
        }

        $request->attributes->set('data', $this->denormalizer->denormalize(
            data: $this->getData($context, $request),
            type: $attrs['resource_class'],
            context: $context
        ));
    }

    /**
     * @param array{resource_class: string} $context
     *
     * @return mixed[]
     */
    private function getData(array $context, Request $request): array {
        $metadata = $this->em->getClassMetadata($context['resource_class']);
        return collect(array_merge($request->request->all(), $request->files->all()))
            ->map(static fn ($value, string $name) => $metadata->getTypeOfField($name) === 'boolean'
                ? is_string($value) && $value === 'true' || $value
                : $value)
            ->all();
    }
}
