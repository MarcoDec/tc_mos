<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Interfaces\CompanyInterface;
#use App\Security\SecurityTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

final class CompanyDataPersister implements ContextAwareDataPersisterInterface {
    // use SecurityTrait {
    //     __construct as private constructSecurity;
    // }

    public function __construct(private readonly EntityManagerInterface $em, Security $security) {
        $this->constructSecurity($security);
    }

    /**
     * @param CompanyInterface $data
     * @param array $context
     * @return CompanyInterface
     */
    public function persist($data, array $context = []): CompanyInterface {
        $this->em->persist($data->setCompany($this->getCompany()));
        $this->em->flush();
        return $data;
    }

    /**
     * @param CompanyInterface $data
     * @param mixed[]          $context
     */
    public function remove($data, array $context = []): void {
    }

    /**
     * @param mixed[] $context
     */
    public function supports($data, array $context = []): bool {
        return $data instanceof CompanyInterface
            && isset($context['collection_operation_name'])
            && $context['collection_operation_name'] === 'post';
    }
}
