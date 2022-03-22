<?php

namespace App\Validator;

use ApiPlatform\Core\Bridge\Symfony\Validator\ValidationGroupsGeneratorInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ProcessGroupsGenerator implements ValidationGroupsGeneratorInterface {
    public function __construct(private readonly ResourceMetadataFactoryInterface $metadataFactory, private readonly RequestStack $stack) {
    }

    /**
     * @return string[]
     */
    public function __invoke($object): array {
        return ["{$this->getShortName()}-{$this->getProcess()}"];
    }

    private function getAttribute(string $key): string {
        $attribute = $this->getCurrentRequest()->attributes->get($key);
        if (is_string($attribute)) {
            return $attribute;
        }
        throw new UnexpectedValueException($attribute, 'string');
    }

    private function getCurrentRequest(): Request {
        if (!empty($request = $this->stack->getCurrentRequest())) {
            return $request;
        }
        throw new InvalidArgumentException('Empty request.');
    }

    private function getProcess(): string {
        return $this->getAttribute('process');
    }

    private function getShortName(): string {
        if (!empty($shortName = $this->metadataFactory->create($this->getAttribute('_api_resource_class'))->getShortName())) {
            return $shortName;
        }
        throw new InvalidArgumentException('ShortName not found.');
    }
}
