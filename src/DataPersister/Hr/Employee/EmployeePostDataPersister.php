<?php

namespace App\DataPersister\Hr\Employee;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\DataPersister\FileDataPersister;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Hr\Employee\Employee;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Exception;
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

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    private function getNextTimeCard()
    {
        $conn = $this->em->getConnection();

        $sql = 'SELECT get_next_timecard() as nextTimeCard';
        /** @var Statement $stmt */
        $stmt = $conn->prepare($sql);
        $result=$stmt->execute();

        // Récupère le résultat
        $results = $result->fetchAssociative();
        return $results['nextTimeCard'];
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
     * @throws Exception
     */
    public function persist($data, array $context = []): Employee
    {
        $content = json_decode($this->requests->getCurrentRequest()->getContent());
        if (isset($content->embRoles)) {
            $roles = array_merge([Roles::ROLE_USER],$content->embRoles);
            $data->setRoles($roles);
        }
        $data->setTimeCard($this->getNextTimeCard());
        /** @var Employee $data */
        if ($data->getPlainPassword() != "") {
            $hashedPassword = $this->passwordHasher->hashPassword($data, $data->getPlainPassword());
            $data->setPassword($hashedPassword);
            $this->logger->info(`Création du mot de passe de l'utilisateur `.$data->getId());
        }
        // On récupère le niveau de l'employé parmis les rôles suivants:
        // ROLE_LEVEL_OPERATOR, ROLE_LEVEL_ANIMATOR, ROLE_LEVEL_MANAGER, ROLE_LEVEL_DIRECTOR

        // Si le manager est défini, on récupère ses informations
        if ($content->manager != null) {
            // On récupère l'id du manager à partir de l'iri qui correspond au nombre terminant l'iri
            $idManager = explode('/', $content->manager)[count(explode('/', $content->manager)) - 1];
            $manager = $this->em->getRepository(Employee::class)->findOneBy(['id' => $idManager]);
            $data->setManager($manager);
            // On récupère les rôles du manager
            $managerRoles = $manager->getRoles();
            // Pour chaque rôle READER du manager on l'ajoute à l'employé en tant que READER
            $roles = $data->getRoles();
            $employeeLevel = null;
            if (in_array(Roles::ROLE_LEVEL_OPERATOR, $roles)) {
                $employeeLevel = Roles::ROLE_LEVEL_OPERATOR;
            } elseif (in_array(Roles::ROLE_LEVEL_ANIMATOR, $roles)) {
                $employeeLevel = Roles::ROLE_LEVEL_ANIMATOR;
            } elseif (in_array(Roles::ROLE_LEVEL_MANAGER, $roles)) {
                $employeeLevel = Roles::ROLE_LEVEL_MANAGER;
            } elseif (in_array(Roles::ROLE_LEVEL_DIRECTOR, $roles)) {
                $employeeLevel = Roles::ROLE_LEVEL_DIRECTOR;
            }
            foreach ($managerRoles as $role) {
                if (strpos($role, '_READER') !== false 
                    && in_array($employeeLevel, [Roles::ROLE_LEVEL_ANIMATOR, Roles::ROLE_LEVEL_MANAGER, Roles::ROLE_LEVEL_DIRECTOR] )) {
                    $roles[] = $role;
                }
                if (strpos($role, '_WRITER') !== false) {
                    if (in_array($employeeLevel, [Roles::ROLE_LEVEL_ANIMATOR, Roles::ROLE_LEVEL_MANAGER, Roles::ROLE_LEVEL_DIRECTOR] )) {
                        $roles[] = $role;
                    } else {
                        $roles[] = str_replace('_WRITER', '_READER', $role);
                    }
                }
                if (strpos($role, '_ADMIN') !== false) {
                    if (in_array($employeeLevel, [Roles::ROLE_LEVEL_MANAGER, Roles::ROLE_LEVEL_DIRECTOR] )) {
                        $roles[] = $role;
                    } else {
                        if (in_array($employeeLevel, [Roles::ROLE_LEVEL_ANIMATOR])) {
                            $roles[] = str_replace('_ADMIN', '_WRITER', $role);
                        } else {
                            $roles[] = str_replace('_ADMIN', '_READER', $role);
                        }
                    }
                }
            }
            $data->setRoles($roles);
        }
        $this->em->persist($data);
        $this->em->flush();
        return $data;
    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
    }
}