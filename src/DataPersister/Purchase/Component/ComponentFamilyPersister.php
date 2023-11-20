<?php

namespace App\DataPersister\Purchase\Component;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Purchase\Component\Family as ComponentFamily;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class ComponentFamilyPersister implements DataPersisterInterface {
    public function __construct(private readonly EntityManagerInterface $em, private readonly RequestStack $requests) {
    }

    public function persist($data): void {
    }

    /**
     * @param ComponentFamily $data
     */
    public function remove($data): void {
        if ($data->hasComponents()) {
            throw new UnprocessableEntityHttpException('Cette famille est associée à des composants. Supprimez les composants ou changez leur famille pour réaliser cette action.');
        }
        $this->em->remove($data);
        $this->em->flush();
    }

    public function supports($data): bool {
        $request = $this->requests->getCurrentRequest();
        return !empty($request)
            && $request->isMethod(Request::METHOD_DELETE)
            && $data instanceof ComponentFamily;
    }
}
