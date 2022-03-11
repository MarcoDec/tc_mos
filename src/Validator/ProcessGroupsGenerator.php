<?php

namespace App\Validator;

use ApiPlatform\Core\Bridge\Symfony\Validator\ValidationGroupsGeneratorInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class ProcessGroupsGenerator implements ValidationGroupsGeneratorInterface {
    public function __construct(private readonly ResourceMetadataFactoryInterface $metadataFactory, private readonly RequestStack $stack) {
    }

    /**
     * @return string[]
     */
    public function __invoke($object): array {
        return ["{$this->getShortName()}-{$this->getProcess()}"];
    }

    private function getCurrentRequest(): Request {
        if (!empty($request = $this->stack->getCurrentRequest())) {
            return $request;
        }
        throw new InvalidArgumentException('Empty request.');
    }

    private function getProcess(): string {
        return $this->getCurrentRequest()->attributes->get('process');
    }

    private function getShortName(): string {
        if (!empty($shortName = $this->metadataFactory->create(
            $this->getCurrentRequest()->attributes->get('_api_resource_class')
        )->getShortName())) {
            return $shortName;
        }
        throw new InvalidArgumentException('ShortName not found.');
    }
}
