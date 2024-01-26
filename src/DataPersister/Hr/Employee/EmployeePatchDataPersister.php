<?php

namespace App\DataPersister\Hr\Employee;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Hr\Employee\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface AS Logger;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use function PHPUnit\Framework\isEmpty;

class EmployeePatchDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher, private Logger $logger, private EntityManagerInterface $em) {

    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Employee
            && isset($context['item_operation_name'])
            && $context['item_operation_name'] === 'patch';
    }

    public function persist($data, array $context = [])
    {
        dump("Persist Employee", $data);
        /** @var Employee $data */
        if ($data->getPlainPassword() != "") {
            $hashedPassword = $this->passwordHasher->hashPassword($data, $data->getPlainPassword());
            dump($hashedPassword);
            $data->setPassword($hashedPassword);
            $this->logger->info(`Changement du mot de passe de l'utilisateur `.$data->getId());
        }
        $this->em->flush();
    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
    }
}