<?php

namespace App\DataPersister\Hr\Employee;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Hr\Employee\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface AS Logger;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use function PHPUnit\Framework\isEmpty;

class EmployeePostDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private Logger $logger,
        private EntityManagerInterface $em,
        private RequestStack $requests
    ) {

    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Employee
            && isset($context['collection_operation_name'])
            && $context['collection_operation_name'] === 'post';
    }

    /**
     * @param Employee $data
     * @param array $context
     * @return Employee
     */
    public function persist($data, array $context = [])
    {
        dump("Persist New Employee", $this->requests->getCurrentRequest());
        $content = json_decode($this->requests->getCurrentRequest()->getContent());
        if (isset($content->embRoles)) {
            $roles = array_merge([Roles::ROLE_USER],$content->embRoles);
            dump($roles);
            $data->setRoles($roles);
        }
        /** @var Employee $data */
        if ($data->getPlainPassword() != "") {
            $hashedPassword = $this->passwordHasher->hashPassword($data, $data->getPlainPassword());
            //dump($hashedPassword);
            $data->setPassword($hashedPassword);
            $this->em->persist($data);
            $this->em->flush();
            dump($data);
            $this->logger->info(`Création du mot de passe de l'utilisateur `.$data->getId());
        }
        return $data;
    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
    }
}