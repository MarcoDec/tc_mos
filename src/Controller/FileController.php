<?php

namespace App\Controller;

use ApiPlatform\Core\Exception\InvalidValueException;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use App\Entity\Interfaces\FileEntity;
use App\Entity\Project\Product\Family;
use App\Filesystem\FileManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class FileController {
    public function __construct(
        private ResourceMetadataFactoryInterface $apiMetadatas,
        private DenormalizerInterface $denormalizer,
        private FileManager $fm
    ) {
    }

    public function __invoke(Request $request): FileEntity {
        $entity = $request->attributes->get('data');
        if (!$entity instanceof FileEntity) {
            throw new InvalidValueException(sprintf('Expected entity implements %s, get %s.', FileEntity::class, get_class($entity)));
        }
        $context = $request->attributes->get('_api_normalization_context');
        $metadata = $this->apiMetadatas->create($request->attributes->get('_api_resource_class'));
        $denormalizationContext = $metadata->getAttribute('denormalization_context');
        $context['groups'] = $denormalizationContext['groups'];
        $context['openapi_definition_name'] = $denormalizationContext['openapi_definition_name'];
        $context['has_identifier_converter'] = true;
        $data = $request->request->all();
        $data['id'] = $context['request_uri'];
        /** @var FileEntity $entity */
        $entity = $this->denormalizer->denormalize(
            data: $data,
            type: $request->attributes->get('_api_resource_class'),
            context: $context
        );
        $entity->setFile($request->files->get('file'));
        $request->attributes->set('data', $entity);
        if ($entity instanceof Family) {
            $this->fm->uploadFamilyIcon($entity, $metadata);
        }
        return $entity;
    }
}
