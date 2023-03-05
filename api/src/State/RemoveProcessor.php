<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RemoveProcessor implements ProcessorInterface {
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ValidatorInterface $validator
    ) {
    }

    /**
     * @param Entity  $data
     * @param mixed[] $uriVariables
     * @param mixed[] $context
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Entity {
        if (empty($context = $operation->getValidationContext()) === false) {
            foreach ($this->validator->validate(value: $data, groups: $context['groups']) as $violation) {
                throw new BadRequestHttpException((string) $violation->getMessage());
            }
        }
        $data->setDeleted(true);
        $this->em->flush();
        return $data;
    }
}
