<?php

namespace App\DataPersister\Purchase\Component;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Purchase\Component\ComponentAttribute;
use Doctrine\ORM\EntityManagerInterface;

class ComponentAttributePersister implements ContextAwareDataPersisterInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof ComponentAttribute
            && isset($context['collection_operation_name'])
            && $context['collection_operation_name'] === 'post';
    }

    /**
     * @param $data
     * @param array $context
     * @return void
     */
    public function persist($data, array $context = [])
    {
        //Ici on gère le cas d'un ajout d'un élément qui existe déjà en base mais avec deleted = true (donc non visible par l'utilisateur)
        //L'idée est de simplement faire un UPDATE au lieu d'un INSERT
        $repository = $this->em->getRepository(ComponentAttribute::class);
        $res = $repository->findBy([
            'attribute' => $data->getAttribute(),
            'component' => $data->getComponent()
        ]);
        if (count($res)>0) {
            foreach ($res as $componentAttribute) {
                $componentAttribute->setDeleted(false);
                $componentAttribute->setValue($data->getValue());
                $componentAttribute->setColor($data->getColor());
                $componentAttribute->setMeasure($data->getMeasure());
                return $componentAttribute;
            }
        } else {
            $this->em->persist($data);
            $this->em->flush();
        }
    }
    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
    }
}