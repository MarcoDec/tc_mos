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
        //dump(['request' => $request->getContent(), 'method' => $request->getMethod()]);
        if (strtolower($request->getMethod()) === 'post') {
            //dump(['persist post' => $data]);
            //On récupère le groupe d'équivalence et on le persiste un première fois pour récupérer son id
            $this->em->persist($data);
            $this->em->flush();
        }
        //On regénère le code de l'équivalence si besoin (ex en cas de changement de famille)
        $data->generateCode();
        $content = json_decode($request->getContent());
        //dump(['content' => $content]);
        if (isset($content->components)) {
            //On ajoute les composants au groupe d'équivalence
            $components = json_decode($request->getContent())->components;
            foreach ($components as $componentIri) {
                $ids=[];
                preg_match('/(\d+)/', $componentIri, $ids, PREG_OFFSET_CAPTURE);
                if (count($ids)>0) {
                    $component = $this->em->getRepository(Component::class)->find($ids[0][0]);
                    $componentEquivalentComponent = new ComponentEquivalentItem();
                    $componentEquivalentComponent->setComponent($component);
                    $componentEquivalentComponent->setComponentEquivalent($data);
                    $this->em->persist($componentEquivalentComponent);
                }
            }
        }

        //On re-persiste le groupe d'équivalence
        $this->em->flush();
        return $data;
    }

    public function remove($data, array $context = [])
    {
    }
}