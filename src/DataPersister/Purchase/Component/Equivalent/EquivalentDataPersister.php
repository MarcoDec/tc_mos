<?php

namespace App\DataPersister\Purchase\Component\Equivalent;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Component\Equivalent\ComponentEquivalent;
use App\Entity\Purchase\Component\Equivalent\ComponentEquivalentItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class EquivalentDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em, private readonly RequestStack $requests
    ) {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof ComponentEquivalent
            && (
                (isset($context['collection_operation_name']) && $context['collection_operation_name'] === 'post')
                || (isset($context['item_operation_name']) && $context['item_operation_name'] === 'patch')
            );
    }

    public function persist($data, array $context = [])
    {
        /** @var Request $request */
        $request = $this->requests->getCurrentRequest();
        if (strtolower($request->getMethod()) === 'post') {
            //On récupère le groupe d'équivalence et on le persiste un première fois pour récupérer son id
            $this->em->persist($data);
            $this->em->flush();
        }
        //On regénère le code de l'équivalence si besoin (ex en cas de changement de famille)
        $data->generateCode();
        $content = json_decode($request->getContent());
        if (isset($content->components)) {
            //On ajoute les composants au groupe d'équivalence
            $components = json_decode($request->getContent())->components;
            $componentsIds = [];
            //On récupère les id des composants à avoir dans le groupe d'équivalence
            foreach ($components as $key => $componentIri) {
                $id=[];
                preg_match('/(\d+)/', $componentIri, $id, PREG_OFFSET_CAPTURE);
                if (count($id)>0) {
                    $componentsIds[$key] = $id[0][0];
                }
            }
            $data->getItems()->map(function (ComponentEquivalentItem $componentEquivalentItem) {
                $this->em->remove($componentEquivalentItem);
            });
            $this->em->flush();
            //On ajoute les composants présents dans la requête et non présents dans le groupe d'équivalence
            foreach ($componentsIds as $id) {
                $component = $this->em->getRepository(Component::class)->find($id);
                $componentEquivalentComponent = new ComponentEquivalentItem();
                $componentEquivalentComponent->setComponent($component);
                $componentEquivalentComponent->setComponentEquivalent($data);
                $this->em->persist($componentEquivalentComponent);
            }
        }
        //On re-persiste le groupe d'équivalence
        $this->em->flush();
        return $this->em->getRepository(ComponentEquivalent::class)->find($data->getId());
    }

    public function remove($data, array $context = [])
    {
    }
}