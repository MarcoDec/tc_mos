<?php
namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
/**
 * Class CompositeDataPersister
 * @package App\DataPersister
 * Cette classe permet de gérer plusieurs DataPersisters en même temps, il a été initialisé pour gérer les DataPersisters de 
 *  - FileDataPersister 
 *  - et EmployeePostDataPersister 
 * tous 2 attachés à la classe Employee
 */
class CompositeDataPersister implements ContextAwareDataPersisterInterface
{
    private array $dataPersisters;

    public function __construct(iterable $dataPersisters)
    {
        $this->dataPersisters = $dataPersisters;
    }

    public function supports($data, array $context = []): bool
    {
        foreach ($this->dataPersisters as $dataPersister) {
            if ($dataPersister->supports($data, $context)) {
                return true;
            }
        }

        return false;
    }

    public function persist($data, array $context = [])
    {
        foreach ($this->dataPersisters as $dataPersister) {
            if ($dataPersister->supports($data, $context)) {
                $dataPersister->persist($data, $context);
            }
        }
    }

    public function remove($data, array $context = [])
    {
        foreach ($this->dataPersisters as $dataPersister) {
            if ($dataPersister->supports($data, $context)) {
                $dataPersister->remove($data, $context);
            }
        }
    }
}
