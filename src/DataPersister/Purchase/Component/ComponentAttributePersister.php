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
        dump([
            'collection_operation_name' => isset($context['collection_operation_name'])?$context['collection_operation_name']:null,
            'item_operation_name' => isset($context['item_operation_name'])?$context['item_operation_name']:null,
            'data instanceof ComponentAttribute' => $data instanceof ComponentAttribute,
            'isset($context[collection_operation_name]) &&$context[collection_operation_name]===post'
            => isset($context['collection_operation_name']) && $context['collection_operation_name'] === 'post',
            'isset($context[item_operation_name]) && $context[item_operation_name]===patch'
            => isset($context['item_operation_name']) && $context['item_operation_name'] === 'patch'
        ]);
        return $data instanceof ComponentAttribute
            && (
            (isset($context['collection_operation_name'])
            && $context['collection_operation_name'] === 'post')
            || (isset($context['item_operation_name'])
            && $context['item_operation_name'] === 'patch')
            );
    }

    /**
     * @param $data
     * @param array $context
     * @return void
     */
    public function persist($data, array $context = [])
    {
        dump('persist');
        dump(['data'=>$data, 'context'=>$context]);
        if (isset($context['collection_operation_name'])) {
            //region Ici on gère le cas d'un ajout d'un élément qui existe déjà en base mais avec deleted = true (donc non visible par l'utilisateur)
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
                }
            } else {
                $this->em->persist($data);
                $this->em->flush();
            }
            //endregion
        } else {
            $this->persistMeasureField($data, $context);
        }

    }

    function persistMeasureField($data, array $context = []): void
    {
        //region on gère ici le persist d'un CcomponentAttribute avec une Measure
        dump('persistMeasureField');
        dump(['data'=>$data, 'context'=>$context]);
        $repository = $this->em->getRepository(ComponentAttribute::class);
        $res = $repository->findBy([
            'attribute' => $data->getAttribute(),
            'component' => $data->getComponent()
        ]);
        if (count($res)>0) {
            dump('persistMeasure données en base trouvées');
            /** @var ComponentAttribute $componentAttribute */
            $componentAttribute = $res[0];
            $componentAttribute->setDeleted(false);
            $componentAttribute->setValue($data->getValue());
            $componentAttribute->setColor($data->getColor());
            $componentAttribute->setMeasure($data->getMeasure());
            $this->em->persist($componentAttribute);
            $this->em->flush();
            dump(['componentAttribute'=>$componentAttribute]);
        } else {
            dump('persistMeasure pas de données en base');
            $this->em->persist($data);
            $this->em->flush();
        }
        //endregion
    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
    }
}