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
        $this->em->beginTransaction();
        $this->em
            ->createQueryBuilder()
            ->update($operation->getClass(), 'd')
            ->set('d.deleted', true)
            ->where('d.id = :id')
            ->setParameter('id', $data->getId())
            ->getQuery()
            ->execute();
        $this->em->commit();
        return $data;
    }
}
