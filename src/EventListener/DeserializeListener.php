<?php

namespace App\EventListener;

use ApiPlatform\Core\EventListener\DeserializeListener as ApiDeserializeListener;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use App\CouchDB\DocumentManager;
use App\Document\Document;
use App\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\MappingException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class DeserializeListener {
    public function __construct(
        private ApiDeserializeListener $decorated,
        private DenormalizerInterface $denormalizer,
        private DocumentManager $dm,
        private EntityManagerInterface $em,
        private SerializerContextBuilderInterface $serializer
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

        /** @var array{resource_class: class-string<Document|Entity>} $context */
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
     * @template T of \App\Document\Document|\App\Entity\Entity
     *
     * @param array{resource_class: class-string<T>} $context
     *
     * @return mixed[]
     */
    private function getData(array $context, Request $request): array {
        try {
            $metadata = $this->em->getClassMetadata($context['resource_class']);
        } catch (MappingException $e) {
            /** @var class-string<Document> $class */
            $class = $context['resource_class'];
            $metadata = $this->dm->getMetadata($class);
        }
        return collect(array_merge($request->request->all(), $request->files->all()))
            ->map(static function ($value, string $name) use ($metadata) {
                return $metadata->getTypeOfField($name) === 'boolean'
                    ? is_string($value) && $value === 'true' || $value
                    : $value;
            })
            ->all();
    }
}
